const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'https://zagcreativity.com';
const OUT_DIR = '/home/zagcreht/felagi_app/public/handoff/evidence/L366';
const COOKIE_FILE = '/home/zagcreht/felagi_app/qa_tools/auth-cookie.txt';
const COOKIE_VALUE = fs.readFileSync(COOKIE_FILE, 'utf8').trim();

(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    deviceScaleFactor: 1,
  });
  await context.addCookies([{
    name: 'felagi_session', value: COOKIE_VALUE,
    domain: 'zagcreativity.com', path: '/',
    httpOnly: true, secure: true, sameSite: 'Lax',
  }]);

  const url = BASE_URL + '/offers/1/messages';
  const outFile = path.join(OUT_DIR, 'desktop', 'S017-messages.png');
  const page = await context.newPage();
  const start = Date.now();
  try {
    const resp = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 60000 });
    await page.waitForTimeout(3000);
    await page.screenshot({ path: outFile, fullPage: true });
    const elapsed = Date.now() - start;
    const size = fs.statSync(outFile).size;
    console.log('OK   S017 desktop -- ' + resp.status() + ' -- ' + Math.round(size/1024) + 'KB -- ' + elapsed + 'ms');
  } catch (e) {
    console.log('FAIL S017 desktop -- ' + e.message);
  }
  await page.close();
  await browser.close();
})();
