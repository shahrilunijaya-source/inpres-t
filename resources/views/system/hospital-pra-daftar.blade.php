@extends('layouts.system', ['active' => 'hospital', 'title' => 'Hospital KKM Pra-Daftar'])

@section('content')
@php
$threaded = !empty($case['threaded']);
$caseRow = [];
if ($threaded && !empty($case['hospital'])) {
    $h = $case['hospital'];
    $caseRow[] = [
        'hosp' => $h['name'], 'mom' => $case['mother']['name'] ?? $case['name'], 'mom_nric' => $case['mother']['ic'] ?? $case['ic'],
        'baby_sex' => $h['sex'], 'baby_weight' => $h['weight'], 'ts' => $h['born_at'], 'status' => 'pemohon_tiba', 'tone' => 'green', 'current' => true,
    ];
}
$inbox = array_merge($caseRow, [
    ['hosp' => 'HOSPITAL KUALA LUMPUR', 'mom' => 'Siti Aisyah binti Hisham', 'mom_nric' => '870519-14-3214', 'baby_sex' => 'L', 'baby_weight' => '3.2 kg', 'ts' => '2026-05-29 20:41', 'status' => 'baru', 'tone' => 'amber'],
    ['hosp' => 'HOSPITAL SELAYANG', 'mom' => 'Aminah binti Yusof', 'mom_nric' => '910822-10-2987', 'baby_sex' => 'P', 'baby_weight' => '2.9 kg', 'ts' => '2026-05-29 20:36', 'status' => 'baru', 'tone' => 'amber'],
    ['hosp' => 'HOSPITAL UMUM SARAWAK', 'mom' => 'Anita anak Joseph', 'mom_nric' => '880314-13-4561', 'baby_sex' => 'L', 'baby_weight' => '3.5 kg', 'ts' => '2026-05-29 20:24', 'status' => 'pemohon_tiba', 'tone' => 'green'],
    ['hosp' => 'HOSPITAL UMUM SABAH', 'mom' => 'Linda binti Mustafa', 'mom_nric' => '930211-12-3145', 'baby_sex' => 'P', 'baby_weight' => '2.7 kg', 'ts' => '2026-05-29 20:15', 'status' => 'baru', 'tone' => 'amber'],
    ['hosp' => 'HOSPITAL TJ SEREMBAN', 'mom' => 'Priya a/p Krishnan', 'mom_nric' => '900618-05-7821', 'baby_sex' => 'L', 'baby_weight' => '3.1 kg', 'ts' => '2026-05-29 20:02', 'status' => 'dalam_proses', 'tone' => 'amber'],
    ['hosp' => 'HOSPITAL SULTANAH AMINAH JB', 'mom' => 'Mei Ling binti Chong', 'mom_nric' => '920424-01-6531', 'baby_sex' => 'P', 'baby_weight' => '3.0 kg', 'ts' => '2026-05-29 19:48', 'status' => 'siap', 'tone' => 'green'],
    ['hosp' => 'HOSPITAL PUTRAJAYA', 'mom' => 'Faridah binti Ahmad', 'mom_nric' => '850907-14-2245', 'baby_sex' => 'L', 'baby_weight' => '3.4 kg', 'ts' => '2026-05-29 19:31', 'status' => 'siap', 'tone' => 'green'],
    ['hosp' => 'HOSPITAL PULAU PINANG', 'mom' => 'Sarah binti Razak', 'mom_nric' => '940515-07-3398', 'baby_sex' => 'P', 'baby_weight' => '2.8 kg', 'ts' => '2026-05-29 19:12', 'status' => 'siap', 'tone' => 'green'],
]);
// Case-file mode (arrived via Semak): show only this birth's hospital notification.
// Browse mode (sidebar): show the full inbox — everyone.
if ($threaded) {
    $inbox = array_values(array_filter($inbox, fn ($r) => !empty($r['current'])));
}
$hospitals = [
    ['Hospital Kuala Lumpur', 47, true], ['Hospital Putrajaya', 18, true], ['Hospital Selayang', 22, true],
    ['Hospital Sungai Buloh', 31, true], ['Hospital Tuanku Ja\'afar', 15, true], ['Hospital Sultanah Aminah JB', 28, true],
    ['Hospital Pulau Pinang', 19, true], ['Hospital Umum Sarawak', 14, true], ['Hospital Umum Sabah', 12, true],
    ['Hospital Raja Permaisuri Bainun', 17, true], ['Hospital Tengku Ampuan Afzan', 11, false],
];
$tones = ['green' => ['#DCFCE7','#15803D'], 'amber' => ['#FEF3C7','#B45309'], 'red' => ['#FEE2E2','#B91C1C']];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 01 · Pra-Pendaftaran Hospital</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Pra-Pendaftaran Hospital · Integrasi KKM</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">99% kelahiran di hospital KKM · pertukaran data klinikal automatik (FHIR R4)</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Hospital Tersambung','10 / 11','FHIR R4 / HL7','#1E40AF'],['Kelahiran Hari Ini','234','pra-daftar masuk','#15803D'],['Menunggu Ibu Bapa','42','datang JPN','#B45309'],['Masa Kaunter','10 min','turun dari 30 min','#6366F1']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 24px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #E5E7EB;">
                <h3 style="margin: 0; font-size: 14px;">Notifikasi Pra-Daftar Hospital</h3>
                <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">● Auto-pull setiap 30s</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 12.5px;">
                <thead style="background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase;">
                    <tr><th style="padding: 10px 16px; text-align: left;">Hospital</th><th style="padding: 10px 16px;">Ibu</th><th style="padding: 10px 16px;">MyKad</th><th style="padding: 10px 16px;">Bayi</th><th style="padding: 10px 16px;">Berat</th><th style="padding: 10px 16px;">Notif</th><th style="padding: 10px 16px;">Status</th></tr>
                </thead>
                <tbody>
                @if(empty($inbox))
                    <tr><td colspan="7" style="padding: 24px 16px; text-align: center; color: #94A3B8; font-size: 12.5px;">Tiada notifikasi hospital untuk kes ini — modul ini terpakai bagi Kelahiran sahaja.</td></tr>
                @endif
                @foreach($inbox as $r)
                    <tr style="border-top: 1px solid #F1F5F9; {{ !empty($r['current']) ? 'background: #EEF2FF; box-shadow: inset 3px 0 0 #6366F1;' : '' }}">
                        <td style="padding: 10px 16px; font-size: 11px; font-weight: 600;">{{ $r['hosp'] }}</td>
                        <td style="padding: 10px 16px;">{{ $r['mom'] }}@if(!empty($r['current']))<span style="margin-left: 6px; background: #6366F1; color: #fff; padding: 1px 6px; border-radius: 999px; font-size: 9px; font-weight: 700;">KES</span>@endif</td>
                        <td style="padding: 10px 16px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $r['mom_nric'] }}</td>
                        <td style="padding: 10px 16px;"><span style="background: #FEF3C7; color: #B45309; padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600;">{{ $r['baby_sex'] }}</span></td>
                        <td style="padding: 10px 16px;">{{ $r['baby_weight'] }}</td>
                        <td style="padding: 10px 16px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $r['ts'] }}</td>
                        <td style="padding: 10px 16px;"><span style="background: {{ $tones[$r['tone']][0] }}; color: {{ $tones[$r['tone']][1] }}; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ strtoupper(str_replace('_',' ', $r['status'])) }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <aside style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px;">
            <h3 style="margin: 0 0 10px; font-size: 14px;">Hospital Sambungan</h3>
            @foreach($hospitals as $h)
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #F1F5F9; font-size: 12px;">
                    <div>
                        <div style="font-weight: 600;">{{ $h[0] }}</div>
                        <div style="color: #6B7280; font-size: 10.5px;">{{ $h[1] }} kelahiran hari ini</div>
                    </div>
                    <span style="background: {{ $h[2] ? '#DCFCE7' : '#FEE2E2' }}; color: {{ $h[2] ? '#15803D' : '#B91C1C' }}; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; height: fit-content;">{{ $h[2] ? '● Live' : '● Down' }}</span>
                </div>
            @endforeach
        </aside>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi · Integrasi KKM Paling Kritikal:</strong>
        <span style="color: #475569; font-size: 13px;"> 99% kelahiran di hospital KKM. Integrasi FHIR R4 membolehkan pertukaran data klinikal automatik (berat, jantina, hospital, masa lahir, doktor) mengurangkan ralat dan menjimatkan masa kaunter ibu bapa dari 30 minit ke 10 minit.</span>
    </div>

    @if($threaded && !empty($case['reference']) && ($case['key'] ?? '') === 'birth')
        {{-- Next: parent completes the online form (hospital data pre-fills it) --}}
        <div style="margin-top: 16px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <div style="font-weight: 700; color: #B45309; font-size: 14px;">Data hospital diterima — ibu bapa lengkapkan borang dalam talian</div>
                <div style="font-size: 12.5px; color: #475569; margin-top: 2px;">Maklumat klinikal (masa, berat, hospital) sudah pra-isi. Ibu bapa lengkapkan Borang JPN.LM01 dalam talian sebelum hadir ke kaunter untuk biometrik.</div>
            </div>
            <a href="{{ route('system.borang', ['ref' => $case['reference']]) }}" style="text-decoration: none; background: var(--ink-navy); color: #fff; padding: 11px 20px; border-radius: 9px; font-size: 13px; font-weight: 700;">Isi Borang JPN.LM01 →</a>
        </div>
    @endif
</div>
@endsection
