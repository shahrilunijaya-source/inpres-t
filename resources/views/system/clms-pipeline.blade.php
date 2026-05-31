@extends('layouts.system', ['active' => 'clms', 'title' => 'CLMS · Kitar Hayat Kad'])

@section('content')
@php
$threaded = !empty($case['threaded']);
$stages = [
    ['Stok Kosong', 84218, 'Kad polikarbonat belum diprogram', '#F1F5F9', '#475569'],
    ['Baris Gilir Cetakan', 421, 'Menunggu personalisasi', '#FEF3C7', '#B45309'],
    ['Personalisasi', 12, 'Laser engrave + program cip', '#FEF3C7', '#B45309'],
    ['Suntikan Kunci PKI', 8, 'Pasang kunci kriptografi PKI', '#FEF3C7', '#B45309'],
    ['Kawalan Mutu', 4, 'Ujian visual + baca cip', '#FEF3C7', '#B45309'],
    ['Sedia Serahan', 287, 'Menunggu pemohon', '#DCFCE7', '#15803D'],
    ['Diserah', 4821, 'Bulan ini', '#DCFCE7', '#15803D'],
];
$caseQueue = [];
if ($threaded && !empty($case['clms'])) {
    $cl = $case['clms'];
    $caseQueue[] = [$cl['serial'], $case['name'], $cl['type'], $cl['stage'], $cl['eta'], $cl['priority'], true];
}
$queue = array_merge($caseQueue, [
    ['MK-2026-987654', 'Arjun a/l Subramaniam', 'Gantian Rosak', 'Cetakan', '12 min', 'normal'],
    ['MK-2026-987655', 'Siti Aminah binti Hasan', 'Kali Pertama 12T', 'Suntikan Kunci', '5 min', 'normal'],
    ['MK-2026-987656', 'Raj Kumar a/l Suresh', 'MyPR', 'Kawalan Mutu', '3 min', 'high'],
    ['MK-2026-987657', 'John Tan Wei Ming', 'Penukaran Nama', 'Personalisasi', '8 min', 'normal'],
    ['MK-2026-987658', 'Faridah binti Salleh', 'Gantian Hilang', 'Cetakan', '14 min', 'normal'],
    ['MK-2026-987659', 'Dr. Ahmad Hisham', 'Kad Khas Diplomat', 'Cetakan', '10 min', 'urgent'],
    ['MK-2026-987660', 'Anand Singh', 'Naturalisasi', 'Cetakan', '15 min', 'normal'],
]);
// Case-file mode (Semak): only this applicant's card. Browse (sidebar): full queue — everyone.
if ($threaded) {
    $queue = array_values(array_filter($queue, fn ($q) => !empty($q[6])));
}
$printers = [
    ['PRT-PJ-01', 'Datacard CR805 Retransfer', 42, 8200, 'printing'],
    ['PRT-PJ-02', 'Datacard CR805 Retransfer', 38, 7150, 'printing'],
    ['PRT-PJ-03', 'Matica XID8600', 0, 9000, 'idle'],
    ['PRT-SBH-01', 'Datacard CR805', 22, 4200, 'printing'],
    ['PRT-SWK-01', 'Matica XID8600', 18, 3800, 'printing'],
];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 04 ms.62 · ICAO Doc 9303</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">CLMS · Card Lifecycle Management System</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Stok · personalisation · key injection · QC · retirement</p>
    </header>

    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px;">
        <h3 style="margin: 0 0 14px; font-size: 14px;">Pipeline Pengeluaran MyKad</h3>
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px;">
            @foreach($stages as $i => $s)
                <div style="background: {{ $s[3] }}; border-radius: 8px; padding: 14px 10px; text-align: center; position: relative;">
                    <div style="font-size: 22px; font-weight: 700; color: {{ $s[4] }};">{{ number_format($s[1]) }}</div>
                    <div style="font-size: 11px; font-weight: 600; color: {{ $s[4] }}; margin-top: 4px;">{{ $s[0] }}</div>
                    <div style="color: #6B7280; font-size: 9.5px; margin-top: 2px; line-height: 1.3;">{{ $s[2] }}</div>
                    @if($i < 6)<div style="position: absolute; right: -9px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 14px; z-index: 1;">→</div>@endif
                </div>
            @endforeach
        </div>
    </section>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #E5E7EB;">
                <h3 style="margin: 0; font-size: 14px;">Baris Gilir Cetakan Aktif</h3>
                <span style="background: #FEF3C7; color: #B45309; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">Auto-route by load</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 12.5px;">
                <thead style="background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase;">
                    <tr><th style="padding: 10px 16px; text-align: left;">No. Siri</th><th style="padding: 10px 16px;">Pemohon</th><th style="padding: 10px 16px;">Jenis</th><th style="padding: 10px 16px;">Peringkat</th><th style="padding: 10px 16px;">ETA</th><th style="padding: 10px 16px;">Keutamaan</th></tr>
                </thead>
                <tbody>
                @if(empty($queue))
                    <tr><td colspan="6" style="padding: 24px 16px; text-align: center; color: #94A3B8; font-size: 12.5px;">Tiada kad dalam baris gilir untuk kes ini — modul ini terpakai bagi MyKad sahaja.</td></tr>
                @endif
                @foreach($queue as $q)
                    @php
                        $pri = match($q[5]) {
                            'urgent' => ['#FEE2E2', '#B91C1C', 'SEGERA'],
                            'high'   => ['#FEF3C7', '#B45309', 'TINGGI'],
                            default  => ['#DCFCE7', '#15803D', 'Biasa'],
                        };
                    @endphp
                    <tr style="border-top: 1px solid #F1F5F9; {{ !empty($q[6]) ? 'background: #EEF2FF; box-shadow: inset 3px 0 0 #6366F1;' : '' }}">
                        <td style="padding: 10px 16px; font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700;">{{ $q[0] }}@if(!empty($q[6]))<span style="margin-left: 6px; background: #6366F1; color: #fff; padding: 1px 6px; border-radius: 999px; font-size: 9px; font-weight: 700;">KES</span>@endif</td>
                        <td style="padding: 10px 16px;">{{ $q[1] }}</td>
                        <td style="padding: 10px 16px;">{{ $q[2] }}</td>
                        <td style="padding: 10px 16px;"><span style="background: #FEF3C7; color: #B45309; padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600;">{{ $q[3] }}</span></td>
                        <td style="padding: 10px 16px; font-family: ui-monospace, monospace; font-weight: 700;">{{ $q[4] }}</td>
                        <td style="padding: 10px 16px;"><span style="background: {{ $pri[0] }}; color: {{ $pri[1] }}; padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600;">{{ $pri[2] }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <aside style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px;">
            <h3 style="margin: 0 0 10px; font-size: 14px;">Pencetak Kad</h3>
            @foreach($printers as $p)
                <div style="border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px; margin-bottom: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <strong style="font-family: ui-monospace, monospace; font-size: 12px;">{{ $p[0] }}</strong>
                        <span style="background: {{ $p[4] === 'printing' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $p[4] === 'printing' ? '#15803D' : '#B45309' }}; padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600;">{{ $p[4] === 'printing' ? '● Cetak' : '○ Idle' }}</span>
                    </div>
                    <div style="color: #6B7280; font-size: 11px; margin-top: 2px;">{{ $p[1] }}</div>
                    <div style="display: flex; justify-content: space-between; margin-top: 6px; font-size: 11px;">
                        <span>Baris gilir: <strong>{{ $p[2] }}</strong></span>
                        <span>Stok: <strong>{{ number_format($p[3]) }}</strong></span>
                    </div>
                </div>
            @endforeach
        </aside>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi · CLMS Berasingan:</strong>
        <span style="color: #475569; font-size: 13px;"> Card Lifecycle Management System uruskan stok kad kosong, personalisation, key injection PKI, QC, retirement. Polikarbonat (bukan PVC) tahan 10+ tahun dan kalis pemalsuan dengan ICAO Doc 9303.</span>
    </div>

    @if($threaded && ($case['key'] ?? '') === 'mykad')
        {{-- Last officer step before the card artifact — personalize + commit to ledger (passthrough) --}}
        <div style="margin-top: 16px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <div style="font-weight: 700; color: #B45309; font-size: 14px;">Sedia untuk personalisation — keluarkan kad gantian</div>
                <div style="font-size: 12.5px; color: #475569; margin-top: 2px;">Laser engrave + key injection PKI, batalkan kad lama &amp; catat kad baru ke blockchain, kemudian jana kad fizikal.</div>
            </div>
            <button type="button" onclick="clmsSend()" style="background: #7C3AED; color: #fff; border: 0; padding: 10px 18px; border-radius: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer;">Personalisasi &amp; Keluarkan Kad</button>
        </div>
    @endif
</div>

@if(($case['key'] ?? '') === 'mykad')
@php
    $clBlock = $case['blockchain']['events'][0]['block'] ?? 1925013;
    $clRows = [
        ['Laser engrave + personalisation kad', 'Datacard CR805 · polikarbonat'],
        ['Key injection PKI · cip dwi-antara muka', 'keypair RSA-2048 disuntik'],
        ['Batal kad lama ' . ($case['lapor']['old_card_no'] ?? '—'), 'CardRevoked · mykad-cc'],
        ['Catat kad baru · Blok #' . $clBlock, 'MyKadIssued · 612ms immutable'],
        ['Publish event.mykad.issued', 'MyDigital ID auto-provision'],
    ];
@endphp
<style>
@keyframes clspin { to { transform: rotate(360deg); } }
.clspin { display: inline-block; animation: clspin .8s linear infinite; }
</style>
<div id="clModal" style="display:none; position:fixed; inset:0; background:rgba(var(--ink-navy-rgb),.55); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:26px 30px; width:470px; max-width:92vw; box-shadow:0 24px 70px rgba(0,0,0,.35);">
        <div style="display:flex; align-items:center; gap:11px; margin-bottom:18px;">
            <div style="width:38px; height:38px; border-radius:9px; background:#F3E8FF; display:flex; align-items:center; justify-content:center;">@include('system._icon', ['name' => 'id-card', 'color' => '#7C3AED', 'size' => 18])</div>
            <div>
                <div style="font-weight:700; font-size:15px; color:var(--ink-navy);">Pengeluaran Kad MyKad</div>
                <div style="font-size:12px; color:#6B7280;">No. Siri {{ $case['card']['card_no'] ?? $case['card_no'] ?? '—' }}</div>
            </div>
        </div>
        @foreach($clRows as $i => $r)
            <div id="clrow{{ $i }}" style="display:flex; align-items:center; gap:11px; padding:10px 0; border-bottom:1px dashed #F1F5F9; opacity:.4; transition:opacity .25s;">
                <span class="ic" style="width:22px; height:22px; border-radius:50%; background:#E2E8F0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; flex-shrink:0;">○</span>
                <div style="flex:1;">
                    <div style="font-size:13px; font-weight:600; color:var(--ink-navy);">{{ $r[0] }}</div>
                    <div style="font-size:11px; color:#94A3B8;">{{ $r[1] }}</div>
                </div>
            </div>
        @endforeach
        <div id="clDone" style="display:none; margin-top:18px; text-align:center;">
            <div style="background:#ECFDF5; color:#15803D; border:1px solid #BBF7D0; border-radius:8px; padding:9px; font-size:12.5px; font-weight:700; margin-bottom:12px;">✓ Kad gantian dikeluarkan · kad lama dibatalkan</div>
            <a href="{{ route('system.kad', ['ref' => $case['reference']]) }}" style="display:inline-block; text-decoration:none; background:#16A34A; color:#fff; padding:11px 22px; border-radius:9px; font-size:13px; font-weight:700;">Jana Kad MyKad →</a>
        </div>
    </div>
</div>
<script>
function clmsSend() {
    var modal = document.getElementById('clModal');
    modal.style.display = 'flex';
    var rows = document.querySelectorAll('#clModal [id^="clrow"]');
    var done = document.getElementById('clDone');
    done.style.display = 'none';
    rows.forEach(function (row) { row.style.opacity = '.4'; var ic = row.querySelector('.ic'); ic.innerHTML = '○'; ic.style.background = '#E2E8F0'; });
    var i = 0;
    function step() {
        if (i > 0) { var prev = rows[i - 1].querySelector('.ic'); prev.innerHTML = '✓'; prev.style.background = '#16A34A'; }
        if (i < rows.length) {
            rows[i].style.opacity = '1';
            rows[i].querySelector('.ic').innerHTML = '<span class="clspin">◜</span>';
            i++; setTimeout(step, 1000);
        } else { done.style.display = 'block'; }
    }
    step();
}
</script>
@endif
@endsection
