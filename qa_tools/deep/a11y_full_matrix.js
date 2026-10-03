#!/usr/bin/env node
/**
 * L338 Block D — Full A11y Matrix (46 screens)
 * Tests: keyboard, reduced motion, zoom 200/400, Amharic, dark, landscape
 */
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'https://zagcreativity.com';
const LOGIN = `${BASE}/_qa/login?user=So`;

const SCREENS = [
  ['S001','/'],['S002','/auth/telegram'],['S003','/profile'],['S004','/browse'],
  ['S005','/needs/new'],['S006','/needs/new/public-preview'],['S007','/needs/test-id/created'],
  ['S008','/needs/test-id'],['S009','/my/needs'],['S010','/needs/test-id/offers'],
  ['S011','/needs/test-id/offers/new'],['S012','/offers/test-id'],['S013','/my/offers'],
  ['S014','/needs/test-id/compare'],['S015','/comparisons/test-id'],
  ['S016','/needs/test-id/comparisons'],['S017','/offers/test-id/messages'],
  ['S018','/notifications'],['S019','/needs/test-id/boost'],['S020','/needs/test-id/rating'],
  ['S021','/support/report'],['S022','/needs/test-id/publications'],
  ['S023','/needs/test-id/offers/unlock'],
  ['A001','/admin/dashboard'],['A002','/admin/telegram'],['A003','/admin/health'],
  ['A004','/admin/features'],['A005','/admin/marketplace'],['A006','/admin/ai'],
  ['A007','/admin/payments'],['A008','/admin/users'],['A009','/admin/content'],
  ['A010','/admin/notifications'],['A011','/admin/files'],['A012','/admin/jobs'],
  ['A013','/admin/backups'],['A014','/admin/integrity'],['A015','/admin/security'],
  ['A016','/admin/audit'],['A017','/admin/settings'],['A018','/admin/recovery'],
  ['A019','/admin/safe-mode'],['A020','/admin/monetization'],['A021','/admin/maintenance'],
  ['A022','/admin/reports'],['A023','/admin/monetization/sponsored-ads'],
];

async function testKeyboard(page) {
  const chain = [];
  for (let i = 0; i < 20; i++) {
    await page.keyboard.press('Tab');
    const info = await page.evaluate(() => {
      const el = document.activeElement;
      if (!el || el === document.body) return null;
      const s = window.getComputedStyle(el);
      const hasOutline = s.outlineStyle !== 'none' && parseFloat(s.outlineWidth) > 0;
      const hasShadow = s.boxShadow !== 'none' && s.boxShadow !== '';
      return { visible: hasOutline || hasShadow };
    });
    if (info) chain.push(info);
  }
  return { total: chain.length, visible: chain.filter(f => f.visible).length };
}

async function testZoom(page, lvl) {
  await page.evaluate(l => { document.documentElement.style.fontSize = l + '%'; }, lvl);
  return await page.evaluate(() => ({
    scroll: document.documentElement.scrollWidth,
    client: document.documentElement.clientWidth,
  }));
}

