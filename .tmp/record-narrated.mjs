import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const BASE = 'https://inpres-t.myappsonline.net';
const VIEWPORT = { width: 1920, height: 1080 };
const CLIPS = '.tmp/clips';
const STATE = '.tmp/state.json';
const DUR = JSON.parse(fs.readFileSync('C:/Users/User/Desktop/Claude/ClaudeCode/Aril/ProjectAI/SpecialProject/Video/Test01/unijaya-tutorial-pipeline/output/inpres_narration/durations.json', 'utf-8'));
// map path -> duration (seconds)
const durByPath = Object.fromEntries(DUR.map((d) => [d.path, { dur: d.duration, id: d.id }]));

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
fs.rmSync(CLIPS, { recursive: true, force: true });
fs.mkdirSync(CLIPS, { recursive: true });

const PUBLIC = ['/', '/pengenalan', '/perkhidmatan', '/soalan-lazim', '/apply', '/track'];
const OFFICER = ['/system', '/system/statistik', '/system/kanban', '/system/tapisan',
  '/system/abis-match', '/system/biometric-capture', '/system/family-tree',
  '/system/kad-mykad', '/system/clms-pipeline', '/system/audit'];

const browser = await chromium.launch();

// --- Login once, persist storage state ---
{
  const ctx = await browser.newContext({ viewport: VIEWPORT });
  const page = await ctx.newPage();
  await page.goto(BASE + '/system/login', { waitUntil: 'domcontentloaded' });
  await page.fill('input[name="email"]', 'demo@jpn.gov.my');
  await page.fill('input[name="password"]', 'password');
  await Promise.all([
    page.waitForLoadState('networkidle').catch(() => {}),
    page.click('button[type="submit"].btn--primary'),
  ]);
  await sleep(1500);
  const ok = !page.url().includes('/login');
  console.log(`login: ${ok} (${page.url()})`);
  await ctx.storageState({ path: STATE });
  await ctx.close();
  if (!ok) { console.log('LOGIN FAILED'); process.exit(1); }
}

// Record one screen for exactly `dur` seconds of visible content, scrolling within.
async function record(p, authed) {
  const meta = durByPath[p];
  if (!meta) { console.log(`no duration for ${p}, skip`); return; }
  const dir = path.join(CLIPS, meta.id);
  fs.mkdirSync(dir, { recursive: true });
  const ctx = await browser.newContext({
    viewport: VIEWPORT,
    recordVideo: { dir, size: VIEWPORT },
    ...(authed ? { storageState: STATE } : {}),
  });
  const page = await ctx.newPage();
  const resp = await page.goto(BASE + p, { waitUntil: 'domcontentloaded', timeout: 30000 }).catch(() => null);
  await page.waitForLoadState('networkidle').catch(() => {});
  // dwell window = narration length; smooth-scroll through page across that window
  const dwellMs = meta.dur * 1000;
  const t0 = Date.now();
  const height = await page.evaluate(() => document.body.scrollHeight).catch(() => VIEWPORT.height);
  if (height > VIEWPORT.height + 150) {
    const steps = Math.max(3, Math.min(10, Math.round(meta.dur / 1.0)));
    const stepMs = dwellMs / (steps + 1);
    await sleep(stepMs);
    for (let i = 1; i <= steps; i++) {
      await page.evaluate((y) => window.scrollTo({ top: y, behavior: 'smooth' }), (height - VIEWPORT.height) * (i / steps)).catch(() => {});
      await sleep(stepMs);
    }
  }
  const remain = dwellMs - (Date.now() - t0);
  if (remain > 0) await sleep(remain);
  await ctx.close(); // flush video
  // rename the single webm to <id>.webm
  const webm = fs.readdirSync(dir).find((f) => f.endsWith('.webm'));
  fs.renameSync(path.join(dir, webm), path.join(CLIPS, `${meta.id}.webm`));
  fs.rmSync(dir, { recursive: true, force: true });
  console.log(`✓ ${meta.id}  ${p}  ${resp ? resp.status() : 'ERR'}  ${meta.dur}s`);
}

for (const p of PUBLIC) await record(p, false);
for (const p of OFFICER) await record(p, true);

await browser.close();
console.log('DONE');
