# InsPres-A — Proposal Screenshot Flows

**Purpose:** Ordered screen sequences to capture for the INPReS proposal deck.
**Prototype:** Laravel 3-act system (Apply · Track · Officer Console) + LAMPIRAN A "Sistem Wajib" capability screens — now **threaded to 3 anchor cases** so every screenshot reads as one continuous application, not disconnected module mockups.
**Base URL:** `http://localhost:8000`

---

## 0 — Prep before screenshotting

```powershell
php artisan migrate:fresh --seed     # rebuild + seed (includes the 3 anchor cases)
php artisan view:clear               # ensure fresh compiled views
npm run build                        # compile assets (or `npm run dev` for live)
php artisan serve                    # serves http://localhost:8000
```

**Demo logins** (password = `password`):

| Role | Email | Sees |
|------|-------|------|
| Officer | `demo@jpn.gov.my` | triage, wizard, biometric, ABIS, kaveat, hospital, CLMS, family-tree |
| Supervisor | `nurul@jpn.gov.my` | + team kanban, blockchain ledger, agency integration, hardware |
| Admin | `ibrahim@jpn.gov.my` | + Kafka events, MyDigital ID, admin dashboard |

Capture at **1440px wide**, dark theme. One screen = one slide.

---

## The case-threading model (read this first)

Every Sistem Wajib screen now carries a **`?case=` context** and renders a persistent
**KES SEMASA rail** at the top: the anchor applicant's name, IC, reference, a step
breadcrumb of that case's journey, and a case switcher. The applicant you see on the
detail page is the **same person** featured (highlighted row / hero panel) on every
capability screen — ABIS, biometric, blockchain, etc.

**Three anchor cases (one per module):**

| Case | `?case=` | Anchor applicant | Reference |
|------|----------|------------------|-----------|
| MyKad (Gantian) | `mykad` | **Lim Pei Shan** | `APP-20260528-7001` |
| Kelahiran (Birth) | `birth` | **Nur Sofia binti Amir Hamzah** (ibu: Farah Nadia) | `APP-20260528-7002` |
| Perkahwinan Sivil | `marriage` | **Daniel Tan Wei Jie × Sarah Pereira** (non-Muslim civil) | `APP-20260528-7003` |

Switch cases on any screen via the rail's "Tukar Kes" chips or by appending `?case=mykad|birth|marriage`.

---

## FLOW A — Citizen Journey (public, no login)

*Story: rakyat applies online in 90 seconds, AI auto-fills from IC scan, tracks status 24/7.*

| # | Screen | URL | Caption |
|---|--------|-----|---------|
| A1 | Landing / hero | `/` | "Tiada barisan. Tiada borang kertas." + 3 doc tiles |
| A2 | Apply form | `/apply?type=birth` | 3-field form before autofill |
| A3 | Mock-OCR autofill | `/apply?type=birth` | Enter IC → fields auto-populate |
| A4 | Reference issued | after POST | Confirmation card with `APP-…` reference |
| A5 | Track search | `/track` | Reference lookup |
| A6 | Track timeline | `/track/APP-…` | Status timeline + AI ETA |

---

## FLOW B — Officer Triage & Decision (login: officer)

*Story: AI scores + queues every application, officer reviews high-confidence cases in seconds.*

| # | Screen | URL | Caption |
|---|--------|-----|---------|
| B1 | Login | `/system/login` | Officer console login |
| B2 | Dashboard | `/system` | KPI tiles, queue, SLA overview |
| B3 | Tapisan queue | `/system/tapisan` | AI-scored triage list + SLA badges |
| B4 | **Tapisan detail (hero)** | `/system/tapisan/APP-20260528-7001` | Lim Pei Shan — AI panel, OCR match, Skor AI, **+ Sistem Wajib quick-link strip** |
| B5 | Approve | after approve | State → approved, audit entry |
| B6 | Kanban | `/system/kanban` | Lifecycle columns |
| B7 | Audit log | `/system/audit` | Immutable-style action trail |

> B4 is the pivot: the "Sistem Wajib · LAMPIRAN A →" strip links straight into the threaded capability screens for that exact case.

---

## FLOW C — Threaded Case Walkthroughs (the differentiator)

Three continuous, single-applicant journeys. Each screen shows the KES SEMASA rail for the same person — screenshot them in order and the deck reads as one terrain per module.

### C1 — MyKad **Gantian Hilang** (lost): **Lim Pei Shan** (login: officer; blockchain/mydigital need supervisor/admin)

Lost-card flow (Modul 04): police report → old card revoked → biometric re-verify → ABIS 1:N (confirm owner) → CLMS personalization → **physical card issued** (blockchain commit as passthrough) → MyDigital ID.

| Step | Screen | URL |
|------|--------|-----|
| Anchor | Tapisan detail | `/system/tapisan/APP-20260528-7001` |
| 1 | **Lapor Kehilangan** (police report, old card DIBATALKAN, RM110 fee) → "Teruskan ke Biometrik" | `/system/lapor-kehilangan?case=mykad` |
| 2 | Biometric capture (ethnic photo, 10-print + face + iris, NFIQ 97) → "Hantar ke ABIS" | `/system/biometric-capture?case=mykad` |
| 3 | ABIS 1:N (MATCH 99.91% confirms she is the lawful owner; ethnic candidate gallery) → "Teruskan ke CLMS" | `/system/abis-match?case=mykad` |
| 4 | CLMS pipeline (serial `MK-2026-770001`) → **"Personalisasi & Keluarkan Kad"** popup (revoke old + issue new, blockchain commit) → Jana Kad | `/system/clms-pipeline?case=mykad` |
| 5 | **Kad MyKad dikeluarkan** — physical card artifact (photo, IC, chip, hologram, JPN branding) | `/system/kad-mykad?case=mykad` |
| 6 | Blockchain ledger (CardRevoked old + MyKadIssued new) — *supervisor/admin oversight* | `/system/blockchain-ledger?case=mykad` |
| 7 | MyDigital ID auto-provision (her account) — *admin* | `/system/mydigital-id?case=mykad` |

