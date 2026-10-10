const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'https://zagcreativity.com';
const OUT_DIR = '/home/zagcreht/felagi_app/public/handoff/evidence/L366';
const COOKIE_FILE = '/home/zagcreht/felagi_app/qa_tools/auth-cookie.txt';

const COOKIE_VALUE = fs.readFileSync(COOKIE_FILE, 'utf8').trim();

// Continue from S010
const SCREENS = [
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

const VP = { name: 'desktop', width: 1440, height: 900 };

(async () => {
  const browser = await chromium.launch();
  const results = [];

  console.log('\n=== DESKTOP (RESUME from S010) ===');

  for (const screen of SCREENS) {
    // Fresh context per screen — avoid memory accumulation
    const context = await browser.newContext({
      viewport: { width: VP.width, height: VP.height },
      deviceScaleFactor: 1,
    });

    await context.addCookies([{
      name: 'felagi_session',
      value: COOKIE_VALUE,
      domain: 'zagcreativity.com',
      path: '/',
      httpOnly: true,
      secure: true,
      sameSite: 'Lax',
    }]);

    const url = BASE_URL + screen.path;
    const outFile = path.join(OUT_DIR, VP.name, screen.id + '-' + screen.name + '.png');
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
      results.push({ id: screen.id, name: screen.name, vp: VP.name, status: resp.status(), size, ms: elapsed, redirected, finalUrl });
      const flag = redirected ? ' [REDIR]' : '';
      console.log('OK   ' + screen.id + ' ' + VP.name + ' -- ' + resp.status() + ' -- ' + Math.round(size/1024) + 'KB -- ' + elapsed + 'ms' + flag);
    } catch (e) {
      console.log('FAIL ' + screen.id + ' ' + VP.name + ' -- ' + e.message);
      results.push({ id: screen.id, name: screen.name, vp: VP.name, status: 'ERR', error: e.message });
    }
    await page.close();
    await context.close();
  }

  await browser.close();

  const report = path.join(OUT_DIR, 'results-desktop-resume.json');
  fs.writeFileSync(report, JSON.stringify(results, null, 2));
  console.log('\n=== DONE ===');
  const ok = results.filter(r => r.status === 200).length;
  const redirects = results.filter(r => r.redirected).length;
  console.log('Total: ' + results.length + ' | 200: ' + ok + ' | Redirects: ' + redirects);
})();
