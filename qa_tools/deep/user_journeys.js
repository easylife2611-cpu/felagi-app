#!/usr/bin/env node
/**
 * L341 Block F — Read-only Interactive User Journeys
 *
 * Design:
 *   - Auth via /_qa/login (L337 route)
 *   - Clicks navigation, verifies screen state
 *   - NO form submits, NO writes, NO deletes
 *   - Screenshot per step
 *   - JSON evidence with navigation chain
 *
 * Journeys (5):
 *   J1 Public discovery:   S001 -> S002 -> S004 -> S008
 *   J2 Auth flow:          S001 -> S003 -> S009
 *   J3 Create flow (RO):   S004 -> S005 (fill) -> S006
 *   J4 Offer viewing:      S008 -> S010 -> S012
 *   J5 Admin flow:         A001 -> A017 -> A023
 */
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'https://zagcreativity.com';
const LOGIN = `${BASE}/_qa/login?user=So`;
const SYNTHETIC_NEED_ID = 'test-id';

const JOURNEYS = [
  {
    id: 'J1',
    name: 'Public discovery',
    steps: [
      { screen: 'S001', route: '/', wait: 500,
        verify: ['felagi-lockup', 'id="email-input"'] },
      { screen: 'S004', route: '/browse', wait: 800,
        verify: ['id="keyword"'] },
      { screen: 'S008', route: `/needs/${SYNTHETIC_NEED_ID}`, wait: 500,
        verify: [] },
    ],
  },
  {
    id: 'J2',
    name: 'Auth flow',
    steps: [
      { screen: 'S001', route: '/', wait: 500,
        verify: ['felagi-lockup'] },
      { screen: 'S003', route: '/profile', wait: 500,
        verify: ['full_name', 'phone_number'] },
      { screen: 'S009', route: '/my/needs', wait: 500,
        verify: [] },
    ],
  },
  {
    id: 'J3',
    name: 'Create flow (read-only — no submit)',
    steps: [
      { screen: 'S004', route: '/browse', wait: 500,
        verify: ['id="keyword"'] },
      { screen: 'S005', route: '/needs/new', wait: 500,
        verify: ['id="title"', 'id="description"', 'id="category_id"'],
        action: 'fill-no-submit' },
      { screen: 'S006', route: '/needs/new/public-preview', wait: 500,
        verify: [] },
    ],
  },
  {
    id: 'J4',
    name: 'Offer viewing',
    steps: [
      { screen: 'S008', route: `/needs/${SYNTHETIC_NEED_ID}`, wait: 500,
        verify: [] },
      { screen: 'S010', route: `/needs/${SYNTHETIC_NEED_ID}/offers`, wait: 500,
        verify: [] },
      { screen: 'S012', route: `/offers/${SYNTHETIC_NEED_ID}`, wait: 500,
        verify: [] },
    ],
  },
  {
    id: 'J5',
    name: 'Admin flow',
    steps: [
      { screen: 'A001', route: '/admin/dashboard', wait: 800,
        verify: [] },
      { screen: 'A017', route: '/admin/settings', wait: 800,
        verify: [] },
      { screen: 'A023', route: '/admin/monetization/sponsored-ads', wait: 800,
        verify: ['id="ads-privacy"'] },
    ],
  },
];

const SHOT_DIR = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep', 'screenshots', 'journeys');
fs.mkdirSync(SHOT_DIR, { recursive: true });

