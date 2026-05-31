@extends('layouts.system', ['active' => 'biometric', 'title' => 'Penangkapan Biometrik'])

@section('content')
@php
    $threaded = !empty($case['threaded']);
    $bio = $case['biometric'] ?? ['counter' => 'K-04 Putrajaya', 'officer' => 'Pn. Faridah · OFC-2031', 'nfiq' => 96, 'duration' => '2 min 47s', 'started' => '20:38:11', 'note' => 'Penangkapan biometrik standard.'];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · APPENDIX C · Perkakasan Wajib</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Penangkapan Biometrik · 10 Cap Jari + Muka + Iris</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">@if($threaded)Sesi aktif: {{ $case['name'] }} · {{ $case['doc_label'] }} · {{ $bio['counter'] }}@else Stesen biometrik · {{ $bio['counter'] }} · tiada sesi aktif @endif</p>
    </header>

    @unless($threaded)
        {{-- Browse mode (sidebar): no active subject — a capture session is opened from a specific application --}}
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 40px; text-align: center; margin-bottom: 18px;">
            <div style="font-size: 15px; font-weight: 700; color: var(--ink-navy);">Tiada sesi biometrik aktif</div>
            <div style="font-size: 13px; color: #6B7280; margin-top: 6px; max-width: 520px; margin-left: auto; margin-right: auto;">Sesi penangkapan biometrik dibuka untuk satu permohonan tertentu. Pilih permohonan dari <strong>Senarai Tapisan</strong> dan tekan “Semak” untuk mula.</div>
            <a href="{{ route('system.tapisan') }}" style="display: inline-block; margin-top: 14px; text-decoration: none; background: var(--ink-navy); color: #fff; padding: 11px 20px; border-radius: 9px; font-size: 13px; font-weight: 700;">Ke Senarai Tapisan →</a>
        </div>
    @endunless

    @php
        // Reusable fingerprint ridge motif (faint until a cell is scanned).
        $fpSvg = '<svg class="bio-fp" viewBox="0 0 40 48" width="60%" height="60%" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">'
            . '<path d="M20 7 C12 7 8 13 8 21"/><path d="M20 11 C14 11 12 16 12 22"/><path d="M20 15 C16 15 15 19 15 23"/><path d="M20 19 C18 19 18 21 18 24"/>'
            . '<path d="M20 7 C28 7 32 13 32 23 C32 31 28 37 24 43"/><path d="M20 11 C26 11 28 16 28 23 C28 30 25 35 22 41"/><path d="M20 15 C24 15 25 19 25 24 C25 29 23 33 21 39"/>'
            . '<path d="M8 25 C8 31 10 36 13 41"/><path d="M12 27 C12 32 14 36 16 40"/></svg>';
        $rightFingers = [['Ibu Jari',97],['Telunjuk',99],['Hantar',95],['Manis',92],['Kelingking',88]];
        $leftFingers  = [['Ibu Jari',96],['Telunjuk',98],['Hantar',94],['Manis',90],['Kelingking',87]];
    @endphp

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 18px; {{ $threaded ? '' : 'opacity:.5; pointer-events:none;' }}">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <h3 style="margin: 0; font-size: 14px;">10 Cap Jari · ISO/IEC 19794-2</h3>
                <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">● Pengimbas Sambung</span>
            </div>

            {{-- Interactive scan controls --}}
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin: 10px 0 14px;">
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; font-size: 11px; color: #6B7280; margin-bottom: 4px;">
                        <span>Tap setiap petak untuk imbas</span>
                        <span><strong id="bioCount">0</strong> / 12 sampel</span>
                    </div>
                    <div style="height: 7px; background: #E5E7EB; border-radius: 999px; overflow: hidden;">
                        <div id="bioBar" style="height: 100%; width: 0; background: linear-gradient(90deg,#16A34A,#22C55E); transition: width .3s;"></div>
                    </div>
                </div>
                <button type="button" onclick="bioScanAll()" style="background: #1E3A8A; color: #fff; border: 0; padding: 9px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">Imbas Semua</button>
                <button type="button" onclick="bioReset()" style="background: #fff; color: #475569; border: 1px solid #E5E7EB; padding: 9px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;">Set Semula</button>
            </div>

            <div class="bio-grid">
                @foreach(array_merge(
                    array_map(fn($f) => ['Kanan '.$f[0], $f[1]], $rightFingers),
                    array_map(fn($f) => ['Kiri '.$f[0], $f[1]], $leftFingers)
                ) as $cell)
                    <button type="button" class="bio-cell" data-q="{{ $cell[1] }}" onclick="bioScan(this)">
                        <span class="bio-cell__surface">
                            {!! $fpSvg !!}
                            <span class="bio-scanline"></span>
                            <span class="bio-hint">Tap imbas</span>
                            <span class="bio-spin"></span>
                            <span class="bio-check">✓</span>
                        </span>
                        <span class="bio-cell__label">{{ $cell[0] }}</span>
                        <span class="bio-cell__q">Q: <span class="v">{{ $cell[1] }}</span></span>
                    </button>
                @endforeach
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 18px;">
                {{-- Face --}}
                <div style="border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px;">
                    <div style="font-size: 11px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">Tangkapan Muka</div>
                    <button type="button" class="bio-cell bio-cell--media" data-q="{{ $bio['nfiq'] }}" onclick="bioScan(this)" style="width: 100%;">
                        <span class="bio-cell__surface" style="aspect-ratio: 1; background: linear-gradient(135deg,#E8EEF6,#CDDDF0);">
                            <span class="bio-media">@include('system._mykad-photo', ['ic' => $case['ic'] ?? '', 'name' => $case['name'] ?? '', 'shape' => 'circle', 'size' => 116, 'gender' => $case['gender'] ?? 'F'])</span>
                            <span class="bio-scanline"></span>
                            <span class="bio-hint">Tap imbas muka</span>
                            <span class="bio-spin"></span>
                            <span class="bio-check">✓</span>
                        </span>
                    </button>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-top: 8px;"><span>Liveness PAD</span><span class="bio-badge">—</span></div>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-top: 4px;"><span>Quality NFIQ</span><span class="bio-badge" data-show="{{ $bio['nfiq'] }}">—</span></div>
                </div>
                {{-- Iris --}}
                <div style="border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px;">
                    <div style="font-size: 11px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">Imbasan Iris</div>
                    <button type="button" class="bio-cell bio-cell--media" data-q="98" onclick="bioScan(this)" style="width: 100%;">
                        <span class="bio-cell__surface" style="aspect-ratio: 1; background: radial-gradient(circle,#1E3A8A 0%,var(--ink-navy) 55%,#000 100%);">
                            <span class="bio-media">
                                <svg viewBox="0 0 100 100" width="62%" height="62%">
                                    <circle cx="50" cy="50" r="46" fill="none" stroke="#3B82F6" stroke-width="1.5" opacity=".5"/>
                                    <circle cx="50" cy="50" r="34" fill="url(#ir)"/>
                                    <circle cx="50" cy="50" r="14" fill="#04060d"/>
                                    <circle cx="44" cy="44" r="4" fill="#dbeafe" opacity=".85"/>
                                    <defs><radialGradient id="ir"><stop offset="0%" stop-color="#1e3a8a"/><stop offset="60%" stop-color="#1d4ed8"/><stop offset="100%" stop-color="var(--ink-navy)"/></radialGradient></defs>
                                    @for($k = 0; $k < 36; $k++)
                                        <line x1="50" y1="50" x2="{{ 50 + 33 * cos(deg2rad($k * 10)) }}" y2="{{ 50 + 33 * sin(deg2rad($k * 10)) }}" stroke="#60a5fa" stroke-width="0.6" opacity=".4"/>
                                    @endfor
                                </svg>
                            </span>
                            <span class="bio-scanline"></span>
                            <span class="bio-hint" style="color:#cbd5e1;">Tap imbas iris</span>
                            <span class="bio-spin"></span>
                            <span class="bio-check">✓</span>
                        </span>
                    </button>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-top: 8px;"><span>Mata Kiri</span><span class="bio-badge" data-show="512 byte">—</span></div>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-top: 4px;"><span>Mata Kanan</span><span class="bio-badge" data-show="512 byte">—</span></div>
                </div>
            </div>
        </section>

        <aside>
            <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px; margin-bottom: 14px;">
                <h3 style="margin: 0 0 10px; font-size: 14px;">Sesi Aktif</h3>
                <table style="width: 100%; font-size: 12px;">
                    <tr><td style="color: #6B7280; padding: 4px 0;">No. Kaunter</td><td style="font-weight: 700; text-align: right;">{{ $bio['counter'] }}</td></tr>
                    <tr><td style="color: #6B7280; padding: 4px 0;">Pegawai</td><td style="text-align: right;">{{ $bio['officer'] }}</td></tr>
                    <tr><td style="color: #6B7280; padding: 4px 0;">Subjek</td><td style="text-align: right; font-weight: 700;">{{ $case['name'] }}</td></tr>
                    <tr><td style="color: #6B7280; padding: 4px 0;">Permohonan</td><td style="text-align: right;">{{ $case['doc_label'] }}</td></tr>
                    <tr><td style="color: #6B7280; padding: 4px 0;">Mula</td><td style="text-align: right;">{{ $bio['started'] }}</td></tr>
                    <tr><td style="color: #6B7280; padding: 4px 0;">Tempoh</td><td style="font-weight: 700; text-align: right;">{{ $bio['duration'] }}</td></tr>
                </table>
                @if(!empty($bio['note']))
                    <div style="margin-top: 10px; font-size: 11px; color: #475569; background: #F8FAFC; border-radius: 6px; padding: 8px 10px;">{{ $bio['note'] }}</div>
                @endif
            </div>

            <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px; margin-bottom: 14px;">
                <h3 style="margin: 0 0 10px; font-size: 14px;">Status Perkakasan</h3>
                @foreach([['Pengimbas 10 Cap Jari','Suprema RealScan-G10'],['Kamera Muka','Logitech Brio 500'],['Pengimbas Iris','IriShield MK2120UL'],['Pembaca MyKad','HID Omnikey 3121']] as $dev)
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #F1F5F9; font-size: 12px;">
                        <div>
                            <div style="font-weight: 600;">{{ $dev[0] }}</div>
                            <div style="color: #6B7280; font-size: 10.5px;">{{ $dev[1] }}</div>
                        </div>
                        <span style="background: #DCFCE7; color: #15803D; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; height: fit-content;">● Aktif</span>
                    </div>
                @endforeach
            </div>

            <div id="bioReady" style="background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 10px; padding: 14px 18px; transition: .3s;">
                <div id="bioReadyTitle" style="font-weight: 700; color: #64748B; margin-bottom: 4px;">Menunggu imbasan…</div>
                <div id="bioReadySub" style="font-size: 12px; color: #94A3B8;">Imbas 10 cap jari + muka + iris untuk teruskan ke ABIS 1:N.</div>
                <a id="bioAbisBtn" href="{{ route('system.abis', ['ref' => $case['reference']]) }}"
                   style="display: block; text-align: center; text-decoration: none; margin-top: 10px; width: 100%; background: #CBD5E1; color: #fff; border: 0; padding: 10px; border-radius: 8px; font-weight: 700; box-sizing: border-box; pointer-events: none; cursor: not-allowed;"
                   onclick="if(window.__bioLocked!==false){event.preventDefault();}">Hantar ke ABIS →</a>
            </div>
        </aside>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi LAMPIRAN A Para 2.1(viii) + APPENDIX C:</strong>
        <span style="color: #475569; font-size: 13px;"> 9 perkakasan biometrik wajib waranti 5 tahun. ISO/IEC 19794 (data interchange) + ISO/IEC 30107 PAD (anti-spoofing). Sokong Modul Kelahiran, MyKad, Kewarganegaraan, Anak Angkat.</span>
    </div>
