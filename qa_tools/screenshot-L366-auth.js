const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'https://zagcreativity.com';
const OUT_DIR = '/home/zagcreht/felagi_app/public/handoff/evidence/L366';
const COOKIE_FILE = '/home/zagcreht/felagi_app/qa_tools/auth-cookie.txt';

const COOKIE_VALUE = fs.readFileSync(COOKIE_FILE, 'utf8').trim();

const SCREENS = [
  { id: 'S001', name: 'welcome',                path: '/' },
  { id: 'S002', name: 'telegram',               path: '/auth/telegram' },
  { id: 'S003', name: 'profile',                path: '/profile' },
  { id: 'S004', name: 'browse',                 path: '/browse' },
  { id: 'S005', name: 'create-need',            path: '/needs/new' },
  { id: 'S006', name: 'need-preview',           path: '/needs/new/public-preview' },
  { id: 'S007', name: 'need-created',           path: '/needs/1/created' },
  { id: 'S008', name: 'show-need',              path: '/needs/1' },
  { id: 'S009', name: 'my-needs',               path: '/my/needs' },
  { id: 'S010', name: 'received-offers',        path: '/needs/1/offers' },
  { id: 'S011', name: 'submit-offer',           path: '/needs/1/offers/new' },
  { id: 'S012', name: 'offer-detail',           path: '/offers/1' },
  { id: 'S013', name: 'my-offers',              path: '/my/offers' },
  { id: 'S014', name: 'compare-offers',         path: '/needs/1/compare' },
  { id: 'S015', name: 'comparison-result',      path: '/comparisons/1' },
  { id: 'S016', name: 'comparison-history',     path: '/needs/1/comparisons' },
  { id: 'S017', name: 'messages',               path: '/offers/1/messages' },
  { id: 'S018', name: 'notifications',          path: '/notifications' },
  { id: 'S019', name: 'boost',                  path: '/needs/1/boost' },
  { id: 'S020', name: 'rating',                 path: '/needs/1/rating' },
  { id: 'S021', name: 'report-support',         path: '/support/report' },
  { id: 'S022', name: 'telegram-publications',  path: '/needs/1/publications' },
  { id: 'S023', name: 'offer-unlock',           path: '/needs/1/offers/unlock' },
];

const VIEWPORTS = [
  { name: 'mobile',  width: 360,  height: 800 },
  { name: 'desktop', width: 1440, height: 900 },
];

(async () => {
  const browser = await chromium.launch();
  const results = [];

  for (const vp of VIEWPORTS) {
    console.log('\n=== ' + vp.name.toUpperCase() + ' ===');
    const context = await browser.newContext({
      viewport: { width: vp.width, height: vp.height },
      deviceScaleFactor: 1,
    });

    // Set auth cookie
    await context.addCookies([{
      name: 'felagi_session',
      value: COOKIE_VALUE,
      domain: 'zagcreativity.com',
      path: '/',
      httpOnly: true,
      secure: true,
      sameSite: 'Lax',
    }]);

    for (const screen of SCREENS) {
      const url = BASE_URL + screen.path;
      const outFile = path.join(OUT_DIR, vp.name, screen.id + '-' + screen.name + '.png');
      const page = await context.newPage();
      const start = Date.now();
      try {
        const resp = await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(1500);
        await page.screenshot({ path: outFile, fullPage: true });
        const elapsed = Date.now() - start;
        const size = fs.statSync(outFile).size;
        const finalUrl = page.url();
        const redirected = finalUrl.replace(BASE_URL, '') !== screen.path;
        results.push({ id: screen.id, name: screen.name, vp: vp.name, status: resp.status(), size, ms: elapsed, redirected, finalUrl });
        const flag = redirected ? ' [REDIR]' : '';
        console.log('OK   ' + screen.id + ' ' + vp.name + ' -- ' + resp.status() + ' -- ' + Math.round(size/1024) + 'KB -- ' + elapsed + 'ms' + flag);
      } catch (e) {
        console.log('FAIL ' + screen.id + ' ' + vp.name + ' -- ' + e.message);
        results.push({ id: screen.id, name: screen.name, vp: vp.name, status: 'ERR', error: e.message });
      }
      await page.close();
    }
    await context.close();
  }

  await browser.close();

  const report = path.join(OUT_DIR, 'results-auth.json');
  fs.writeFileSync(report, JSON.stringify(results, null, 2));
  console.log('\n=== DONE ===');
  const ok = results.filter(r => r.status === 200).length;
  const redirects = results.filter(r => r.redirected).length;
  console.log('Total: ' + results.length + ' | 200: ' + ok + ' | Redirects: ' + redirects);
})();
