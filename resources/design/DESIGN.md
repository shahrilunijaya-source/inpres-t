# InsPres — Design System

**Status:** v0.1 (Phase 1 foundation)
**Parent:** [kdn-ilpf-retreat](../../../../../SpecialProject/KDN-iLPF/ilpf/ilpf_tapisan_v3.html) — the prototype Shahril called "awesome". This system is a port of that design language onto Laravel + Filament + Tailwind.

This is the source of truth for the visual language across all three brand forks (`inspres-a`, `inspres-t`, `inspres-x`). The forks differ only in `tokens.css` (accent color, font) and brand assets (logo, app name). Everything else — surfaces, glow recipes, gradient buttons, mono numerics, bounce transitions — stays identical across the family.

---

## Surfaces (dark-first)

Four-layer dark surface architecture:

| Layer | Token | Use |
|-------|-------|-----|
| Page background | `--ai-bg` (#0a0a0f) | Body backdrop, main canvas |
| Secondary surface | `--ai-bg-2` (#10121d) | Card/panel solid backgrounds when glass is wrong |
| Glass (low elevation) | `--ai-glass` (rgba 0.04) + blur(20px) | Default card/widget |
| Glass (high elevation) | `--ai-glass-hi` (rgba 0.07) + blur(24px) | Modal, hero, focused panel |

**Border vocabulary:**
- `--ai-border` (rgba 0.08) — default divider, low contrast
- `--ai-border-hi` (rgba 0.16) — focused/active borders

**Body backdrop:** subtle radial gradients in indigo + violet at top-center and bottom-right. Adds depth without distraction. Set in `app.css` body rule.

---

## Typography

| Level | Family | Weight | Size | Letter-spacing |
|-------|--------|--------|------|----------------|
| h1 | Inter | 600 | 2.5rem (40px) | -0.02em |
| h2 | Inter | 600 | 1.875rem (30px) | -0.02em |
| h3 | Inter | 600 | 1.5rem (24px) | -0.02em |
| Body | Inter | 400 | 1rem (16px) | 0 |
| Small | Inter | 400 | 0.875rem (14px) | 0 |
| Mono / Numerics | IBM Plex Mono | 500 | inherit | -0.01em |

**Rule:** any user-facing numeric value — IC number, reference ID, timestamp, ETA, countdown, AI score, application count, postcode — uses the mono family with `tabular-nums`. Helper class: `.mono`.

**Text tiers:**
- `--ai-text` (#e6e9f2) — primary content
- `--ai-text-dim` (#94a3b8) — secondary, labels, captions
- `--ai-text-mute` (#64748b) — disabled, placeholder, metadata

---

## Accent palette

| Token | Hex | Semantic role |
|-------|-----|---------------|
| `--ai-indigo` | #818cf8 | Primary accent, active states, focus glow, primary buttons |
| `--ai-violet` | #c084fc | Secondary accent, gradient pair with indigo (e.g., score pill) |
| `--ai-cyan` | #22d3ee | Highlight, attention without alarm |
| `--ai-emerald` | #34d399 | Success, on-track SLA, approved status |
| `--ai-amber` | #fbbf24 | Warning, at-risk SLA, needs-review |
| `--ai-rose` | #fb7185 | Error, breached SLA, rejected, destructive action |

`--ai-accent` aliases `--ai-indigo` by default. The Phase 6 brand fork swaps this alias (and possibly `--ai-indigo` itself) per brand.

---

## The Five Signature Moves

These five recipes are the design DNA. Every "kdn-ilpf-looking" component uses at least one.

### 1. Multi-layer glow ring
For focus, active state, hero element.

```css
box-shadow:
    0 0 0 3px rgba(129, 140, 248, 0.20),
    0 0 10px rgba(129, 140, 248, 0.70);
```

Helper class `.glow-ring`. Auto-applied to `input:focus`, `textarea:focus`, `select:focus`.

### 2. Glass surface + inset edge light
Frosted-glass card with a 1px top-edge highlight that makes it look "lit from within".

```css
background: var(--ai-glass);
backdrop-filter: blur(20px);
box-shadow:
    0 10px 28px rgba(99, 102, 241, 0.50),
    inset 0 1px 0 rgba(255, 255, 255, 0.25);
```

Helper classes `.glass-card` (low) and `.glass-card-hi` (high elevation).

### 3. Diagonal gradient button (135°)
Primary action surface.

```css
background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
```

Helper class `.btn-gradient`. Variant `.btn-accent` uses `--ai-indigo` → `--ai-violet` (used for AI score pill, etc.).

### 4. Bounce micro-interaction
For state changes, hovers, row selects.

```css
transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
```

Helper class `.bounce`. Applied to interactive elements that benefit from a slight overshoot (buttons, cards, badges).

### 5. Mono font for numerics
Identifies "data" vs "prose" at a glance. Helper class `.mono`.

---

## Component vocabulary

Implemented in [`resources/css/signature.css`](../css/signature.css) and as Blade components in [`resources/views/components/`](../views/components/).

| Component | Class / Tag | Use |
|-----------|-------------|-----|
| Glass card | `.glass-card` / `<x-glass-card>` | Default container |
| Glass card (elevated) | `.glass-card-hi` | Hero/modal containers |
| Gradient button | `.btn-gradient` / `<x-glow-button>` | Primary CTA |
| Accent button | `.btn-accent` | Indigo→violet, used for AI-related actions |
| SLA badge | `.badge .badge-{emerald,amber,rose}` / `<x-sla-badge :state="...">` | Status pills |
| Score pill | `.score-pill` | AI score numeric, gradient background |
| Mono inline | `.mono` | Any user-facing number |
| Pulse glow | `.pulse-glow` | Active timeline step |
| AI sweep | `.ai-sweep` | "AI is working" indicator |
| Fade-up | `.fade-up` | Entrance animation for content |

---

## Two surfaces — Portal vs System

One Laravel app serves two distinct UX surfaces sharing the same tokens and signature CSS:

| Surface | Layout | Route | Auth | Feel |
|---------|--------|-------|------|------|
| Portal | `layouts/portal.blade.php` | `/`, `/apply`, `/track/{ref}` | None | Hero-led, marketing-tier, big type, glass cards spaced generously |
| System | Filament panel (`/admin`) | `/admin/*` | Login required | Dense data, tables, sidebars, score pills, audit-log feel |

Both load the same `tokens.css` + `signature.css`. The visual language is identical. The chrome and density differ.

---

## Voice & tone

- **Portal:** confident, calm, citizen-respectful. Bahasa + English where appropriate. Short sentences. No bureaucratic phrasing.
- **System:** terse, scan-friendly. Officer is busy. Labels over instructions. Numbers over prose. Show counts, not paragraphs.

---

## What stays vs what changes across forks

**Stays identical** across `inspres-a` / `inspres-t` / `inspres-x`:
- `signature.css` (recipes — glass, glow, gradient, bounce, badges)
- All Blade templates, controllers, models, migrations
- Surface architecture, typography scale, component vocabulary
- Voice & tone rules

**Changes per fork (Phase 6 swap):**
- `tokens.css` — `--ai-accent`, `--ai-indigo`, possibly `--ai-violet` (replaced with Shahril's brand CSS for Ace / Theta / Dnex)
- `--ai-font` value + `@font-face` import in `app.css`
- `public/logo.svg`, `public/favicon.ico`
- `config/app.php` `name`
- ~10 brand-name copy strings across welcome/footer/header
- `.env` `DB_DATABASE` + `APP_URL`

If a fork ever needs to change anything in the "stays identical" list, that's a signal the change belongs upstream in `inspres-a` first, then propagated to the others.

---

## References

- Design parent: [`ilpf_tapisan_v3.html`](../../../../../SpecialProject/KDN-iLPF/ilpf/ilpf_tapisan_v3.html). Read the `<style>` block when in doubt about a recipe.
- Plan: [`this-is-my-prototype-typed-boole.md`](../../../../../../../../Users/User/.claude/plans/this-is-my-prototype-typed-boole.md).
