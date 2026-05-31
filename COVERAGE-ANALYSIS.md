# InsPres-A Prototype — Coverage vs Spec (Modul 1, 4, 5)

**Date:** 2026-05-29
**Spec source:** `source/INPReS_AppA_11_Modul01_Kelahiran.pdf`, `_14_Modul04_KadPengenalan.pdf`, `_15_Modul05_KahwinCerai.pdf`

---

## What the spec defines

| Modul | Doc | Fungsi utama | Happy-path "cerita" |
|-------|-----|--------------|---------------------|
| 1 — Kelahiran | birth | **22** | 14 mesej / 9 komponen / 12 min |
| 4 — Kad Pengenalan MyKad | mykad | **22** | 14 langkah / 18 min (ABIS 1:N) |
| 5 — Perkahwinan & Perceraian | marriage | **19** | 13 langkah / 22 min (kaveat 21 hari) |
| **Total** | | **63 fungsi** | |

Each module's transaction story spans 7–9 backend components: biometric-svc + **ABIS 1:N (GPU H200, 30M rekod)**, **Oracle RAC** + Data Guard, **Hyperledger Fabric** (immutable audit chaincode), **Kafka** event bus, KKM/agency integration, QR crypto-signed sijil.

---

## What the prototype actually is

3-act generic application workflow (Laravel), NOT module-specific logic:

- **Act 1 — Apply** (`/apply`, public): 1 form, 3 fields (IC, nama, alamat) + mock-OCR autofill. `doc_type ∈ {birth, marriage, mykad}`.
- **Act 2 — Track** (`/track`): rujukan lookup + status timeline.
- **Act 3 — System** (officer console, auth): dashboard (utama), tapisan + approve/reject/bulk, kanban, statistik, audit log, AI ETA/score, SLA badge.
- **Birth wizard** (`/system/pendaftaran/baru`): 5-step (hospital → ibu → bayi → ayah → sahkan) — **birth only**.

Lifecycle = single generic chain for all 3 doc types:
`received → verified → officer_review → approved → issued (+ rejected)`

Models: Application, Citizen, AuditLog, User. Service: AiEtaService (1 file). No biometric, no blockchain, no Oracle, no Kafka, no agency integration — all absent.

---

## Coverage assessment

### Lens A — against 63 documented fungsi (literal)
Prototype implements the registration/application happy-path only:

| Modul | Fungsi covered | Of |
|-------|----------------|-----|
| Kelahiran | Pendaftaran Biasa (wizard) + Carian + Paparan sijil (stub) | ~2 / 22 |
| MyKad | Permohonan (generic form) | ~1 / 22 |
| Kahwin/Cerai | Pendaftaran Perkahwinan (generic form) | ~1 / 19 |

**≈ 4 / 63 ≈ 6%** of documented functions.

### Lens B — front-stage user/officer journey (cerita happy-path)
Prototype simulates the **front office** (intake → triage → officer decision → issue → track → audit) but stubs the **entire back office**:

| Layer | Status |
|-------|--------|
| Citizen intake / form | ✅ done (generic) |
| AI triage (score + ETA + SLA) | ✅ done (mock) |
| Officer review / approve / reject | ✅ done |
| Issuance state | ✅ stub (no real sijil/QR) |
| Tracking + audit trail | ✅ done (DB, not blockchain) |
| Biometric ABIS 1:N | ❌ 0% |
| Hyperledger blockchain | ❌ 0% |
| Oracle RAC / Data Guard | ❌ (SQLite/MySQL dev) |
| Kafka event-driven | ❌ 0% |
| KKM + 13-agency integration | ❌ 0% |
| QR crypto-signed cert | ❌ 0% |

Front-stage workflow ≈ **30–40%** of user-visible journey. Critical backend (langkah ditanda oren — biometric, blockchain, immutable proof) = **0%**.

---

## Verdict

**~6% by function spec; ~30% of the front-stage demo journey; 0% of critical backend.**

This is a **UX/workflow presentation prototype** — proves the officer approval experience + AI triage + tracking across 3 doc types. It is NOT a functional implementation of any of the 63 spec functions. The hard, high-value, tender-graded parts (ABIS biometric, Hyperledger immutability, Oracle, Kafka, agency integration) are entirely unbuilt.
