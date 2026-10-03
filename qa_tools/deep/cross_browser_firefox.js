#!/usr/bin/env node
const { firefox } = require('playwright');
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

(async () => {
  const browser = await firefox.launch({ headless: true, firefoxUserPrefs: { "security.sandbox.content.level": 0 } });
  const context = await browser.newContext({ viewport: { width: 1200, height: 900 } });
  const page = await context.newPage();

  const errors = [];
  page.on('pageerror', e => errors.push({ url: page.url(), msg: e.message }));

  console.log('🔐 Login via /_qa/login (Firefox)...');
  const loginResp = await page.goto(LOGIN);
  console.log(`   Login: HTTP ${loginResp.status()}`);

  const results = [];
  let passed = 0, failed = 0;

  for (const [id, route] of SCREENS) {
    try {
      const resp = await page.goto(`${BASE}${route}`, { waitUntil: 'networkidle', timeout: 20000 });
      const status = resp.status();
      const finalUrl = page.url();
      const redirected = finalUrl.includes('/admin/login') && !route.includes('/admin/login');
      const overflow = await page.evaluate(() => ({
        scroll: document.documentElement.scrollWidth,
        client: document.documentElement.clientWidth,
      }));
      const hasOverflow = overflow.scroll > overflow.client + 2;
      const ok = status === 200 && !redirected && !hasOverflow;
      ok ? passed++ : failed++;
      results.push({ screen: id, route, status, finalUrl, redirected, hasOverflow, ok });
      console.log(`${ok ? '✅' : '❌'} ${id}: HTTP ${status}${redirected ? ' (redirect)' : ''}${hasOverflow ? ' (overflow)' : ''}`);
    } catch (e) {
      failed++;
      results.push({ screen: id, route, error: e.message, ok: false });
      console.error(`❌ ${id}: ${e.message}`);
    }
  }

  const output = {
    generated: new Date().toISOString(),
    browser: 'firefox',
    total: results.length,
    passed, failed,
    passRate: ((passed / results.length) * 100).toFixed(2) + '%',
    results, errors,
  };

  const outPath = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep',
    'CROSS_BROWSER_FIREFOX_' + Date.now() + '.json');
  fs.writeFileSync(outPath, JSON.stringify(output, null, 2));

  console.log(`\n📄 ${path.relative(path.join(__dirname, '..', '..'), outPath)}`);
  console.log(`📊 Firefox: ${output.total} | ✅ ${passed} | ❌ ${failed} | ${output.passRate}`);

  await browser.close();
  process.exit(failed > 0 ? 1 : 0);
})();
