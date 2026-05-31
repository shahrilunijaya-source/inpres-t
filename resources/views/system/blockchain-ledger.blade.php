@extends('layouts.system', ['active' => 'blockchain', 'title' => 'Hyperledger Fabric Ledger'])

@section('content')
@php
$caseRows = [];
foreach (($case['blockchain']['events'] ?? []) as $ev) {
    $caseRows[] = $ev + ['cc' => $case['blockchain']['cc'] ?? 'inpres-cc', 'current' => true];
}
$recent = array_merge($caseRows, [
    ['block' => 1925012, 'hash' => '0xc5e7a1b9f2...', 'cc' => 'kahwin-cc', 'event' => 'PerkahwinanRegistered', 'subj' => 'PK-2026-009876', 'ts' => '2026-05-29 20:41:31'],
    ['block' => 1925011, 'hash' => '0xa3f9d8e7c4...', 'cc' => 'mykad-cc', 'event' => 'MyKadIssued', 'subj' => 'MK-2026-987654', 'ts' => '2026-05-29 20:40:58'],
    ['block' => 1925010, 'hash' => '0xb8d4e2f1a9...', 'cc' => 'mykad-cc', 'event' => 'CardRevoked', 'subj' => 'MK-2018-554321', 'ts' => '2026-05-29 20:40:58'],
    ['block' => 1925009, 'hash' => '0x7a3b9c2d8e...', 'cc' => 'kelahiran-cc', 'event' => 'BirthRegistered', 'subj' => 'KLH-2026-001234', 'ts' => '2026-05-29 20:39:22'],
    ['block' => 1925008, 'hash' => '0xd2e5f4a1c7...', 'cc' => 'kelahiran-cc', 'event' => 'BiometricVerified', 'subj' => 'KLH-2026-001234', 'ts' => '2026-05-29 20:39:18'],
    ['block' => 1925007, 'hash' => '0x9c8b7a6d5e...', 'cc' => 'kematian-cc', 'event' => 'DeathRegistered', 'subj' => 'KMT-2026-004412', 'ts' => '2026-05-29 20:38:51'],
    ['block' => 1925006, 'hash' => '0x4e3d2c1b0a...', 'cc' => 'audit-cc', 'event' => 'OfficerLogin', 'subj' => 'OFC-2031', 'ts' => '2026-05-29 20:38:11'],
    ['block' => 1925005, 'hash' => '0xf1e2d3c4b5...', 'cc' => 'kahwin-cc', 'event' => 'CaveatExpired', 'subj' => 'KAV-2026-002211', 'ts' => '2026-05-29 20:37:00'],
]);
// Case-file mode (Semak): only this record's ledger events. Browse (sidebar): full ledger.
if (!empty($case['threaded'])) {
    $recent = array_values(array_filter($recent, fn ($r) => !empty($r['current'])));
}
$peers = [['peer0.jpn-putrajaya','142,789 blok','green'],['peer1.jpn-shah-alam','142,788 blok','green'],['peer2.jpn-johor','142,788 blok','green'],['peer3.jpn-kk-sabah','142,785 blok','amber'],['orderer0.mampu','—','green'],['orderer1.mampu','—','green'],['orderer2.mampu-dr','—','green']];
$chaincodes = ['kelahiran-cc v2.1','kematian-cc v2.0','mykad-cc v3.0','kahwin-cc v1.8','warganegara-cc v2.2','anak-angkat-cc v1.5','audit-cc v4.1'];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Para 2.1(viii) · Bukti Mahkamah Akta Keterangan S.90A</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Hyperledger Fabric · Lejer Rekod Kekal</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">4 peer + 3 orderer · Channel: inpres-main · Retention 7 tahun (Akta Arkib 2003)</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Jumlah Blok','#1,925,012','+4,821 hari ini','#A855F7'],['Rangkaian','4 peer · 3 orderer','3 channel aktif','#10B981'],['Block Time','612ms','RAFT consensus','#1E40AF'],['Bukti Mahkamah','S.90A','Tanpa percaya pentadbir','#DC2626']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 22px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #E5E7EB;">
                <h3 style="margin: 0; font-size: 14px;">Blok Terkini · Lejer Immutable</h3>
                <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">● 8 blok / 4 saat</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead style="background: #F9FAFB; color: #6B7280; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr><th style="padding: 8px 14px; text-align: left;">Blok</th><th style="padding: 8px 14px; text-align: left;">Hash</th><th style="padding: 8px 14px; text-align: left;">Chaincode</th><th style="padding: 8px 14px; text-align: left;">Event</th><th style="padding: 8px 14px; text-align: left;">Subjek</th><th style="padding: 8px 14px; text-align: left;">Masa</th></tr>
                </thead>
                <tbody>
                @foreach($recent as $r)
                    <tr style="border-top: 1px solid #F1F5F9; {{ !empty($r['current']) ? 'background: #F5F3FF; box-shadow: inset 3px 0 0 #7C3AED;' : '' }}">
                        <td style="padding: 8px 14px; font-family: ui-monospace, monospace; color: #7C3AED; font-weight: 700;">#{{ $r['block'] }}</td>
                        <td style="padding: 8px 14px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $r['hash'] }}</td>
                        <td style="padding: 8px 14px;"><span style="background: #FEF3C7; color: #B45309; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 600;">{{ $r['cc'] }}</span></td>
                        <td style="padding: 8px 14px; font-weight: 600;">{{ $r['event'] }}</td>
                        <td style="padding: 8px 14px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $r['subj'] }}@if(!empty($r['current']))<span style="margin-left: 6px; background: #7C3AED; color: #fff; padding: 1px 6px; border-radius: 999px; font-size: 9px; font-weight: 700;">KES</span>@endif</td>
                        <td style="padding: 8px 14px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $r['ts'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <aside>
            <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px; margin-bottom: 14px;">
                <h3 style="margin: 0 0 10px; font-size: 14px;">Topologi Rangkaian</h3>
                @foreach($peers as $n)
                    <div style="display: flex; justify-content: space-between; padding: 6px 10px; background: #F8FAFC; border-radius: 6px; margin-bottom: 6px; font-size: 11px;">
                        <span style="font-family: ui-monospace, monospace;">{{ $n[0] }}</span>
                        <div>
                            <span style="color: #6B7280; font-size: 10px;">{{ $n[1] }}</span>
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: {{ $n[2] === 'green' ? '#22C55E' : '#F59E0B' }}; margin-left: 8px;"></span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px;">
                <h3 style="margin: 0 0 10px; font-size: 14px;">Chaincode Dipasang</h3>
                @foreach($chaincodes as $cc)
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #F1F5F9; font-size: 12px;">
                        <span style="font-family: ui-monospace, monospace;">{{ $cc }}</span>
                        <span style="background: #DCFCE7; color: #15803D; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 600;">INSTANTIATED</span>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi LAMPIRAN A Para 2.1(viii):</strong>
        <span style="color: #475569; font-size: 13px;"> Hyperledger Fabric immutable ledger mewajibkan setiap rekod dicatat dengan crypto signature. Sekali blok diterbit, tiada sesiapa boleh ubah — termasuk pentadbir JPN. Memenuhi syarat bukti dokumentari di mahkamah.</span>
    </div>
</div>
@endsection