> Blockchain is admin-viewable oversight; the officer never opens it — the commit (revoke old + issue new) **passes through** as the popup on CLMS, on the way to the card artifact. The card is the deliverable screenshot, parallel to Sijil LM05 (birth) / KC02 (marriage).

### C2 — Kelahiran: **Nur Sofia** (login: officer)

Spec flow (Modul 01, Akta 299): hospital sends clinical data → **parents complete the form online** → **counter biometric pengesahan** → register → Salasilah → blockchain passthrough → Sijil JPN.LM05 + MyKid.

| Step | Screen | URL |
|------|--------|-----|
| Anchor | Tapisan detail (ibu + bapa) | `/system/tapisan/APP-20260528-7002` |
| 1 | Hospital pra-daftar (KKM FHIR — clinical data + masa/tempat lahir in) | `/system/hospital-pra-daftar?case=birth` |
| 2 | **Borang Pendaftaran JPN.LM01** (online; bayi auto-isi dari hospital; ibu+bapa auto-isi penuh dari rekod MyKad) | `/system/borang-kelahiran?case=birth` |
| 3 | **Pengesahan biometrik ibu bapa** (counter; 10-print + face) → **"Hantar ke ABIS →"** | `/system/biometric-capture?case=birth` |
| 4 | ABIS 1:N (parents unique; NO MATCH = bukan pendua) → **"Teruskan ke Salasilah →"** | `/system/abis-match?case=birth` |
| 5 | Salasilah (auto-link parents + grandparents — keluarga Islam) → **"Daftar & Hantar ke Blockchain"** popup → Jana Sijil | `/system/family-tree?case=birth` |
| 6 | **Sijil Kelahiran JPN.LM05 + MyKid** (cert, QR, MyKid eligibility) | `/system/sijil?case=birth` |

> Blockchain is a **locked passthrough** (admin-only module; process flows through). The Salasilah page has the interactive commit popup → certificate.

### C3 — Perkahwinan Sivil: **Daniel Tan Wei Jie × Sarah Pereira** (login: officer)

Civil marriage (Modul 05, Akta 164) is **non-Muslim only** and a **two-party** process — the entire marriage list uses non-Muslim names. Both parties are adults, so their **MyKad + biometrik are already on file** (verified, not captured). Open from the **Sijil Perkahwinan** queue → "Semak".

| Step | Screen | What it shows |
|------|--------|---------------|
| Anchor | `/system/tapisan/APP-20260528-7003` | **Both** parties side-by-side: MyKad no, DOB, biometrik *atas fail*, status Bujang |
| 1 | `/system/family-tree?case=marriage` | Salasilah — both families, blood-relation check (allowed) |
| 2 | `/system/kaveat-board?case=marriage` | Kaveat 21 hari + countdown + **bantahan** capability (public objection) |
| 3 | `/system/upacara-perkahwinan?case=marriage` | Upacara: 4 venue types — **Rumah Ibadat (Gereja St. John)** with location detail inputs / JPN / MALAWAKIL (country) / Tribunal; 2 saksi + Pendaftar + ikrar; → **blockchain passthrough (612ms, module admin-locked)** → Jana Sijil |
| 4 | `/system/sijil-perkahwinan?case=marriage` | **Sijil JPN.KC02** — certificate, QR crypto-signed, 2 salinan, blockchain-confirmed |

> The officer never opens the Blockchain module (admin-locked) — the business process *passes through* it (shown as a 612ms ledger confirmation) on the way to the certificate. The Upacara/solemnization + venue choice is the defining civil-marriage act, with no equivalent in the MyKad or birth journeys.

---

## FLOW D — Supervisor / Admin Oversight

| # | Screen | URL | Login |
|---|--------|-----|-------|
| D1 | Supervisor dashboard | `/system` | supervisor |
| D2 | Team kanban | `/system/kanban` | supervisor |
| D3 | Statistik (charts) | `/system/statistik` | any officer |
| D4 | Agency integration (13 agensi + agencies touched by case) | `/system/agensi-integrasi?case=mykad` | supervisor |
| D5 | Hardware status | `/system/perkakasan-status` | supervisor |
| D6 | Kafka events | `/system/kafka-events` | admin |
| D7 | Sub-function catalog (63-function coverage map) | `/system/sub-fungsi-katalog` | officer |

---

## Recommended deck order

1. **A1** — citizen hero (the "why")
2. **A2–A6** — citizen journey end-to-end
3. **B4** — Lim Pei Shan detail + Sistem Wajib strip (the pivot)
4. **C1 steps 1→5** — one MyKad applicant through biometric → ABIS → CLMS → blockchain → MyDigital ID
5. **C2** — birth case (hospital → family tree → blockchain)
6. **C3** — civil marriage case (kaveat → family tree → blockchain)
7. **D3 / D7** — analytics + 63-function catalog (full-scope awareness)

The C-flows are what make it read as **a system**: same face, same reference, every capability.

---

## Honesty note (internal — do not screenshot)

Per `COVERAGE-ANALYSIS.md`: Flows A–B are **functional** (real DB, real workflow). The
Sistem Wajib screens in Flow C/D are **capability views** — now threaded to a consistent
anchor applicant for presentation, but **not** wired to real ABIS / Hyperledger / Kafka /
agency backends. Present them as "designed capability / proposed architecture," not "live."
Front-stage journey ≈ 30–40% built; critical backend = 0% built.
