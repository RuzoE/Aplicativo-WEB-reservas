import fs from 'node:fs/promises';
import path from 'node:path';
import puppeteer from 'puppeteer';

async function ensureDir(dirPath) {
  await fs.mkdir(dirPath, { recursive: true });
}

async function main() {
  const url = 'http://127.0.0.1:8081';
  const outPath1 = path.join('evidencias', 'screenshots', 'mobile_closed.png');
  const outPath2 = path.join('evidencias', 'screenshots', 'mobile_open.png');

  await ensureDir(path.dirname(outPath1));

  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  await page.setViewport({ width: 375, height: 812, deviceScaleFactor: 1 });
  
  console.log(`Navigating to ${url}...`);
  await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });
  
  // Take screenshot of closed menu
  await page.screenshot({ path: outPath1 });
  console.log(`Saved screenshot of closed mobile menu to ${outPath1}`);

  // Click the toggler
  console.log('Clicking the navbar toggler...');
  await page.click('.navbar-toggler');
  
  // Wait 1 second for bootstrap animation to finish
  await new Promise(resolve => setTimeout(resolve, 1000));
  
  // Take screenshot of open menu
  await page.screenshot({ path: outPath2 });
  console.log(`Saved screenshot of open mobile menu to ${outPath2}`);

  await browser.close();
}

main().catch((err) => {
  console.error('Screenshot failed:', err);
  process.exit(1);
});