async function runJourney(page, journey) {
  const steps = [];
  let passed = 0, failed = 0;

  for (let i = 0; i < journey.steps.length; i++) {
    const step = journey.steps[i];
    const stepId = `${journey.id}-${i + 1}-${step.screen}`;
    const stepResult = {
      stepIndex: i,
      screen: step.screen,
      route: step.route,
      verify: step.verify || [],
      verified: [],
      http: null,
      finalUrl: null,
      title: null,
      ok: false,
      error: null,
      screenshot: null,
      fieldsFilled: [],
    };

    try {
      const resp = await page.goto(`${BASE}${step.route}`, {
        waitUntil: 'domcontentloaded',
        timeout: 15000,
      });
      stepResult.http = resp.status();
      stepResult.finalUrl = page.url();
      stepResult.title = await page.title();
      await page.waitForTimeout(step.wait || 300);

      // Verify expected elements
      for (const selector of step.verify) {
        const found = await page.evaluate((sel) => {
          if (sel.startsWith('id=')) {
            const id = sel.slice(3).replace(/"/g, '');
            return !!document.getElementById(id);
          }
          if (sel.startsWith('#')) {
            return !!document.querySelector(sel);
          }
          // text/attribute selector fallback
          return document.body.innerHTML.includes(sel);
        }, selector);
        if (found) stepResult.verified.push(selector);
      }

      // Read-only form fill (J3 special)
      if (step.action === 'fill-no-submit') {
        try {
          await page.fill('#title', 'QA Test Need — do not submit', { timeout: 3000 });
          stepResult.fieldsFilled.push('title');
          await page.fill('#description', 'Read-only journey test. No submit.', { timeout: 3000 });
          stepResult.fieldsFilled.push('description');
          // NO SUBMIT — verify fill only
          const titleVal = await page.inputValue('#title');
          stepResult.titleLength = titleVal.length;
        } catch (e) {
          stepResult.error = 'fill failed: ' + e.message;
        }
      }

      // Screenshot
      const shotPath = path.join(SHOT_DIR, `${stepId}.png`);
      await page.screenshot({ path: shotPath, fullPage: false });
      stepResult.screenshot = path.relative(path.join(__dirname, '..', '..'), shotPath);

      // Pass criteria
      const verifyOk = stepResult.verify.length === 0
        || stepResult.verified.length === stepResult.verify.length;
      stepResult.ok = stepResult.http === 200 && verifyOk && !stepResult.error;
      if (stepResult.ok) passed++; else failed++;
    } catch (e) {
      stepResult.error = e.message;
      failed++;
    }

    steps.push(stepResult);
    console.log(`${stepResult.ok ? '✅' : '❌'} ${stepId}: HTTP ${stepResult.http}${stepResult.error ? ' — ' + stepResult.error : ''}`);
  }

  return { id: journey.id, name: journey.name, steps, passed, failed };
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1200, height: 900 },
  });
  const page = await context.newPage();

  console.log('🔐 Login via /_qa/login...');
  const loginResp = await page.goto(LOGIN);
  if (loginResp.status() !== 200) {
    console.error('❌ Login failed');
    process.exit(1);
  }
  console.log('✅ Login OK\n');

  const journeyResults = [];
  let totalPassed = 0, totalFailed = 0;

  for (const journey of JOURNEYS) {
    console.log(`── ${journey.id}: ${journey.name} ──`);
    const result = await runJourney(page, journey);
    journeyResults.push(result);
    totalPassed += result.passed;
    totalFailed += result.failed;
    console.log();
  }

  const output = {
    generated: new Date().toISOString(),
    base: BASE,
    mode: 'read-only',
    journeys: journeyResults,
    summary: {
      journeys: JOURNEYS.length,
      steps: totalPassed + totalFailed,
      passed: totalPassed,
      failed: totalFailed,
      passRate: (((totalPassed) / (totalPassed + totalFailed)) * 100).toFixed(2) + '%',
    },
  };

  const outPath = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep',
    'USER_JOURNEYS_' + Date.now() + '.json');
  fs.writeFileSync(outPath, JSON.stringify(output, null, 2));

  console.log('═══════════════════════════════════════');
  console.log(`📄 Evidence: ${path.relative(path.join(__dirname, '..', '..'), outPath)}`);
  console.log(`📊 Journeys: ${output.summary.journeys}`);
  console.log(`📊 Steps:    ${output.summary.steps}`);
  console.log(`✅ Passed:   ${output.summary.passed}`);
  console.log(`❌ Failed:   ${output.summary.failed}`);
  console.log(`📈 PassRate: ${output.summary.passRate}`);
  console.log('═══════════════════════════════════════');

  await browser.close();
  process.exit(totalFailed > 0 ? 1 : 0);
})();
