#!/usr/bin/env node
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'https://zagcreativity.com';
const LOGIN = `${BASE}/_qa/login?user=So`;

const SCREENS = [
  ['S001', '/'],
  ['S003', '/profile'],
  ['S004', '/browse'],
  ['S005', '/needs/new'],
  ['S018', '/notifications'],
];

const LOCALES = [
  { code: 'am', accept: 'am-ET,am;q=0.9' },
  { code: 'en', accept: 'en-US,en;q=0.9' },
];

(async () => {
  const browser = await chromium.launch({ headless: true });
  const results = [];

  for (const loc of LOCALES) {
    const ctx = await browser.newContext({
      locale: loc.accept.split(',')[0],
      extraHTTPHeaders: { 'Accept-Language': loc.accept },
    });
    const page = await ctx.newPage();
    await page.goto(LOGIN);

    for (const [id, route] of SCREENS) {
      await page.goto(`${BASE}${route}`, { waitUntil: 'domcontentloaded' });
      const html = await page.content();
      const title = await page.title();
      const ethiopic = (html.match(/[\u1200-\u137F]/g) || []).length;
      const latin = (html.match(/[a-zA-Z]/g) || []).length;

      const ok = loc.code === 'am' ? ethiopic > 0 : latin > 0;
      results.push({ locale: loc.code, screen: id, route, title, ethiopic, latin, ok });
      console.log(`${ok ? '✅' : '❌'} ${loc.code} ${id}: title="${title}" ethiopic=${ethiopic} latin=${latin}`);
    }
    await ctx.close();
  }

  const output = {
    generated: new Date().toISOString(),
    total: results.length,
    passed: results.filter(r => r.ok).length,
    failed: results.filter(r => !r.ok).length,
    results,
  };

  const outPath = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep', 'LOCALIZATION_QA_' + Date.now() + '.json');
  fs.writeFileSync(outPath, JSON.stringify(output, null, 2));

  console.log(`\n📄 Evidence: ${path.relative(path.join(__dirname, '..', '..'), outPath)}`);
  console.log(`📊 ${output.total} checks | ✅ ${output.passed} | ❌ ${output.failed}`);
  await browser.close();
})();
