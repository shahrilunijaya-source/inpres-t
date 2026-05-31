@extends('layouts.system', ['active' => 'kaveat', 'title' => 'Kaveat 21 Hari · Perkahwinan'])

@section('content')
@php
$threaded = !empty($case['threaded']);
$caseCouple = [];
if ($threaded && !empty($case['kaveat'])) {
    $k = $case['kaveat'];
    $caseCouple[] = [
        'ref' => $k['ref'], 'groom' => $case['groom']['name'] ?? $case['name'], 'bride' => $case['bride']['name'] ?? '—',
        'lodged' => $k['lodged'], 'expires' => $k['expires'], 'days_left' => $k['days_left'],
        'objections' => $k['objections'], 'tone' => $k['tone'], 'current' => true,
    ];
}
$couples = array_merge($caseCouple, [
    ['ref' => 'KAV-2026-002214', 'groom' => 'Ahmad bin Hisham', 'bride' => 'Faridah binti Salleh', 'lodged' => '2026-05-12', 'expires' => '2026-06-02', 'days_left' => 4, 'objections' => 0, 'tone' => 'amber'],
    ['ref' => 'KAV-2026-002215', 'groom' => 'Raj Kumar a/l Suresh', 'bride' => 'Priya a/p Krishnan', 'lodged' => '2026-05-15', 'expires' => '2026-06-05', 'days_left' => 7, 'objections' => 0, 'tone' => 'amber'],
    ['ref' => 'KAV-2026-002216', 'groom' => 'John Tan Wei Ming', 'bride' => 'Sarah Lim Hui Yen', 'lodged' => '2026-05-20', 'expires' => '2026-06-10', 'days_left' => 12, 'objections' => 0, 'tone' => 'green'],
    ['ref' => 'KAV-2026-002217', 'groom' => 'Anand Singh', 'bride' => 'Mei Ling Chen', 'lodged' => '2026-05-22', 'expires' => '2026-06-12', 'days_left' => 14, 'objections' => 1, 'tone' => 'red'],
    ['ref' => 'KAV-2026-002218', 'groom' => 'David Lee Chee Keong', 'bride' => 'Angela Wong Mei Fen', 'lodged' => '2026-05-25', 'expires' => '2026-06-15', 'days_left' => 17, 'objections' => 0, 'tone' => 'green'],
    ['ref' => 'KAV-2026-002219', 'groom' => 'Vinod a/l Ramasamy', 'bride' => 'Devi a/p Murthy', 'lodged' => '2026-05-27', 'expires' => '2026-06-17', 'days_left' => 19, 'objections' => 0, 'tone' => 'green'],
    ['ref' => 'KAV-2026-002211', 'groom' => 'Michael Tan Boon Hwa', 'bride' => 'Stephanie Ng Lai Peng', 'lodged' => '2026-05-08', 'expires' => '2026-05-29', 'days_left' => 0, 'objections' => 0, 'tone' => 'green', 'status' => 'tamat'],
]);
// Case-file mode (Semak): only the current couple. Browse (sidebar): full caveat board.
if ($threaded) {
    $couples = array_values(array_filter($couples, fn ($c) => !empty($c['current'])));
}
$tones = ['green' => ['#DCFCE7','#15803D'], 'amber' => ['#FEF3C7','#B45309'], 'red' => ['#FEE2E2','#B91C1C']];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 05 ms.65 · Akta 164 Seksyen 22</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Pengurusan Kaveat 21 Hari</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Pengiklanan rasmi sebelum upacara perkahwinan sivil</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Kaveat Aktif','6','dalam tempoh 21 hari','#1E40AF'],['Hampir Tamat','2','≤ 7 hari lagi','#B45309'],['Bantahan','1','perlu siasat','#DC2626'],['Tamat Bulan Ini','23','sedia untuk upacara','#15803D']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 28px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    @if($threaded && !empty($case['kaveat']))
    @php($kv = $case['kaveat'])
    @php($elapsed = max(0, 21 - ($kv['days_left'] ?? 0)))
    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; margin-top: 18px; padding: 18px 20px;">
        <div style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; text-transform: uppercase; color: #B45309; font-weight: 700;">Status Kaveat · Kes Semasa</div>
                <div style="font-size: 16px; font-weight: 700; color: var(--ink-navy); margin-top: 2px;">{{ $case['groom']['name'] ?? $case['name'] }} <span style="color:#B45309;">×</span> {{ $case['bride']['name'] ?? '' }}</div>
                <div style="font-size: 12px; color: #6B7280; font-family: ui-monospace, monospace;">{{ $kv['ref'] }} · difailkan {{ $kv['lodged'] }} · tamat {{ $kv['expires'] }}</div>
            </div>
            {{-- 21-day progress --}}
            <div style="flex: 1; min-width: 220px;">
                <div style="display: flex; justify-content: space-between; font-size: 11px; color: #6B7280; margin-bottom: 4px;"><span>Hari {{ $elapsed }} / 21</span><span>{{ $kv['days_left'] }} hari lagi</span></div>
                <div style="height: 10px; background: #F1F5F9; border-radius: 999px; overflow: hidden;">
                    <div style="height: 100%; width: {{ round($elapsed / 21 * 100) }}%; background: linear-gradient(90deg, #F59E0B, #16A34A);"></div>
                </div>
            </div>
            {{-- Objection state + action --}}
            <div style="text-align: center;">
                @if(($kv['objections'] ?? 0) > 0)
                    <div style="background: #FEE2E2; color: #B91C1C; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 13px;">{{ $kv['objections'] }} bantahan diterima</div>
                    <div style="font-size: 11px; color: #B91C1C; margin-top: 4px;">→ Buka siasatan sebelum upacara</div>
                @else
                    <div style="background: #DCFCE7; color: #15803D; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 13px;">Tiada bantahan</div>
                    <div style="font-size: 11px; color: #6B7280; margin-top: 4px;">Orang awam boleh failkan bantahan</div>
                @endif
                <button type="button" style="margin-top: 8px; background: #fff; border: 1px solid #FCA5A5; color: #B91C1C; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">+ Rekod Bantahan</button>
            </div>
        </div>
        <div style="margin-top: 14px; padding: 12px 14px; background: {{ ($kv['days_left'] ?? 1) <= 0 ? '#ECFDF5' : '#FFFBEB' }}; border: 1px solid {{ ($kv['days_left'] ?? 1) <= 0 ? '#BBF7D0' : '#FDE68A' }}; border-radius: 8px; font-size: 12.5px; color: #475569;">
            @if(($kv['days_left'] ?? 1) <= 0)
                ✓ Tempoh 21 hari tamat tanpa bantahan — <strong style="color:#15803D;">layak diteruskan ke Upacara</strong>.
            @else
                Menunggu tempoh kaveat tamat ({{ $kv['days_left'] }} hari lagi). Upacara hanya boleh dijalankan selepas tempoh tamat tanpa bantahan (Akta 164 S.22).
            @endif
        </div>
    </section>
    @endif

    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; margin-top: 18px; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #E5E7EB;">
            <h3 style="margin: 0; font-size: 14px;">Senarai Kaveat · Tempoh 21 Hari Akta 164 S.22</h3>
            <span style="background: #FEF3C7; color: #B45309; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">Auto-iklan: gazet + portal awam</span>
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 12.5px;">
            <thead style="background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                <tr>
                    <th style="padding: 10px 16px; text-align: left;">Rujukan</th><th style="padding: 10px 16px; text-align: left;">Bakal Suami</th><th style="padding: 10px 16px; text-align: left;">Bakal Isteri</th><th style="padding: 10px 16px; text-align: left;">Difailkan</th><th style="padding: 10px 16px; text-align: left;">Tamat</th><th style="padding: 10px 16px; text-align: left;">Tempoh</th><th style="padding: 10px 16px; text-align: left;">Bantahan</th><th style="padding: 10px 16px; text-align: left;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
            @if(empty($couples))
                <tr><td colspan="8" style="padding: 24px 16px; text-align: center; color: #94A3B8; font-size: 12.5px;">Tiada rekod kaveat untuk kes ini — modul ini terpakai bagi Perkahwinan Sivil sahaja.</td></tr>
            @endif
            @foreach($couples as $c)
                <tr style="border-top: 1px solid #F1F5F9; {{ !empty($c['current']) ? 'background: #FFFBEB; box-shadow: inset 3px 0 0 #F59E0B;' : '' }}">
                    <td style="padding: 10px 16px; font-family: ui-monospace, monospace; font-size: 11px; font-weight: 700;">{{ $c['ref'] }}@if(!empty($c['current']))<span style="margin-left: 6px; background: #F59E0B; color: #fff; padding: 1px 6px; border-radius: 999px; font-size: 9px; font-weight: 700;">KES</span>@endif</td>
                    <td style="padding: 10px 16px;">{{ $c['groom'] }}</td>
                    <td style="padding: 10px 16px;">{{ $c['bride'] }}</td>
                    <td style="padding: 10px 16px;">{{ $c['lodged'] }}</td>
                    <td style="padding: 10px 16px;">{{ $c['expires'] }}</td>
                    <td style="padding: 10px 16px;">
                        @if(($c['status'] ?? '') === 'tamat')
                            <span style="background: #DCFCE7; color: #15803D; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">TAMAT · sedia upacara</span>
                        @else
                            <span style="background: {{ $tones[$c['tone']][0] }}; color: {{ $tones[$c['tone']][1] }}; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ $c['days_left'] }} hari lagi</span>
                        @endif
                    </td>
                    <td style="padding: 10px 16px;">
                        @if($c['objections'] > 0)
                            <span style="background: #FEE2E2; color: #B91C1C; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ $c['objections'] }} bantahan</span>
                        @else
                            <span style="background: #DCFCE7; color: #15803D; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">Bersih</span>
                        @endif
                    </td>
                    <td style="padding: 10px 16px; font-size: 11px; font-weight: 600;">
                        @if(($c['status'] ?? '') === 'tamat')<span style="color: #15803D;">→ Tetapkan tarikh upacara</span>
                        @elseif($c['objections'] > 0)<span style="color: #B91C1C;">→ Buka siasatan</span>
                        @else<span style="color: #6B7280;">Menunggu tempoh tamat</span>@endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px;">
            <h3 style="margin: 0 0 10px; font-size: 14px;">Saluran Pengiklanan Aktif</h3>
            @foreach([['Portal Awam JPN','jpn.gov.my/kaveat','green'],['e-Gazet Kerajaan Persekutuan','gazet.gov.my','green'],['Notis Papan Kenyataan','21 cawangan JPN','green'],['Akhbar Berita Harian (S.22)','5 keluaran/minggu','amber']] as $ch)
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #F1F5F9;">
                    <div>
                        <div style="font-weight: 600; font-size: 13px;">{{ $ch[0] }}</div>
                        <div style="color: #6B7280; font-size: 11px;">{{ $ch[1] }}</div>
                    </div>
                    <span style="background: {{ $tones[$ch[2]][0] }}; color: {{ $tones[$ch[2]][1] }}; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; height: fit-content;">● Aktif</span>
                </div>
            @endforeach
        </section>
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px;">
            <h3 style="margin: 0 0 10px; font-size: 14px;">Semakan Auto Bersilang</h3>
            @foreach([['Family Tree · Hubungan Darah','Cek adik beradik / sedarah'],['Perkahwinan Sedia Ada','Cek poligami tanpa kebenaran'],['Umur Minimum Akta 164 S.10','Lelaki 18, Perempuan 16'],['Senarai Hitam JPN','Cek sekatan permohonan']] as $chk)
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #F1F5F9;">
                    <div>
                        <div style="font-weight: 600; font-size: 13px;">{{ $chk[0] }}</div>
                        <div style="color: #6B7280; font-size: 11px;">{{ $chk[1] }}</div>
                    </div>
                    <span style="background: #DCFCE7; color: #15803D; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; height: fit-content;">PASS</span>
                </div>
            @endforeach
        </section>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi LAMPIRAN A Modul 05 ms.65:</strong>
        <span style="color: #475569; font-size: 13px;"> Akta 164 S.22 mewajibkan tempoh kaveat 21 hari untuk pengiklanan dan elak perkahwinan haram. Auto-publish ke 4 saluran rasmi + auto-cross-check Family Tree, Senarai Hitam, rekod perkahwinan sedia ada.</span>
    </div>
</div>
@endsection
