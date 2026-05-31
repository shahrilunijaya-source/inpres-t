<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log Masuk · InPreS</title>
    @vite(['resources/css/system.css'])
</head>
<body class="system">

<div class="vb-page">

    {{-- ============ LEFT — Pine-deep editorial ============ --}}
    <div class="vb-left">
        <div>
            <div class="vb-left__top">
                <div class="wm"><span class="i">I</span>nPreS<span class="dot"></span></div>
                <div class="meta">RUANG PEGAWAI · v1</div>
            </div>
        </div>

        <div>
            <h2 class="vb-left__hero">
                <span class="line">Dibina untuk</span>
                <span class="line"><span class="accent">meluluskan</span>.</span>
                <span class="line">Direka untuk</span>
                <span class="line">dikeluarkan.<span class="dot dot--lg"></span></span>
            </h2>
            <p class="vb-left__lede">
                Ruang kerja Pegawai Pendaftaran Negara. Tenang. Padat. Decisif. Setiap permohonan, satu klik daripada selesai.
            </p>

            <div class="vb-left__decisive">
                <div class="vb-decisive"><span class="word">Disahkan</span>.</div>
                <div class="vb-decisive"><span class="word">Diluluskan</span>.</div>
                <div class="vb-decisive"><span class="word">Dikeluarkan</span>.</div>
            </div>
        </div>

        <div class="vb-left__foot">
            <span class="stamp">Jabatan Pendaftaran Negara · KDN</span>
            <span>Sesi disulitkan TLS 1.3</span>
        </div>
    </div>

    {{-- ============ RIGHT — White form ============ --}}
    <div class="vb-right">
        <div class="vb-right__inner">
            <div class="swap__screen">
                <div class="vb-right__head">
                    <div>
                        <div class="eyebrow" style="margin-bottom: 6px;">Akses Pegawai</div>
                        <h1 class="vb-h1">Log masuk.<span class="dot"></span></h1>
                    </div>
                </div>
                <p class="vb-sub">Masukkan emel rasmi JPN dan kata laluan anda untuk meneruskan.</p>

                <form class="va-form" method="POST" action="{{ route('system.login.attempt') }}">
                    @csrf

                    <div class="field">
                        <label class="field__label">Emel JPN</label>
                        <input
                            type="email"
                            name="email"
                            class="field__input"
                            placeholder="pegawai@jpn.gov.my"
                            value="{{ old('email', 'demo@jpn.gov.my') }}"
                            autofocus
                            required>
                    </div>

                    <div class="field">
                        <label class="field__label">Kata Laluan</label>
                        <div class="field__row">
                            <input
                                type="password"
                                name="password"
                                id="passwordField"
                                class="field__input"
                                placeholder="••••••••"
                                value="password"
                                required>
                            <button type="button" class="field__eye" onclick="
                                const f = document.getElementById('passwordField');
                                const isText = f.type === 'text';
                                f.type = isText ? 'password' : 'text';
                            ">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="va-form__row">
                        <label style="color: var(--mute); display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                            <input type="checkbox" name="remember" style="margin: 0;">
                            <span>Ingat saya</span>
                        </label>
                        <a href="#">Lupa kata laluan?</a>
                    </div>

                    @if ($errors->any())
                        <div class="formerr">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button type="submit" class="btn btn--primary btn--block">
                        Log Masuk
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                    </button>
                </form>

                <div style="margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                    <button type="button" class="demo-trigger" id="demoTrigger">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Lihat akaun demo
                    </button>
                    <a href="{{ route('home') }}" style="color: var(--mute); text-decoration: none; font-size: 11px;">← Portal awam</a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ============ DEMO USERS MODAL ============ --}}