async function countAnimated(page) {
  return await page.evaluate(() => {
    let c = 0;
    document.querySelectorAll('*').forEach(el => {
      const s = window.getComputedStyle(el);
      if (s.animationName !== 'none' && s.animationDuration !== '0s') c++;
    });
    return c;
  });
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const results = { generated: new Date().toISOString(), screens: {}, summary: {} };

  // Light context
  const ctx = await browser.newContext({ viewport: { width: 1200, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(LOGIN);

  for (const [id, route] of SCREENS) {
    try {
      await page.goto(`${BASE}${route}`, { waitUntil: 'domcontentloaded', timeout: 15000 });
      const kb = await testKeyboard(page);
      await page.goto(`${BASE}${route}`, { waitUntil: 'domcontentloaded' });
      const z200 = await testZoom(page, 200);
      await page.goto(`${BASE}${route}`, { waitUntil: 'domcontentloaded' });
      const z400 = await testZoom(page, 400);
      await page.goto(`${BASE}${route}`, { waitUntil: 'domcontentloaded' });
      const amharic = await page.evaluate(() => ({
        hasEthiopic: /[\u1200-\u137F]/.test(document.body.textContent || ''),
      }));
      results.screens[id] = {
        route,
        keyboard: { ...kb, pass: kb.total > 0 && kb.visible === kb.total },
        zoom200: { ...z200, pass: z200.scroll <= z200.client + 2 },
        zoom400: { ...z400, pass: z400.scroll <= z400.client + 2 },
        amharic,
      };
      const ok = results.screens[id].keyboard.pass
        && results.screens[id].zoom200.pass
        && results.screens[id].zoom400.pass;
      console.log(`${ok ? '✅' : '⚠️'} ${id}: kb=${kb.visible}/${kb.total} z200=${z200.scroll}/${z200.client} z400=${z400.scroll}/${z400.client}`);
    } catch (e) {
      results.screens[id] = { route, error: e.message };
      console.error(`❌ ${id}: ${e.message}`);
    }
  }
  await ctx.close();

  // Reduced motion
  const ctxR = await browser.newContext({ reducedMotion: 'reduce', viewport: { width: 1200, height: 900 } });
  const pageR = await ctxR.newPage();
  await pageR.goto(LOGIN);
  for (const [id, route] of SCREENS) {
    try {
      await pageR.goto(`${BASE}${route}`, { waitUntil: 'domcontentloaded' });
      const animated = await countAnimated(pageR);
      results.screens[id].reducedMotion = { animated, pass: animated === 0 };
    } catch (e) {
      results.screens[id].reducedMotion = { error: e.message };
    }
  }
  await ctxR.close();

  // Dark mode
  const ctxD = await browser.newContext({ colorScheme: 'dark', viewport: { width: 1200, height: 900 } });
  const pageD = await ctxD.newPage();
  await pageD.goto(LOGIN);
  for (const [id, route] of SCREENS) {
    try {
      await pageD.goto(`${BASE}${route}`, { waitUntil: 'domcontentloaded' });
      const bg = await pageD.evaluate(() => window.getComputedStyle(document.body).backgroundColor);
      results.screens[id].darkMode = { bodyBg: bg, pass: bg !== 'rgb(255, 255, 255)' };
    } catch (e) {
      results.screens[id].darkMode = { error: e.message };
    }
  }
  await ctxD.close();

  // Landscape 800x400
  const ctxL = await browser.newContext({ viewport: { width: 800, height: 400 } });
  const pageL = await ctxL.newPage();
  await pageL.goto(LOGIN);
  for (const [id, route] of SCREENS) {
    try {
      await pageL.goto(`${BASE}${route}`, { waitUntil: 'domcontentloaded' });
      const o = await pageL.evaluate(() => ({
        scroll: document.documentElement.scrollWidth,
        client: document.documentElement.clientWidth,
      }));
      results.screens[id].landscape = { ...o, pass: o.scroll <= o.client + 2 };
    } catch (e) {
      results.screens[id].landscape = { error: e.message };
    }
  }
  await ctxL.close();
  await browser.close();

  const all = Object.values(results.screens);
  results.summary = {
    total: all.length,
    keyboardPass: all.filter(s => s.keyboard?.pass).length,
    reducedMotionPass: all.filter(s => s.reducedMotion?.pass).length,
    zoom200Pass: all.filter(s => s.zoom200?.pass).length,
    zoom400Pass: all.filter(s => s.zoom400?.pass).length,
    darkModePass: all.filter(s => s.darkMode?.pass).length,
    landscapePass: all.filter(s => s.landscape?.pass).length,
    amharicPass: all.filter(s => s.amharic?.hasEthiopic).length,
  };

  const outPath = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep',
    'A11Y_FULL_MATRIX_' + Date.now() + '.json');
  fs.writeFileSync(outPath, JSON.stringify(results, null, 2));

  console.log(`\n📄 ${path.relative(path.join(__dirname, '..', '..'), outPath)}`);
  console.log('📊 SUMMARY:');
  Object.entries(results.summary).forEach(([k, v]) => {
    if (k !== 'total') console.log(`   ${k}: ${v}/${results.summary.total}`);
  });
})();
