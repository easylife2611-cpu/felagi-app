#!/usr/bin/env node
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'https://zagcreativity.com';
const LOGIN = `${BASE}/_qa/login?user=So`;

// Screens with known Amharic/English UI strings from lang/*.json
const CHECKS = [
  { id: 'S004', route: '/browse',       am: ['ፍላጎቶች','ፈልግ','ምድብ'], en: ['Browse','Search','Category'] },
  { id: 'S005', route: '/needs/new',    am: ['ፍላጎት','አዲስ','ርዕስ'], en: ['Need','Create','Title'] },
  { id: 'S018', route: '/notifications',am: ['ማሳወቂያ','አንብብ','ሁሉም'], en: ['Alert','Read','All'] },
];

(async () => {
  const browser = await chromium.launch({ headless: true });
  const results = [];

  for (const lang of ['am', 'en']) {
    const ctx = await browser.newContext({
      locale: lang === 'am' ? 'am-ET' : 'en-US',
      extraHTTPHeaders: { 'Accept-Language': lang },
    });
    const page = await ctx.newPage();
    await page.goto(LOGIN);

    for (const check of CHECKS) {
      await page.goto(`${BASE}${check.route}`, { waitUntil: 'domcontentloaded' });
      const html = await page.content();
      const expected = check[lang];
      const found = expected.filter(w => html.includes(w));
      const ok = found.length >= 1;
      results.push({ locale: lang, screen: check.id, expected, found, ok });
      console.log(`${ok ? '✅' : '❌'} ${lang} ${check.id}: found ${found.length}/${expected.length} (${found.join(', ')})`);
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

  const outPath = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep', 'LOCALIZATION_DEEP_' + Date.now() + '.json');
  fs.writeFileSync(outPath, JSON.stringify(output, null, 2));
  console.log(`\n📄 ${path.relative(path.join(__dirname, '..', '..'), outPath)}`);
  console.log(`📊 ${output.total} | ✅ ${output.passed} | ❌ ${output.failed}`);
  await browser.close();
})();
