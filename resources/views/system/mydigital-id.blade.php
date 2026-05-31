@extends('layouts.system', ['active' => 'mydigital', 'title' => 'MyDigital ID Auto-Provision'])

@section('content')
@php
$caseLog = [['11:02:55', $case['card_no'] ?? $case['reference'], $case['name'], $case['mydigital']['action'] ?? 'AUTO_PROVISION', $case['mydigital']['status'] ?? 'success', true]];
$log = array_merge($caseLog, [
    ['20:41:31', 'MK-2026-987654', 'Arjun a/l Subramaniam', 'BIOMETRIC_UPDATE', 'success'],
    ['20:40:12', 'MK-2026-987655', 'Siti Aminah binti Hasan', 'AUTO_PROVISION', 'success'],
    ['20:38:55', 'MK-2026-987656', 'Raj Kumar a/l Suresh', 'AUTO_PROVISION', 'success'],
    ['20:37:22', 'MK-2018-554321', 'Arjun a/l Subramaniam', 'CARD_REVOKED', 'success'],
    ['20:35:08', 'MK-2026-987657', 'John Tan Wei Ming', 'AUTO_PROVISION', 'success'],
    ['20:33:41', 'MK-2026-987658', 'Faridah binti Salleh', 'AUTO_PROVISION', 'success'],
    ['20:31:19', 'MK-2026-987659', 'Dr. Ahmad Hisham', 'AUTO_PROVISION', 'success'],
    ['20:29:55', 'MK-2026-987660', 'Anand Singh', 'AUTO_PROVISION', 'pending'],
]);
// Case-file mode (Semak): only this applicant's provision. Browse (sidebar): full log.
if (!empty($case['threaded'])) {
    $log = array_values(array_filter($log, fn ($l) => !empty($l[5])));
}
$agencies = [
    ['LHDN e-Filing', '14.2M', '2 saat lalu'], ['KWSP i-Akaun', '15.8M', '5 saat lalu'],
    ['PERKESO ASSIST', '8.1M', '12 saat lalu'], ['JPJ MySIKAP', '6.7M', '8 saat lalu'],
    ['JPA MyEPF', '1.3M', '1 minit lalu'], ['KKM MySejahtera', '22.4M', '1 saat lalu'],
    ['SPR myUndi', '12.8M', '4 minit lalu'], ['KPM MySchool', '5.1M', '3 minit lalu'],
];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 04 · MAMPU MyGDX Federation</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">MyDigital ID · Auto-Provision Akaun</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Setiap MyKad baru auto-create akaun MyDigital ID · SSO 74 agensi</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Akaun Aktif','21,847,392','warganegara dewasa','#1E40AF'],['Provision Hari Ini','4,821','auto-create MyKad baru','#15803D'],['Agensi Tersambung','74','via MAMPU MyGDX','#6366F1'],['Masa Provision','1,240ms','end-to-end purata','#B45309']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 22px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px; margin-top: 18px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; gap: 12px; flex-wrap: wrap;">
            <h3 style="margin: 0; font-size: 14px;">Aliran Auto-Provision</h3>
            <button type="button" id="mydRunBtn" onclick="mydRun()" style="background: #4338CA; color: #fff; border: 0; padding: 9px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">Jalankan Auto-Provision →</button>
        </div>
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px;">
            @foreach([['Modul MyKad','Sijil siap','#3B82F6'],['Event Kafka','event.mykad.issued','#6366F1'],['MyDigital ID Service','Terima event','#A855F7'],['Jana Identiti','Akaun Keycloak','#EC4899'],['SSO Aktif','74 agensi siap','#16A34A']] as $i => $step)
                <div class="myd-step" data-step="{{ $i }}" data-tone="{{ $step[2] }}" style="background: #F8FAFC; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 10px; text-align: center; position: relative; opacity: .5; transition: opacity .3s, background .3s, border-color .3s;">
                    <div class="myd-step__n" style="font-size: 11px; font-weight: 700; color: #94A3B8; margin-bottom: 4px;">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 12.5px; font-weight: 600; color: var(--ink-navy);">{{ $step[0] }}</div>
                    <div style="color: #6B7280; font-size: 10px; margin-top: 4px;">{{ $step[1] }}</div>
                    @if($i < 4)<div style="position: absolute; right: -9px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 14px; z-index: 1;">→</div>@endif
                </div>
            @endforeach
        </div>
    </section>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #E5E7EB;">
                <h3 style="margin: 0; font-size: 14px;">Log Provision Terkini</h3>
                <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">● Live</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead style="background: #F9FAFB; color: #6B7280; font-size: 10.5px; text-transform: uppercase;">
                    <tr><th style="padding: 8px 14px; text-align: left;">Masa</th><th style="padding: 8px 14px;">MyKad</th><th style="padding: 8px 14px;">Nama</th><th style="padding: 8px 14px;">Action</th><th style="padding: 8px 14px;">Status</th></tr>
                </thead>
                <tbody>
                @foreach($log as $l)
                    <tr style="border-top: 1px solid #F1F5F9; {{ !empty($l[5]) ? 'background: #EEF2FF; box-shadow: inset 3px 0 0 #6366F1;' : '' }}">
                        <td style="padding: 8px 14px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $l[0] }}</td>
                        <td style="padding: 8px 14px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $l[1] }}</td>
                        <td style="padding: 8px 14px;">{{ $l[2] }}@if(!empty($l[5]))<span style="margin-left: 6px; background: #6366F1; color: #fff; padding: 1px 6px; border-radius: 999px; font-size: 9px; font-weight: 700;">KES</span>@endif</td>
                        <td style="padding: 8px 14px;"><span style="background: #FEF3C7; color: #B45309; padding: 2px 7px; border-radius: 999px; font-size: 9.5px; font-weight: 600;">{{ $l[3] }}</span></td>
                        <td style="padding: 8px 14px;"><span style="background: {{ $l[4] === 'success' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $l[4] === 'success' ? '#15803D' : '#B45309' }}; padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600;">{{ strtoupper($l[4]) }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden;">
            <div style="padding: 14px 20px; border-bottom: 1px solid #E5E7EB;"><h3 style="margin: 0; font-size: 14px;">Agensi Pengguna MyDigital ID (Top 8 / 74)</h3></div>
            <table style="width: 100%; border-collapse: collapse; font-size: 12.5px;">
                <thead style="background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase;">
                    <tr><th style="padding: 10px 16px; text-align: left;">Agensi</th><th style="padding: 10px 16px;">Pengguna</th><th style="padding: 10px 16px;">Aktiviti</th></tr>
                </thead>
                <tbody>
                @foreach($agencies as $a)
                    <tr class="myd-ag" style="border-top: 1px solid #F1F5F9; transition: background .3s;">
                        <td style="padding: 10px 16px; font-weight: 600;">{{ $a[0] }}</td>
                        <td style="padding: 10px 16px; font-weight: 700;">{{ $a[1] }}</td>
                        <td style="padding: 10px 16px; color: #6B7280; font-size: 11px;">{{ $a[2] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi · MyDigital ID Integration:</strong>
        <span style="color: #475569; font-size: 13px;"> Setiap MyKad baru auto-create akaun MyDigital ID. Rakyat boleh terus guna untuk 74 perkhidmatan kerajaan online tanpa setup berasingan. MAMPU MyGDX sebagai federation backbone.</span>
    </div>
</div>

<script>
function mydRun() {
    var btn = document.getElementById('mydRunBtn');
    var steps = document.querySelectorAll('.myd-step');
    if (!btn) return;
    btn.disabled = true; btn.style.opacity = '.55'; btn.style.cursor = 'wait'; btn.textContent = 'Memproses…';
    steps.forEach(function (s) { s.style.opacity = '.5'; s.style.background = '#F8FAFC'; s.style.borderColor = '#E5E7EB'; var n = s.querySelector('.myd-step__n'); if (n) n.style.color = '#94A3B8'; });
    var i = 0;
    function light() {
        if (i >= steps.length) {
            // pipeline complete → flash the consumer agencies green
            document.querySelectorAll('.myd-ag').forEach(function (r, k) {
                setTimeout(function () {
                    r.style.background = '#ECFDF5';
                    setTimeout(function () { r.style.background = ''; }, 1400);
                }, k * 90);
            });
            btn.textContent = 'Akaun MyDigital ID Diaktifkan ✓'; btn.style.background = '#16A34A'; btn.style.opacity = '1';
            setTimeout(function () { btn.disabled = false; btn.style.cursor = 'pointer'; btn.style.background = '#4338CA'; btn.textContent = 'Jalankan Auto-Provision →'; }, 3000);
            return;
        }
        var s = steps[i], tone = s.dataset.tone || '#4338CA';
        s.style.opacity = '1'; s.style.background = tone + '15'; s.style.borderColor = tone + '55';
        var n = s.querySelector('.myd-step__n'); if (n) n.style.color = tone;
        i++; setTimeout(light, 480);
    }
    light();
}
</script>
@endsection