<div class="demo-modal" id="demoModal" role="dialog" aria-modal="true" aria-labelledby="demoModalTitle">
    <div class="demo-modal__card">
        <button type="button" class="demo-modal__close" id="demoModalClose" aria-label="Tutup">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <div class="demo-modal__head">
            <div class="demo-modal__eyebrow">Akaun Demo · Prototaip</div>
            <h2 class="demo-modal__h" id="demoModalTitle">3 peringkat pengguna · 5 akaun ujian<span class="dot"></span></h2>
            <p class="demo-modal__sub">
                Sistem ini menyokong 3 peringkat akses dengan kebenaran berbeza. Klik mana-mana akaun di bawah untuk mengisi borang log masuk secara automatik.
            </p>
        </div>

        <div class="demo-modal__body">
            <div class="demo-tiers">

                {{-- ==== OFFICER ==== --}}
                <div class="demo-tier demo-tier--officer">
                    <div class="demo-tier__head">
                        <div class="demo-tier__badge">P1</div>
                        <div>
                            <div class="demo-tier__title">Pegawai</div>
                            <div class="demo-tier__role">Officer · L1</div>
                        </div>
                    </div>

                    <div class="demo-tier__caps">
                        <div class="demo-tier__caps-label">Kebenaran</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Semak permohonan individu</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Lulus / tolak permohonan</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Lihat saluran tugasan sendiri</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Daftar permohonan baru</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Log audit (tindakan sendiri)</div>
                    </div>

                    <div class="demo-tier__accounts">
                        <div class="demo-tier__accounts-label">Akaun ujian (3)</div>

                        <button type="button" class="demo-tier__btn js-demo-login" data-email="demo@jpn.gov.my">
                            <div class="demo-tier__btn-body">
                                <div class="demo-tier__btn-name">Demo Officer <span style="color: var(--teal); font-size: 10px; margin-left: 4px; font-weight: 700; letter-spacing: 0.08em;">UTAMA</span></div>
                                <div class="demo-tier__btn-email">demo@jpn.gov.my</div>
                            </div>
                            <svg class="demo-tier__btn-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                        </button>

                        <button type="button" class="demo-tier__btn js-demo-login" data-email="aisyah@jpn.gov.my">
                            <div class="demo-tier__btn-body">
                                <div class="demo-tier__btn-name">Aisyah binti Yusof</div>
                                <div class="demo-tier__btn-email">aisyah@jpn.gov.my</div>
                            </div>
                            <svg class="demo-tier__btn-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                        </button>

                        <button type="button" class="demo-tier__btn js-demo-login" data-email="hafiz@jpn.gov.my">
                            <div class="demo-tier__btn-body">
                                <div class="demo-tier__btn-name">Hafiz bin Razak</div>
                                <div class="demo-tier__btn-email">hafiz@jpn.gov.my</div>
                            </div>
                            <svg class="demo-tier__btn-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ==== SUPERVISOR ==== --}}
                <div class="demo-tier demo-tier--supervisor">
                    <div class="demo-tier__head">
                        <div class="demo-tier__badge">P2</div>
                        <div>
                            <div class="demo-tier__title">Penyelia</div>
                            <div class="demo-tier__role">Supervisor · L2</div>
                        </div>
                    </div>

                    <div class="demo-tier__caps">
                        <div class="demo-tier__caps-label">Tambahan kepada Pegawai</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Operasi lulus pukal (bulk)</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Tugaskan semula kes</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Pantau prestasi pasukan</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Atas tangani keputusan</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Akses laporan unit</div>
                    </div>

                    <div class="demo-tier__accounts">
                        <div class="demo-tier__accounts-label">Akaun ujian (1)</div>

                        <button type="button" class="demo-tier__btn js-demo-login" data-email="nurul@jpn.gov.my">
                            <div class="demo-tier__btn-body">
                                <div class="demo-tier__btn-name">Nurul Iman</div>
                                <div class="demo-tier__btn-email">nurul@jpn.gov.my</div>
                            </div>
                            <svg class="demo-tier__btn-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ==== ADMIN ==== --}}
                <div class="demo-tier demo-tier--admin">
                    <div class="demo-tier__head">
                        <div class="demo-tier__badge">P3</div>
                        <div>
                            <div class="demo-tier__title">Pentadbir</div>
                            <div class="demo-tier__role">Admin · L3</div>
                        </div>
                    </div>

                    <div class="demo-tier__caps">
                        <div class="demo-tier__caps-label">Tambahan kepada Penyelia</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Pengurusan pengguna</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Peranan & kebenaran</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Tetapan sistem & API</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Audit log penuh (kekal)</div>
                        <div class="demo-tier__cap"><span class="demo-tier__cap-tick">✓</span> Sandaran & pemulihan</div>
                    </div>

                    <div class="demo-tier__accounts">
                        <div class="demo-tier__accounts-label">Akaun ujian (1)</div>

                        <button type="button" class="demo-tier__btn js-demo-login" data-email="ibrahim@jpn.gov.my">
                            <div class="demo-tier__btn-body">
                                <div class="demo-tier__btn-name">Ibrahim Pentadbir</div>
                                <div class="demo-tier__btn-email">ibrahim@jpn.gov.my</div>
                            </div>
                            <svg class="demo-tier__btn-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="demo-modal__foot">
            <span class="demo-modal__foot-pwd">
                Kata laluan untuk semua akaun: <code>password</code>
            </span>
            <span style="font-size: 11px; color: var(--mute-2); letter-spacing: 0.04em;">
                Hanya untuk prototaip · Pengeluaran akan guna MFA + SSO
            </span>
        </div>
    </div>
</div>

<script>
    const demoTrigger = document.getElementById('demoTrigger');
    const demoModal = document.getElementById('demoModal');
    const demoModalClose = document.getElementById('demoModalClose');

    demoTrigger?.addEventListener('click', () => {
        demoModal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    });

    function closeDemoModal() {
        demoModal.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    demoModalClose?.addEventListener('click', closeDemoModal);
    demoModal?.addEventListener('click', (e) => {
        if (e.target === demoModal) closeDemoModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && demoModal.classList.contains('is-open')) closeDemoModal();
    });

    // Click an account → fill email + close
    document.querySelectorAll('.js-demo-login').forEach(btn => {
        btn.addEventListener('click', () => {
            const email = btn.dataset.email;
            const emailField = document.querySelector('input[name="email"]');
            const passwordField = document.querySelector('input[name="password"]');
            if (emailField) emailField.value = email;
            if (passwordField) passwordField.value = 'password';
            closeDemoModal();
            // Focus submit so user can press Enter
            document.querySelector('button[type="submit"].btn--primary')?.focus();
        });
    });
</script>

</body>
</html>
