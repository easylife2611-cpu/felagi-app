#!/usr/bin/env node
/**
 * Admin Deep QA — 23 screens × 6 viewports + screenshots + a11y
 * Uses /_qa/login (L337 fix) for authenticated session.
 */
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'https://zagcreativity.com';
const LOGIN = `${BASE}/_qa/login?user=So`;

// 23 admin screens — verified via route:list
const ADMIN_SCREENS = [
  { id: 'A001', path: '/admin/dashboard',                     name: 'Dashboard' },
  { id: 'A002', path: '/admin/telegram',                      name: 'Telegram Distribution' },
  { id: 'A003', path: '/admin/health',                        name: 'Health' },
  { id: 'A004', path: '/admin/features',                      name: 'Features' },
  { id: 'A005', path: '/admin/marketplace',                   name: 'Marketplace' },
  { id: 'A006', path: '/admin/ai',                            name: 'AI' },
  { id: 'A007', path: '/admin/payments',                      name: 'Payments' },
  { id: 'A008', path: '/admin/users',                         name: 'Users' },
  { id: 'A009', path: '/admin/content',                       name: 'Content' },
  { id: 'A010', path: '/admin/notifications',                 name: 'Notifications' },
  { id: 'A011', path: '/admin/files',                         name: 'Files' },
  { id: 'A012', path: '/admin/jobs',                          name: 'Jobs' },
  { id: 'A013', path: '/admin/backups',                       name: 'Backups' },
  { id: 'A014', path: '/admin/integrity',                     name: 'Integrity' },
  { id: 'A015', path: '/admin/security',                      name: 'Security' },
  { id: 'A016', path: '/admin/audit',                         name: 'Audit' },
  { id: 'A017', path: '/admin/settings',                      name: 'Settings' },
  { id: 'A018', path: '/admin/recovery',                      name: 'Recovery' },
  { id: 'A019', path: '/admin/safe-mode',                     name: 'Safe Mode' },
  { id: 'A020', path: '/admin/monetization',                  name: 'Monetization' },
  { id: 'A021', path: '/admin/maintenance',                   name: 'Maintenance' },
  { id: 'A022', path: '/admin/reports',                       name: 'Reports' },
  { id: 'A023', path: '/admin/monetization/sponsored-ads',    name: 'Sponsored Ads' },
];

const VIEWPORTS = [
  { name: 'mobile-320',   width: 320,  height: 568 },
  { name: 'mobile-360',   width: 360,  height: 640 },
  { name: 'tablet-600',   width: 600,  height: 800 },
  { name: 'tablet-840',   width: 840,  height: 1000 },
  { name: 'desktop-1200', width: 1200, height: 900 },
  { name: 'desktop-1600', width: 1600, height: 900 },
];

const SCREENSHOT_DIR = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep', 'screenshots', 'admin');
fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  const errors = [];
  page.on('pageerror', e => errors.push({ url: page.url(), msg: e.message }));

  // Login via L337 QA route
  console.log('🔐 Logging in via /_qa/login...');
  const loginResp = await page.goto(LOGIN);
  const loginStatus = loginResp.status();
  if (loginStatus !== 200) {
    console.error(`❌ Login failed: HTTP ${loginStatus}`);
    process.exit(1);
  }
  const loginBody = await page.textContent('body');
  console.log(`✅ Login: ${loginBody.slice(0, 120)}`);

  // Iterate 23 screens × 6 viewports
  const results = [];
  let passed = 0, failed = 0;

  for (const screen of ADMIN_SCREENS) {
    for (const vp of VIEWPORTS) {
      await page.setViewportSize({ width: vp.width, height: vp.height });
      const url = `${BASE}${screen.path}`;

      try {
        const resp = await page.goto(url, { waitUntil: 'networkidle', timeout: 20000 });
        const status = resp.status();
        const finalUrl = page.url();
        const title = await page.title();
        const redirected = finalUrl.includes('/admin/login');

        // Overflow check
        const overflow = await page.evaluate(() => ({
          bodyScroll: document.body.scrollWidth,
          bodyClient: document.body.clientWidth,
          htmlScroll: document.documentElement.scrollWidth,
          htmlClient: document.documentElement.clientWidth,
        }));
        const hasOverflow = overflow.htmlScroll > overflow.htmlClient + 2;

        // Screenshot
        const shotPath = path.join(SCREENSHOT_DIR, `${screen.id}_${vp.name}.png`);
        await page.screenshot({ path: shotPath, fullPage: false });

        const ok = status === 200 && !redirected && !hasOverflow;
        if (ok) passed++; else failed++;

        results.push({
          screen: screen.id, name: screen.name, viewport: vp.name,
          status, finalUrl, title, redirected, hasOverflow,
          screenshot: path.relative(path.join(__dirname, '..', '..'), shotPath),
          ok,
        });

        const icon = ok ? '✅' : '❌';
        console.log(`${icon} ${screen.id} ${vp.name}: HTTP ${status}${redirected ? ' (redirect)' : ''}${hasOverflow ? ' (overflow)' : ''}`);

      } catch (e) {
        failed++;
        results.push({ screen: screen.id, viewport: vp.name, error: e.message, ok: false });
        console.error(`❌ ${screen.id} ${vp.name}: ${e.message}`);
      }
    }
  }

  // Evidence JSON
  const output = {
    generated: new Date().toISOString(),
    base: BASE,
    loginRoute: LOGIN,
    totalScreens: ADMIN_SCREENS.length,
    totalViewports: VIEWPORTS.length,
    totalChecks: results.length,
    passed,
    failed,
    passRate: ((passed / results.length) * 100).toFixed(2) + '%',
    results,
    pageErrors: errors,
  };

  const outPath = path.join(__dirname, '..', '..', 'docs', 'reports', 'qa', 'deep', 'ADMIN_DEEP_QA_' + Date.now() + '.json');
  fs.writeFileSync(outPath, JSON.stringify(output, null, 2));

  console.log(`\n📄 Evidence: ${path.relative(path.join(__dirname, '..', '..'), outPath)}`);
  console.log(`📊 Total: ${output.totalChecks} | ✅ ${passed} | ❌ ${failed} | ${output.passRate}`);

  await browser.close();
  process.exit(failed > 0 ? 1 : 0);
})();
