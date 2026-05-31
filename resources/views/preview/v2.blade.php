<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>V2 — Stripe-Light SaaS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #fafbfc;
    --surface: #ffffff;
    --surface-soft: #f6f9fc;
    --border: #e6ebf1;
    --border-strong: #d4dce5;
    --text: #0a2540;
    --text-dim: #425466;
    --text-mute: #8898aa;
    --primary: #635bff;        /* Stripe indigo */
    --primary-soft: #efeeff;
    --primary-hover: #4f47e0;
    --accent: #00d4ff;
    --success: #1bb574;
    --warning: #f79e1b;
    --danger: #df1b41;
    --font: 'Inter', system-ui, sans-serif;
    --mono: 'IBM Plex Mono', monospace;
    --shadow-sm: 0 2px 5px rgba(50,50,93,0.08), 0 1px 2px rgba(0,0,0,0.05);
    --shadow-md: 0 7px 14px rgba(50,50,93,0.10), 0 3px 6px rgba(0,0,0,0.07);
    --shadow-lg: 0 13px 27px rgba(50,50,93,0.14), 0 8px 16px rgba(0,0,0,0.08);
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--bg); color: var(--text); font-family: var(--font); line-height: 1.55; -webkit-font-smoothing: antialiased; min-height: 100vh; background-image: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99,91,255,0.10), transparent 60%); background-attachment: fixed; }
.header { padding: 20px 32px; display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; }
.brand { display: flex; align-items: center; gap: 12px; }
.brand-logo { width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary) 0%, #00d4ff 100%); color: #fff; border-radius: 8px; display: grid; place-items: center; font-weight: 800; box-shadow: 0 4px 12px rgba(99,91,255,0.30); }
.brand-name { font-weight: 700; font-size: 17px; letter-spacing: -0.01em; }
.brand-sub { font-size: 12px; color: var(--text-mute); }
nav { display: flex; align-items: center; gap: 28px; }
nav a { color: var(--text-dim); text-decoration: none; font-size: 14px; font-weight: 500; }
nav a:hover { color: var(--text); }
.btn { padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.18s cubic-bezier(0.34,1.56,0.64,1); }
.btn-primary { background: linear-gradient(135deg, var(--primary) 0%, #4f47e0 100%); color: #fff; box-shadow: 0 4px 14px rgba(99,91,255,0.35); }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(99,91,255,0.45); }
.btn-ghost { background: var(--surface); color: var(--text); border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
.btn-ghost:hover { background: var(--surface-soft); color: var(--text); border-color: var(--border-strong); box-shadow: var(--shadow-md); transform: translateY(-1px); }
.container { max-width: 1100px; margin: 0 auto; padding: 60px 32px; }
.hero { text-align: center; }
.hero h1 { font-size: 56px; font-weight: 800; letter-spacing: -0.03em; line-height: 1.05; margin-bottom: 20px; }
.hero h1 span { background: linear-gradient(135deg, var(--primary) 0%, #00d4ff 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
.hero p { font-size: 19px; color: var(--text-dim); max-width: 680px; margin: 0 auto 36px; }
.hero-cta { display: flex; gap: 12px; justify-content: center; }
.chip { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; background: var(--primary-soft); color: var(--primary); border-radius: 999px; font-size: 12px; font-weight: 600; font-family: var(--mono); letter-spacing: 0.04em; margin-bottom: 24px; }
.dot { width: 6px; height: 6px; border-radius: 50%; background: var(--primary); animation: pulse 2s ease-in-out infinite; }
@keyframes pulse { 50% { opacity: 0.5; transform: scale(1.4); } }
.cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 80px; }
.card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 28px; box-shadow: var(--shadow-sm); transition: all 0.2s cubic-bezier(0.34,1.56,0.64,1); cursor: pointer; }
.card:hover { box-shadow: var(--shadow-lg); transform: translateY(-3px); border-color: var(--primary); }
.card-icon { width: 44px; height: 44px; background: var(--primary-soft); color: var(--primary); border-radius: 10px; display: grid; place-items: center; margin-bottom: 18px; font-size: 18px; }
.card h3 { font-size: 19px; font-weight: 700; letter-spacing: -0.01em; margin-bottom: 8px; }
.card p { font-size: 14px; color: var(--text-dim); margin-bottom: 18px; }
.card-cta { font-size: 14px; font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 6px; }
.metrics { margin-top: 80px; background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 40px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; box-shadow: var(--shadow-md); }
.metric-val { font-family: var(--mono); font-size: 42px; font-weight: 700; background: linear-gradient(135deg, var(--primary) 0%, #00d4ff 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 6px; letter-spacing: -0.02em; }
.metric-label { font-size: 14px; color: var(--text-dim); }
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
        <a href="#" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">Officer Login</a>
    </nav>
</div>

<div class="container">
    <div class="hero">
        <div class="chip"><span class="dot"></span> V2 · STRIPE-LIGHT</div>
        <h1>Mohon dokumen JPN.<br/><span>90 saat. Sifar kertas.</span></h1>
        <p>AI auto-isi 80% borang dari pengimbasan IC. Status dikemaskini secara langsung. Sijil dijana automatik selepas kelulusan pegawai.</p>
        <div class="hero-cta">
            <a href="#" class="btn btn-primary">Mulakan Permohonan →</a>
            <a href="#" class="btn btn-ghost">Semak Status</a>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <div class="card-icon">✓</div>
            <h3>Sijil Kelahiran</h3>
            <p>Untuk bayi baharu lahir atau salinan sijil yang hilang.</p>
            <div class="card-cta">Mohon sekarang →</div>
        </div>
        <div class="card">
            <div class="card-icon">♥</div>
            <h3>Sijil Perkahwinan</h3>
            <p>Pendaftaran perkahwinan atau pengeluaran semula sijil.</p>
            <div class="card-cta">Mohon sekarang →</div>
        </div>
        <div class="card">
            <div class="card-icon">▢</div>
            <h3>MyKAD</h3>
            <p>Pembaharuan, penggantian, atau permohonan kali pertama.</p>
            <div class="card-cta">Mohon sekarang →</div>
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