</div>

<style>
    .bio-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
    .bio-cell { appearance: none; background: none; border: 0; padding: 0; cursor: pointer; text-align: center; font: inherit; }
    .bio-cell__surface { position: relative; aspect-ratio: 1; border-radius: 8px; border: 2px dashed #94A3B8; background: linear-gradient(135deg,#EEF2F7,#E2E8F0); display: flex; align-items: center; justify-content: center; overflow: hidden; transition: border-color .2s, background .3s; color: #1E3A8A; }
    .bio-cell:hover .bio-cell__surface { border-color: #1E3A8A; }
    .bio-fp { opacity: .14; transition: opacity .4s; }
    .bio-media { opacity: .16; filter: grayscale(1); transition: opacity .4s, filter .4s; display: flex; align-items: center; justify-content: center; }
    .bio-cell.is-done .bio-fp { opacity: 1; }
    .bio-cell.is-done .bio-media { opacity: 1; filter: none; }
    .bio-cell.is-done .bio-cell__surface { border: 2px solid #16A34A; background: linear-gradient(135deg,#DBEAFE,#BFDBFE); }
    .bio-cell--media.is-done .bio-cell__surface { background: linear-gradient(135deg,#E8EEF6,#CDDDF0); }
    .bio-hint { position: absolute; bottom: 6px; left: 0; right: 0; font-size: 10px; color: #64748B; }
    .bio-cell.is-done .bio-hint, .bio-cell.is-scanning .bio-hint { display: none; }
    .bio-check { position: absolute; top: 4px; right: 4px; width: 17px; height: 17px; border-radius: 50%; background: #16A34A; color: #fff; font-size: 10px; line-height: 17px; display: none; }
    .bio-cell.is-done .bio-check { display: block; }
    .bio-scanline { position: absolute; left: 0; right: 0; top: -30%; height: 26%; background: linear-gradient(180deg, rgba(34,197,94,0), rgba(34,197,94,.55), rgba(34,197,94,0)); display: none; }
    .bio-cell.is-scanning .bio-scanline { display: block; animation: bioScanMove .85s linear; }
    .bio-cell.is-scanning .bio-cell__surface { border-color: #16A34A; box-shadow: 0 0 0 3px rgba(34,197,94,.18); }
    .bio-spin { position: absolute; width: 22px; height: 22px; border: 2.5px solid rgba(34,197,94,.3); border-top-color: #16A34A; border-radius: 50%; display: none; }
    .bio-cell.is-scanning .bio-spin { display: block; animation: bioSpin .6s linear infinite; }
    .bio-cell__label { display: block; font-size: 11px; font-weight: 600; margin-top: 6px; }
    .bio-cell__q { display: inline-block; margin-top: 2px; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; background: #F1F5F9; color: #94A3B8; opacity: .4; transition: .3s; }
    .bio-cell.is-done .bio-cell__q { opacity: 1; background: #DCFCE7; color: #15803D; }
    .bio-badge { background: #F1F5F9; color: #94A3B8; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .bio-badge.is-on { background: #DCFCE7; color: #15803D; }
    @keyframes bioScanMove { from { top: -30%; } to { top: 104%; } }
    @keyframes bioSpin { to { transform: rotate(360deg); } }
</style>

<script>
(function () {
    var TOTAL = document.querySelectorAll('.bio-cell').length; // 12
    window.__bioLocked = true;

    function refresh() {
        var done = document.querySelectorAll('.bio-cell.is-done').length;
        var cnt = document.getElementById('bioCount');
        var bar = document.getElementById('bioBar');
        if (cnt) cnt.textContent = done;
        if (bar) bar.style.width = (done / TOTAL * 100) + '%';

        if (done >= TOTAL) {
            window.__bioLocked = false;
            var box = document.getElementById('bioReady');
            var btn = document.getElementById('bioAbisBtn');
            var t = document.getElementById('bioReadyTitle');
            var s = document.getElementById('bioReadySub');
            if (box) { box.style.background = '#ECFDF5'; box.style.borderColor = '#22C55E'; box.style.borderStyle = 'solid'; }
            if (t) { t.textContent = 'Sedia untuk ABIS 1:N'; t.style.color = '#15803D'; }
            if (s) { s.textContent = 'Semua 12 sampel lulus quality check.'; }
            if (btn) { btn.style.background = 'var(--ink-navy)'; btn.style.pointerEvents = 'auto'; btn.style.cursor = 'pointer'; }
        }
    }

    window.bioScan = function (cell) {
        if (cell.classList.contains('is-done') || cell.classList.contains('is-scanning')) return;
        cell.classList.add('is-scanning');
        setTimeout(function () {
            cell.classList.remove('is-scanning');
            cell.classList.add('is-done');
            // reveal sibling badges for media cells (face / iris)
            if (cell.classList.contains('bio-cell--media')) {
                var card = cell.parentElement;
                card.querySelectorAll('.bio-badge').forEach(function (b) {
                    b.classList.add('is-on');
                    b.textContent = b.dataset.show || 'PASS';
                });
            }
            refresh();
        }, 850);
    };

    window.bioScanAll = function () {
        var pending = document.querySelectorAll('.bio-cell:not(.is-done):not(.is-scanning)');
        pending.forEach(function (c, i) { setTimeout(function () { window.bioScan(c); }, i * 220); });
    };

    window.bioReset = function () {
        document.querySelectorAll('.bio-cell').forEach(function (c) { c.classList.remove('is-done', 'is-scanning'); });
        document.querySelectorAll('.bio-badge').forEach(function (b) { b.classList.remove('is-on'); b.textContent = '—'; });
        window.__bioLocked = true;
        var box = document.getElementById('bioReady');
        var btn = document.getElementById('bioAbisBtn');
        var t = document.getElementById('bioReadyTitle');
        var s = document.getElementById('bioReadySub');
        if (box) { box.style.background = '#F8FAFC'; box.style.borderColor = '#CBD5E1'; box.style.borderStyle = 'dashed'; }
        if (t) { t.textContent = 'Menunggu imbasan…'; t.style.color = '#64748B'; }
        if (s) { s.textContent = 'Imbas 10 cap jari + muka + iris untuk teruskan ke ABIS 1:N.'; }
        if (btn) { btn.style.background = '#CBD5E1'; btn.style.pointerEvents = 'none'; btn.style.cursor = 'not-allowed'; }
        refresh();
    };
})();
</script>
@endsection
