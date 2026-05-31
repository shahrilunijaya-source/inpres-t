@extends('layouts.system', ['active' => 'perkakasan', 'title' => 'Perkakasan Kaunter (9)'])

@section('content')
@php
$devices = [
    ['Pengimbas 10 Cap Jari', 'Suprema RealScan-G10', 'ISO/IEC 19794-2', 247, 241, 'fingerprint'],
    ['Kamera Muka + Iris', 'Logitech Brio 500 + IriShield MK2120UL', 'ISO/IEC 19794-5/6', 247, 245, 'eye'],
    ['Pengimbas Cap Jari Tunggal', 'Crossmatch DigitalPersona U.are.U 5300', 'ISO/IEC 19794-2', 412, 408, 'fingerprint'],
    ['PC Kaunter Dual Monitor', 'Dell OptiPlex 7000 + 2 × 24"', 'Windows 11 LTSC', 247, 246, 'monitor'],
    ['Pencetak Kaunter (Sijil)', 'HP LaserJet Enterprise M611dn', 'A4 mono 65 ppm', 247, 240, 'printer'],
    ['Pembaca Kad MyKad (Cip)', 'HID OMNIKEY 3121 USB', 'PC/SC ISO 7816', 412, 411, 'card'],
    ['Pembaca QR Code', 'Honeywell Voyager 1450g 2D', 'GS1 DataMatrix', 412, 410, 'qr'],
    ['Pencetak Kad MyKad', 'Datacard CR805 Retransfer', 'ICAO Doc 9303', 47, 45, 'id-card'],
    ['Pengimbas Dokumen', 'Fujitsu fi-7160 ADF Duplex', 'PDF/A archival', 247, 244, 'doc'],
    ['Terminal Pembayaran FPX/iGFMAS', 'Verifone V200c', 'PCI-DSS L1', 247, 247, 'cash'],
    ['Tablet Kaunter Bergerak', 'Samsung Galaxy Tab Active5 Rugged', 'IP68 · offline sync', 84, 79, 'tablet'],
];
$branches = [
    ['JPN Putrajaya HQ', 24, 24, 'green'], ['JPN Wangsa Maju KL', 18, 18, 'green'],
    ['JPN Shah Alam', 16, 16, 'green'], ['JPN Johor Bahru', 14, 13, 'amber'],
    ['JPN Pulau Pinang', 12, 12, 'green'], ['JPN Kota Kinabalu', 12, 11, 'amber'],
    ['JPN Kuching', 12, 12, 'green'], ['JPN Ipoh', 10, 10, 'green'],
    ['JPN Kuantan', 8, 8, 'green'], ['JPN Alor Setar', 8, 7, 'amber'],
];
$total_units = collect($devices)->sum(3);
$total_online = collect($devices)->sum(4);
$uptime = round($total_online * 100 / $total_units, 1);
$tones = ['green' => ['#DCFCE7','#15803D'], 'amber' => ['#FEF3C7','#B45309'], 'red' => ['#FEE2E2','#B91C1C']];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · APPENDIX C · Perkakasan Wajib</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Perkakasan Kaunter · 9 Jenis APPENDIX C</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Waranti 5 tahun · pemantauan real-time semua 21 cawangan JPN</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Jenis Perkakasan',count($devices),'APPENDIX C tender','#1E40AF'],['Unit Online',number_format($total_online),'dari '.number_format($total_units).' unit','#15803D'],['Uptime',$uptime.'%','24 jam terakhir','#15803D'],['Waranti','5 tahun','on-site SLA 4 jam','#B45309']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 24px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px; margin-top: 18px;">
        <h3 style="margin: 0 0 14px; font-size: 14px;">Inventori Perkakasan</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
            @foreach($devices as $dev)
                @php($up = round($dev[4]*100/$dev[3], 1))
                @php($tone = $up >= 99 ? 'green' : ($up >= 95 ? 'amber' : 'red'))
                @php($dot = $tone === 'green' ? '#22C55E' : ($tone === 'amber' ? '#F59E0B' : '#DC2626'))
                @php($offline = $dev[3] - $dev[4])
                <div style="border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px; background: #fff; position: relative;">
                    <div style="position: absolute; top: 10px; right: 10px; width: 10px; height: 10px; border-radius: 50%; background: {{ $dot }}; box-shadow: 0 0 8px {{ $dot }};"></div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <div style="width: 42px; height: 42px; background: #F1F5F9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #475569;">@include('system._icon', ['name' => $dev[5], 'color' => '#475569', 'size' => 22])</div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 13px; color: var(--ink-navy); line-height: 1.2;">{{ $dev[0] }}</div>
                            <div style="color: #6B7280; font-size: 10px; margin-top: 2px;">{{ $dev[1] }}</div>
                        </div>
                    </div>
                    <div style="background: #F8FAFC; border-radius: 6px; padding: 8px 10px; margin-top: 8px; display: flex; justify-content: space-between; font-size: 11px; font-family: ui-monospace, monospace;">
                        <span style="color: #6B7280;">Piawaian</span>
                        <span style="font-weight: 600;">{{ $dev[2] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #F1F5F9;">
                        <div>
                            <div style="color: #6B7280; font-size: 10px;">Unit</div>
                            <div style="font-weight: 700;">{{ $dev[4] }} / {{ $dev[3] }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="color: #6B7280; font-size: 10px;">Uptime</div>
                            <span style="background: {{ $tones[$tone][0] }}; color: {{ $tones[$tone][1] }}; padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600;">{{ $up }}%</span>
                        </div>
                    </div>
                    @if($offline > 0)<div style="margin-top: 8px; font-size: 11px; color: #DC2626; font-weight: 600;"><span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#DC2626;margin-right:5px;vertical-align:middle;"></span>{{ $offline }} unit offline</div>@endif
                </div>
            @endforeach
        </div>
    </section>

    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; margin-top: 18px; overflow: hidden;">
        <div style="padding: 14px 20px; border-bottom: 1px solid #E5E7EB;"><h3 style="margin: 0; font-size: 14px;">Cawangan JPN · Status Kaunter</h3></div>
        <table style="width: 100%; border-collapse: collapse; font-size: 12.5px;">
            <thead style="background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase;">
                <tr><th style="padding: 10px 16px; text-align: left;">Cawangan</th><th style="padding: 10px 16px;">Kaunter</th><th style="padding: 10px 16px;">Online</th><th style="padding: 10px 16px;">Uptime</th><th style="padding: 10px 16px;">Status</th></tr>
            </thead>
            <tbody>
            @foreach($branches as $b)
                @php($bup = round($b[2]*100/$b[1], 1))
                <tr style="border-top: 1px solid #F1F5F9;">
                    <td style="padding: 10px 16px; font-weight: 600;">{{ $b[0] }}</td>
                    <td style="padding: 10px 16px; text-align: center;">{{ $b[1] }}</td>
                    <td style="padding: 10px 16px; text-align: center; font-weight: 700;">{{ $b[2] }}</td>
                    <td style="padding: 10px 16px; text-align: center;">{{ $bup }}%</td>
                    <td style="padding: 10px 16px;"><span style="background: {{ $tones[$b[3]][0] }}; color: {{ $tones[$b[3]][1] }}; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ $b[3] === 'green' ? '● PENUH OPS' : '● ADA ISU' }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi APPENDIX C · Waranti 5 Tahun:</strong>
        <span style="color: #475569; font-size: 13px;"> Tender LAMPIRAN A Para 2.2 mewajibkan 9 jenis perkakasan dengan piawaian ISO/IEC 19794 + ISO/IEC 30107 PAD. Waranti 5 tahun + SLA on-site 4 jam (Klang Valley) / 8 jam (luar).</span>
    </div>
</div>
@endsection
