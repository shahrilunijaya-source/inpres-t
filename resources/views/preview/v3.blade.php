<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>V3 — Notion Warm Light</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #fbf9f6;
    --surface: #ffffff;
    --surface-warm: #f7f3ec;
    --border: #ebe5d9;
    --border-strong: #d6cdb8;
    --text: #2d2419;
    --text-dim: #6b5d47;
    --text-mute: #9b8f78;
    --primary: #7b5fb8;         /* soft purple */
    --primary-soft: #f1ecf9;
    --primary-hover: #654a9c;
    --accent: #e8a44b;          /* warm amber */
    --success: #5b8a5c;
    --warning: #c97612;
    --danger: #a8473e;
    --font: 'Inter', system-ui, sans-serif;
    --mono: 'IBM Plex Mono', monospace;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--bg); color: var(--text); font-family: var(--font); line-height: 1.55; -webkit-font-smoothing: antialiased; min-height: 100vh; }
.header { background: var(--surface-warm); border-bottom: 1px solid var(--border); padding: 18px 32px; display: flex; justify-content: space-between; align-items: center; }
.brand { display: flex; align-items: center; gap: 12px; }
.brand-logo { width: 38px; height: 38px; background: var(--primary); color: #fff; border-radius: 10px; display: grid; place-items: center; font-weight: 700; }
.brand-name { font-weight: 600; font-size: 16px; letter-spacing: -0.005em; }
.brand-sub { font-size: 12px; color: var(--text-mute); }
nav { display: flex; align-items: center; gap: 24px; }
nav a { color: var(--text-dim); text-decoration: none; font-size: 14px; font-weight: 500; }
nav a:hover { color: var(--text); }
.btn { padding: 9px 18px; border-radius: 10px; font-weight: 500; font-size: 14px; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
.btn-primary { background: var(--primary); color: #fff; }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }
.btn-soft { background: var(--surface); color: var(--text); border: 1px solid var(--border-strong); }
.btn-soft:hover { background: var(--surface-warm); }
.container { max-width: 1080px; margin: 0 auto; padding: 60px 32px; }
.hero { max-width: 760px; }
.hero h1 { font-size: 46px; font-weight: 700; letter-spacing: -0.025em; line-height: 1.1; margin-bottom: 18px; }
.hero h1 em { font-style: normal; color: var(--primary); font-weight: 700; }
.hero p { font-size: 17px; color: var(--text-dim); margin-bottom: 28px; max-width: 600px; }
.hero-cta { display: flex; gap: 12px; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: var(--primary-soft); color: var(--primary); border-radius: 6px; font-size: 12px; font-weight: 600; font-family: var(--mono); margin-bottom: 18px; }
.dot { width: 6px; height: 6px; border-radius: 50%; background: var(--primary); }
.cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 64px; }
.card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 24px; transition: all 0.2s; }
.card:hover { border-color: var(--border-strong); box-shadow: 0 8px 24px rgba(123, 95, 184, 0.08); }
.card-tag { display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; background: var(--surface-warm); border: 1px solid var(--border); border-radius: 6px; font-size: 11px; font-weight: 600; font-family: var(--mono); color: var(--text-dim); margin-bottom: 14px; }
.card-emoji { font-size: 32px; margin-bottom: 12px; display: block; }
.card h3 { font-size: 18px; font-weight: 600; margin-bottom: 6px; letter-spacing: -0.01em; }
.card p { font-size: 14px; color: var(--text-dim); margin-bottom: 14px; }
.card-cta { font-size: 13px; font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 4px; }
.metrics { margin-top: 64px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.metric { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 28px; }
.metric-val { font-family: var(--mono); font-size: 36px; font-weight: 600; color: var(--primary); margin-bottom: 6px; letter-spacing: -0.02em; }
.metric-label { font-size: 14px; color: var(--text-dim); }
.note { margin-top: 64px; background: var(--surface-warm); border: 1px solid var(--border); border-radius: 10px; padding: 20px; display: flex; gap: 14px; align-items: center; }
.note-icon { width: 32px; height: 32px; background: var(--accent); color: #fff; border-radius: 8px; display: grid; place-items: center; flex-shrink: 0; font-size: 16px; }
.note p { font-size: 14px; color: var(--text); }
.note strong { font-weight: 600; }
</style>
</head>
<body>
<div class="header">
    <div class="brand">
        <div class="brand-logo">IP</div>
        <div>
            <div class="brand-name">Inpres</div>
            <div class="brand-sub">Portal Rakyat</div>
        </div>
    </div>
    <nav>
        <a href="#">Utama</a>
        <a href="#">Mohon</a>
        <a href="#">Semak Status</a>
        <a href="#" class="btn btn-soft" style="padding: 6px 14px; font-size: 13px;">Officer Login</a>
    </nav>
</div>

<div class="container">
    <div class="hero">
        <div class="chip"><span class="dot"></span> V3 · NOTION WARM</div>
        <h1>Mohon dokumen JPN dengan <em>tenang</em>.<br/>Tanpa kerenah birokrasi.</h1>
        <p>AI auto-isi maklumat dari pengimbasan IC. Status dikemaskini setiap masa. Sijil dihantar terus ke peti masuk anda.</p>
        <div class="hero-cta">
            <a href="#" class="btn btn-primary">Mulakan permohonan →</a>
            <a href="#" class="btn btn-soft">Semak status</a>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <span class="card-emoji">👶</span>
            <div class="card-tag">SLA · 5 hari bekerja</div>
            <h3>Sijil Kelahiran</h3>
            <p>Untuk bayi baharu lahir atau salinan sijil yang hilang.</p>
            <div class="card-cta">Mulakan →</div>
        </div>
        <div class="card">
            <span class="card-emoji">💍</span>
            <div class="card-tag">SLA · 7 hari bekerja</div>
            <h3>Sijil Perkahwinan</h3>
            <p>Pendaftaran perkahwinan atau pengeluaran semula sijil.</p>
            <div class="card-cta">Mulakan →</div>
        </div>
        <div class="card">
            <span class="card-emoji">🪪</span>
            <div class="card-tag">SLA · 14 hari bekerja</div>
            <h3>MyKAD</h3>
            <p>Pembaharuan, penggantian, atau permohonan kali pertama.</p>
            <div class="card-cta">Mulakan →</div>
        </div>
    </div>

    <div class="metrics">
        <div class="metric"><div class="metric-val">90s</div><div class="metric-label">Purata masa hantar borang dengan AI</div></div>
        <div class="metric"><div class="metric-val">73%</div><div class="metric-label">AI boleh tindak tanpa pegawai</div></div>
        <div class="metric"><div class="metric-val">24/7</div><div class="metric-label">Semak status pada bila-bila masa</div></div>
    </div>

    <div class="note">
        <div class="note-icon">!</div>
        <p><strong>Maklumat anda dilindungi.</strong> Inpres mematuhi Akta Perlindungan Data Peribadi 2010 (PDPA).</p>
    </div>
</div>
</body>
</html>
