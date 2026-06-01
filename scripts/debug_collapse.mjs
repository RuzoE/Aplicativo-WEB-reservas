import puppeteer from 'puppeteer';

async function main() {
  const url = 'http://127.0.0.1:8081';
  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  await page.setViewport({ width: 375, height: 812, deviceScaleFactor: 1 });
  
  await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });
  
  console.log('--- Before Click ---');
  const beforeInfo = await page.evaluate(() => {
    const el = document.getElementById('navbarCollapse');
    if (!el) return 'Element #navbarCollapse not found';
    const computed = window.getComputedStyle(el);
    return {
      classes: el.className,
      display: computed.display,
      visibility: computed.visibility,
      height: computed.height,
      opacity: computed.opacity,
      offsetHeight: el.offsetHeight
    };
  });
  console.log(beforeInfo);

  console.log('Clicking toggler...');
  await page.click('.navbar-toggler');
  
  // Wait 1 second
  await new Promise(resolve => setTimeout(resolve, 1000));
  
  console.log('--- After Click ---');
  const afterInfo = await page.evaluate(() => {
    const el = document.getElementById('navbarCollapse');
    if (!el) return 'Element #navbarCollapse not found';
    const computed = window.getComputedStyle(el);
    return {
      classes: el.className,
      display: computed.display,
      visibility: computed.visibility,
      height: computed.height,
      opacity: computed.opacity,
      offsetHeight: el.offsetHeight
    };
  });
  console.log(afterInfo);

  await browser.close();
}

main().catch(console.error);
