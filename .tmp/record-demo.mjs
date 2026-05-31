import { chromium } from 'playwright';

const BASE = process.env.BASE_URL || 'https://inpres-t.myappsonline.net';
const OUT_DIR = '.tmp/video';
const VIEWPORT = { width: 1920, height: 1080 };

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

// Smooth scroll down a page, then back to top.
async function tour(page, { settle = 1200, scroll = true } = {}) {
  await page.waitForLoadState('networkidle').catch(() => {});
  await sleep(settle);
  if (!scroll) return;
  const height = await page.evaluate(() => document.body.scrollHeight);
  if (height > VIEWPORT.height + 200) {
    const steps = Math.min(12, Math.ceil(height / 400));
    for (let i = 1; i <= steps; i++) {
      await page.evaluate((y) => window.scrollTo({ top: y, behavior: 'smooth' }), (height / steps) * i);
      await sleep(600);
    }
    await sleep(700);
    await page.evaluate(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
    await sleep(900);
  }
}

async function goto(page, path, label, opts) {
  console.log(`→ ${label}  (${path})`);
  const resp = await page.goto(BASE + path, { waitUntil: 'domcontentloaded', timeout: 30000 }).catch((e) => {
    console.log(`   FAILED: ${e.message}`);
    return null;
  });
  if (resp) console.log(`   ${resp.status()}`);
  await tour(page, opts);
}

const PUBLIC = [
  ['/', 'Home — landing'],
  ['/pengenalan', 'Pengenalan'],
  ['/perkhidmatan', 'Perkhidmatan'],
  ['/soalan-lazim', 'Soalan Lazim'],
  ['/apply', 'Act 1 — Apply'],
  ['/track', 'Act 2 — Smart Tracker'],
];

const OFFICER = [
  ['/system', 'Officer dashboard (Utama)'],
  ['/system/statistik', 'Statistik'],
  ['/system/kanban', 'Kanban board'],
  ['/system/tapisan', 'Tapisan (filtering)'],
  ['/system/abis-match', 'ABIS biometric match'],
  ['/system/biometric-capture', 'Biometric capture'],
  ['/system/family-tree', 'Family tree'],
  ['/system/kad-mykad', 'MyKad card'],
  ['/system/clms-pipeline', 'CLMS pipeline'],
  ['/system/audit', 'Audit trail'],
];

const browser = await chromium.launch({ args: ['--start-maximized'] });
const context = await browser.newContext({
  viewport: VIEWPORT,
  recordVideo: { dir: OUT_DIR, size: VIEWPORT },
});
const page = await context.newPage();

// --- Public acts ---
for (const [path, label] of PUBLIC) await goto(page, path, label);

// --- Login ---
console.log('→ Login as demo officer');
await page.goto(BASE + '/system/login', { waitUntil: 'domcontentloaded' });
await sleep(1000);
await page.fill('input[name="email"]', 'demo@jpn.gov.my');
await sleep(400);
await page.fill('input[name="password"]', 'password');
await sleep(500);
await Promise.all([
  page.waitForLoadState('networkidle').catch(() => {}),
  page.click('button[type="submit"].btn--primary'),
]);
await sleep(1500);
const loggedIn = !page.url().includes('/login');
console.log(`   logged in: ${loggedIn} (url=${page.url()})`);

// --- Officer console ---
if (loggedIn) {
  for (const [path, label] of OFFICER) await goto(page, path, label);
} else {
  console.log('   SKIPPING officer tour — login failed (DB likely not seeded on live).');
}

await sleep(1000);
await context.close(); // flush video
await browser.close();

const fs = await import('node:fs');
const files = fs.readdirSync(OUT_DIR).filter((f) => f.endsWith('.webm'));
console.log('VIDEO_FILES=' + files.join(','));
