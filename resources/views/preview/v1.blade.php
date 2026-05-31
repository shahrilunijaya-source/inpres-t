<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>V1 — MyGov Clean</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #f4f6fa;
    --surface: #ffffff;
    --surface-2: #fbfcfe;
    --border: #e0e6ef;
    --border-strong: #c4cee0;
    --text: #0b1733;
    --text-dim: #4b5677;
    --text-mute: #8b95ad;
    --primary: #0033a0;        /* JPN royal blue */
    --primary-soft: #e6ecf8;
    --primary-hover: #002780;
    --accent: #d92f30;          /* JPN red secondary */
    --success: #1a7f3c;
    --warning: #c97612;
    --danger: #b21d1f;
    --font: 'Inter', system-ui, sans-serif;
    --mono: 'IBM Plex Mono', monospace;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--bg); color: var(--text); font-family: var(--font); line-height: 1.5; -webkit-font-smoothing: antialiased; }
.gov-bar { background: var(--primary); color: #fff; padding: 6px 24px; font-size: 12px; }
.gov-bar a { color: #cfdaff; text-decoration: none; margin-left: 16px; }
.header { background: var(--surface); border-bottom: 1px solid var(--border); padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
.brand { display: flex; align-items: center; gap: 12px; }
.brand-logo { width: 40px; height: 40px; background: var(--primary); color: #fff; border-radius: 6px; display: grid; place-items: center; font-weight: 700; }
.brand-name { font-weight: 600; font-size: 16px; }
.brand-sub { font-size: 12px; color: var(--text-mute); }
nav a { color: var(--text-dim); text-decoration: none; margin-left: 24px; font-size: 14px; font-weight: 500; }
nav a:hover { color: var(--primary); }
.btn { padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
.btn-primary { background: var(--primary); color: #fff; }
.btn-primary:hover { background: var(--primary-hover); }
.btn-outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
.container { max-width: 1100px; margin: 0 auto; padding: 60px 24px; }
.hero { text-align: center; }
.hero h1 { font-size: 44px; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 16px; }
.hero h1 span { color: var(--primary); }
.hero p { font-size: 18px; color: var(--text-dim); max-width: 640px; margin: 0 auto 32px; }
.hero-cta { display: flex; gap: 12px; justify-content: center; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: var(--primary-soft); color: var(--primary); border-radius: 999px; font-size: 12px; font-weight: 600; font-family: var(--mono); letter-spacing: 0.04em; margin-bottom: 16px; }
.dot { width: 6px; height: 6px; border-radius: 50%; background: var(--primary); }
.cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 64px; }
.card { background: var(--surface); border: 1px solid var(--border); border-radius: 8px; padding: 24px; transition: all 0.2s; }
.card:hover { border-color: var(--primary); box-shadow: 0 4px 12px rgba(0, 51, 160, 0.08); transform: translateY(-2px); }
.card-icon { width: 40px; height: 40px; background: var(--primary-soft); color: var(--primary); border-radius: 6px; display: grid; place-items: center; margin-bottom: 16px; }
.card h3 { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
.card p { font-size: 14px; color: var(--text-dim); margin-bottom: 16px; }
.card-meta { font-size: 12px; color: var(--text-mute); font-family: var(--mono); padding-top: 12px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; }
.badge-success { background: #e6f4ec; color: var(--success); padding: 2px 8px; border-radius: 4px; font-weight: 600; }
.metrics { margin-top: 64px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; padding: 32px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
.metric-val { font-family: var(--mono); font-size: 36px; font-weight: 700; color: var(--primary); margin-bottom: 4px; }
.metric-label { font-size: 14px; color: var(--text-dim); }
</style>
</head>
<body>
<div class="gov-bar">
    Laman web rasmi Kerajaan Malaysia
    <a href="#">Bahasa Malaysia</a>
    <a href="#">English</a>
</div>
<div class="header">
    <div class="brand">
        <div class="brand-logo">IP</div>
        <div>
            <div class="brand-name">Inpres</div>
            <div class="brand-sub">Portal Rakyat · Jabatan Pendaftaran Negara</div>
        </div>
    </div>
    <nav>
        <a href="#">Utama</a>
        <a href="#">Mohon</a>
        <a href="#">Semak Status</a>
        <a href="#" class="btn btn-outline" style="padding: 6px 14px; font-size: 13px;">Officer Login</a>
    </nav>
</div>

<div class="container">
    <div class="hero">
        <div class="chip"><span class="dot"></span> V1 · MYGOV CLEAN</div>
        <h1>Mohon dokumen JPN.<br/><span>Tiada barisan. Tiada borang kertas.</span></h1>
        <p>Daftar kelahiran, perkahwinan, atau MyKAD secara dalam talian. AI auto-isi maklumat anda dari pengimbasan IC. Status dikemaskini secara langsung.</p>
        <div class="hero-cta">
            <a href="#" class="btn btn-primary">Mulakan Permohonan →</a>
            <a href="#" class="btn btn-outline">Semak Status</a>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <div class="card-icon">✓</div>
            <h3>Sijil Kelahiran</h3>
            <p>Untuk bayi baharu lahir atau salinan sijil yang hilang.</p>
            <div class="card-meta"><span>SLA</span><span class="badge-success">5 hari bekerja</span></div>
        </div>
        <div class="card">
            <div class="card-icon">♥</div>
            <h3>Sijil Perkahwinan</h3>
            <p>Pendaftaran perkahwinan atau pengeluaran semula sijil.</p>
            <div class="card-meta"><span>SLA</span><span class="badge-success">7 hari bekerja</span></div>
        </div>
        <div class="card">
            <div class="card-icon">▢</div>
            <h3>MyKAD</h3>
            <p>Pembaharuan, penggantian, atau permohonan kali pertama.</p>
            <div class="card-meta"><span>SLA</span><span class="badge-success">14 hari bekerja</span></div>
        </div>
    </div>

    <div class="metrics">
        <div><div class="metric-val">90s</div><div class="metric-label">Purata masa hantar borang dengan AI</div></div>
        <div><div class="metric-val">73%</div><div class="metric-label">AI boleh tindak tanpa pegawai</div></div>
        <div><div class="metric-val">24/7</div><div class="metric-label">Semak status pada bila-bila masa</div></div>
    </div>
</div>
</body>
</html>
