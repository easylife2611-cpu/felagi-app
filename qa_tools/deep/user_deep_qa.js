#!/usr/bin/env node
/**
 * User Deep QA — 23 screens × 6 viewports + screenshots + overflow
 * Uses /_qa/login (L337 fix) for authenticated session.
 */
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'https://zagcreativity.com';
const LOGIN = `${BASE}/_qa/login?user=So`;

const USER_SCREENS = [
  ['S001', '/',                              'Welcome'],
  ['S002', '/auth/telegram',                 'Telegram sign-in'],
  ['S003', '/profile',                       'Profile'],
  ['S004', '/browse',                        'Browse Needs'],
  ['S005', '/needs/new',                     'Create Need'],
  ['S006', '/needs/new/public-preview',      'Public Preview'],
  ['S007', '/needs/test-id/created',         'Need-Created'],
  ['S008', '/needs/test-id',                 'Need Details'],
  ['S009', '/my/needs',                      'My Needs'],
  ['S010', '/needs/test-id/offers',          'Received Offers'],
  ['S011', '/needs/test-id/offers/new',      'Submit Offer'],
  ['S012', '/offers/test-id',                'Offer Details'],
  ['S013', '/my/offers',                     'My Offers'],
  ['S014', '/needs/test-id/compare',         'Compare Confirmation'],
  ['S015', '/comparisons/test-id',           'AI Comparison Result'],
  ['S016', '/needs/test-id/comparisons',     'Comparison History'],
  ['S017', '/offers/test-id/messages',       'Messages'],
  ['S018', '/notifications',                 'Notifications'],
  ['S019', '/needs/test-id/boost',           'Boost/Payments'],
  ['S020', '/needs/test-id/rating',          'Rating'],
  ['S021', '/support/report',                'Report/Support'],
  ['S022', '/needs/test-id/publications',    'Telegram Publications'],
  ['S023', '/needs/test-id/offers/unlock',   'Offer Submission Unlock'],
];

const VIEWPORTS = [
  { name: 'mobile-320',   width: 320,  height: 568 },
  { name: 'mobile-360',   width: 360,  height: 640 },
  { name: 'tablet-600',   width: 600,  height: 800 },
  { name: 'tablet-840',   width: 840,  height: 1000 },
  { name: 'desktop-1200', width: 1200, height: 900 },
  { name: 'desktop-1600', width: 1600, height: 900 },
];

const SHOT_DIR = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep', 'screenshots', 'user');
fs.mkdirSync(SHOT_DIR, { recursive: true });

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  const errors = [];
  page.on('pageerror', e => errors.push({ url: page.url(), msg: e.message }));

  console.log('🔐 Login via /_qa/login...');
  const loginResp = await page.goto(LOGIN);
  if (loginResp.status() !== 200) { console.error('❌ login failed'); process.exit(1); }
  console.log('✅ Login OK');

  const results = [];
  let passed = 0, failed = 0;

  for (const [id, route, name] of USER_SCREENS) {
    for (const vp of VIEWPORTS) {
      await page.setViewportSize({ width: vp.width, height: vp.height });
      const url = `${BASE}${route}`;
      try {
        const resp = await page.goto(url, { waitUntil: 'networkidle', timeout: 20000 });
        const status = resp.status();
        const finalUrl = page.url();
        const title = await page.title();

        const ov = await page.evaluate(() => ({
          scroll: document.documentElement.scrollWidth,
          client: document.documentElement.clientWidth,
        }));
        const hasOverflow = ov.scroll > ov.client + 2;

        const shotPath = path.join(SHOT_DIR, `${id}_${vp.name}.png`);
        await page.screenshot({ path: shotPath, fullPage: false });

        const ok = status === 200 && !hasOverflow;
        ok ? passed++ : failed++;

        results.push({
          screen: id, name, viewport: vp.name, route,
          status, finalUrl, title, hasOverflow,
          screenshot: path.relative(path.join(__dirname, '..', '..'), shotPath),
          ok,
        });

        console.log(`${ok ? '✅' : '❌'} ${id} ${vp.name}: HTTP ${status}${hasOverflow ? ' (overflow)' : ''}`);
      } catch (e) {
        failed++;
        results.push({ screen: id, viewport: vp.name, error: e.message, ok: false });
        console.error(`❌ ${id} ${vp.name}: ${e.message}`);
      }
    }
  }

  const output = {
    generated: new Date().toISOString(),
    base: BASE,
    totalScreens: USER_SCREENS.length,
    totalViewports: VIEWPORTS.length,
    totalChecks: results.length,
    passed, failed,
    passRate: ((passed / results.length) * 100).toFixed(2) + '%',
    results,
    pageErrors: errors,
  };

  const outPath = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep', 'USER_DEEP_QA_' + Date.now() + '.json');
  fs.writeFileSync(outPath, JSON.stringify(output, null, 2));

  console.log(`\n📄 Evidence: ${path.relative(path.join(__dirname, '..', '..'), outPath)}`);
  console.log(`📊 Total: ${output.totalChecks} | ✅ ${passed} | ❌ ${failed} | ${output.passRate}`);

  await browser.close();
  process.exit(failed > 0 ? 1 : 0);
})();
