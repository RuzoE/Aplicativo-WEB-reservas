import fs from 'node:fs/promises';
import path from 'node:path';
import puppeteer from 'puppeteer';

async function ensureDir(dirPath) {
  await fs.mkdir(dirPath, { recursive: true });
}

async function main() {
  const url = process.env.URL || process.argv[2] || 'https://example.com';
  const outPath = process.env.OUT || process.argv[3] || path.join('evidencias', 'screenshots', 'example.png');

  await ensureDir(path.dirname(outPath));

  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800, deviceScaleFactor: 1 });
  await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });
  await page.screenshot({ path: outPath, fullPage: true });
  await browser.close();

  console.log(`Saved screenshot of ${url} to ${outPath}`);
}

main().catch((err) => {
  console.error('Screenshot failed:', err);
  process.exit(1);
});
