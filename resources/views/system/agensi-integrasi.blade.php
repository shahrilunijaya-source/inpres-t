@extends('layouts.system', ['active' => 'agensi', 'title' => 'Integrasi Agensi Luar'])

@section('content')
@php
$agencies = [
    ['code' => 'KKM', 'name' => 'Kementerian Kesihatan', 'usage' => 'Kelahiran hospital (pra-daftar)', 'status' => 'live', 'latency' => 142, 'today_calls' => 4821, 'success' => 99.94],
    ['code' => 'JIM', 'name' => 'Imigresen Malaysia', 'usage' => 'Nombor paspot · MyPR', 'status' => 'live', 'latency' => 218, 'today_calls' => 1245, 'success' => 99.81],
    ['code' => 'JAKIM', 'name' => 'Jab. Kemajuan Islam', 'usage' => 'Perkahwinan Islam · cross-ref', 'status' => 'live', 'latency' => 178, 'today_calls' => 892, 'success' => 99.92],
    ['code' => 'MAIS', 'name' => 'Majlis Agama Sarawak', 'usage' => 'Bukan Islam · Sarawak', 'status' => 'live', 'latency' => 311, 'today_calls' => 87, 'success' => 99.50],
    ['code' => 'JKSM', 'name' => 'Jab. Kehakiman Syariah', 'usage' => 'Perceraian Islam', 'status' => 'live', 'latency' => 254, 'today_calls' => 412, 'success' => 99.78],
    ['code' => 'MAHKAMAH', 'name' => 'Mahkamah Tinggi', 'usage' => 'Perintah perceraian sivil', 'status' => 'live', 'latency' => 189, 'today_calls' => 156, 'success' => 99.85],
    ['code' => 'JAKOA', 'name' => 'Jab. Orang Asli', 'usage' => 'MyPOCA · komuniti', 'status' => 'live', 'latency' => 423, 'today_calls' => 34, 'success' => 98.94],
    ['code' => 'JKM', 'name' => 'Jab. Kebajikan Masyarakat', 'usage' => 'Laporan sosial · anak angkat', 'status' => 'live', 'latency' => 201, 'today_calls' => 287, 'success' => 99.62],
    ['code' => 'MAMPU', 'name' => 'MAMPU MyDigital ID', 'usage' => 'Auto-provision setiap MyKad', 'status' => 'live', 'latency' => 94, 'today_calls' => 6231, 'success' => 99.98],
    ['code' => 'KPM', 'name' => 'Kementerian Pendidikan', 'usage' => 'Persekolahan · rekod', 'status' => 'live', 'latency' => 167, 'today_calls' => 2104, 'success' => 99.88],
    ['code' => 'LHDN', 'name' => 'Lembaga Hasil Dalam Negeri', 'usage' => 'Status cukai · perkahwinan', 'status' => 'live', 'latency' => 287, 'today_calls' => 1892, 'success' => 99.71],
    ['code' => 'KWSP', 'name' => 'Kumpulan Wang Simpanan', 'usage' => 'Penama · ahli waris', 'status' => 'live', 'latency' => 256, 'today_calls' => 743, 'success' => 99.83],
    ['code' => 'TBH', 'name' => 'Tabung Haji', 'usage' => 'Kelahiran Hedjaz (haji)', 'status' => 'live', 'latency' => 412, 'today_calls' => 18, 'success' => 98.50],
    ['code' => 'PDRM', 'name' => 'Polis Diraja Malaysia', 'usage' => 'Saman · senarai hitam', 'status' => 'live', 'latency' => 178, 'today_calls' => 3214, 'success' => 99.76],
    ['code' => 'SPR', 'name' => 'Suruhanjaya Pilihan Raya', 'usage' => 'Auto-daftar pengundi @ 18', 'status' => 'live', 'latency' => 234, 'today_calls' => 421, 'success' => 99.87],
    ['code' => 'KEMLU', 'name' => 'Kementerian Luar (MALAWAKIL)', 'usage' => 'Perkahwinan luar negara', 'status' => 'degraded', 'latency' => 782, 'today_calls' => 23, 'success' => 96.21],
    ['code' => 'PERKESO', 'name' => 'PERKESO Keselamatan Sosial', 'usage' => 'Caruman · ahli waris', 'status' => 'live', 'latency' => 245, 'today_calls' => 567, 'success' => 99.69],
];
$live = collect($agencies)->where('status','live')->count();
$total_calls = collect($agencies)->sum('today_calls');
$avg_success = round(collect($agencies)->avg('success'), 2);
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Integrasi rentas modul</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Integrasi Agensi Luar · 13 Agensi + MyGDX</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">API Gateway + OAuth 2.0 + mTLS · MAMPU MyGDX backbone (74 agensi federation)</p>
    </header>

    {{-- Agencies queried for the current case --}}
    @php($agByCode = collect($agencies)->keyBy('code'))
    <section style="background: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 10px; padding: 14px 18px; margin-bottom: 18px;">
        <div style="font-size: 10.5px; letter-spacing: 1px; text-transform: uppercase; color: #4338CA; font-weight: 700; margin-bottom: 8px;">Agensi disentuh untuk kes semasa · {{ $case['name'] }} ({{ $case['reference'] }})</div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            @foreach(($case['agencies'] ?? []) as $code)
                @php($a = $agByCode[$code] ?? null)
                <div style="background: #fff; border: 1px solid #C7D2FE; border-radius: 8px; padding: 8px 14px;">
                    <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #4338CA; font-weight: 700;">{{ $code }}</span>
                    <span style="font-size: 12px; color: var(--ink-navy); margin-left: 6px;">{{ $a['name'] ?? $code }}</span>
                    @if($a)<span style="font-size: 11px; color: #15803D; margin-left: 8px;">● {{ $a['latency'] }}ms</span>@endif
                </div>
            @endforeach
        </div>
    </section>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Agensi Aktif',$live.' / '.count($agencies),'+ 74 via MyGDX','#15803D'],['Degraded','1','latency > 500ms','#B45309'],['Panggilan Hari Ini',number_format($total_calls),'API rentas agensi','#1E40AF'],['Kadar Kejayaan',$avg_success.'%','purata 24 jam','#15803D']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 24px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px; margin-top: 18px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; gap: 12px; flex-wrap: wrap;">
            <h3 style="margin: 0; font-size: 14px;">Topologi Integrasi</h3>
            <button type="button" onclick="agTestAll()" style="background: #1E3A8A; color: #fff; border: 0; padding: 9px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">Uji Semua Sambungan</button>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 12px;">
            @foreach($agencies as $a)
                @php($dot = $a['status'] === 'live' ? '#22C55E' : '#F59E0B')
                <div class="ag-card" data-latency="{{ $a['latency'] }}" data-status="{{ $a['status'] }}" style="position: relative; border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px; background: #fff;">
                    <div class="ag-dot" style="position: absolute; top: 10px; right: 10px; width: 8px; height: 8px; border-radius: 50%; background: {{ $dot }}; box-shadow: 0 0 8px {{ $dot }};"></div>
                    <div style="font-family: ui-monospace, monospace; font-size: 11px; color: #6B7280; font-weight: 600;">{{ $a['code'] }}</div>
                    <div style="font-weight: 700; font-size: 13px; color: var(--ink-navy); margin-top: 2px;">{{ $a['name'] }}</div>
                    <div style="color: #6B7280; font-size: 11px; margin-top: 4px; line-height: 1.4;">{{ $a['usage'] }}</div>
                    <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #F1F5F9; font-size: 11px;">
                        <span style="color: #6B7280;">{{ $a['latency'] }}ms</span>
                        <span style="color: #15803D; font-weight: 600;">{{ $a['success'] }}%</span>
                    </div>
                    <div style="color: #6B7280; font-size: 10px; margin-top: 4px;">{{ number_format($a['today_calls']) }} panggilan hari ini</div>
                    <button type="button" class="ag-test" onclick="agTest(this)" style="width: 100%; margin-top: 10px; background: #F1F5F9; color: #1E3A8A; border: 1px solid #E5E7EB; padding: 7px; border-radius: 7px; font-size: 11.5px; font-weight: 700; cursor: pointer;">Uji Sambungan</button>
                </div>
            @endforeach
        </div>
    </section>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px;">
            <h3 style="margin: 0 0 10px; font-size: 14px;">Lapisan Keselamatan</h3>
            @foreach([['mTLS Mutual TLS','Sijil X.509 setiap agensi'],['OAuth 2.0 Client Credentials','Token rotation 1 jam'],['IP Allowlist','Hanya julat MyGov'],['Rate Limiting','1000 req/min per agensi'],['Request Signing HMAC-SHA256','Audit non-repudiation']] as $sec)
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #F1F5F9;">
                    <div>
                        <div style="font-weight: 600; font-size: 13px;">{{ $sec[0] }}</div>
                        <div style="color: #6B7280; font-size: 11px;">{{ $sec[1] }}</div>
                    </div>
                    <span style="background: #DCFCE7; color: #15803D; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; height: fit-content;">● ON</span>
                </div>
            @endforeach
        </section>
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px;">
            <h3 style="margin: 0 0 10px; font-size: 14px;">Kanal Mesej Dalaman</h3>
            @foreach([['Apache Kafka Cluster','3 broker · 6 topic','4821 msg/s'],['Kong API Gateway','24 upstream service','12.4k req/s'],['Keycloak Auth','SSO + 2FA + WebAuthn','312 active sesi'],['Redis Cache Cluster','Token + session store','45MB/s'],['MAMPU MyGDX Bus','Federated 74 agensi','—']] as $svc)
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #F1F5F9;">
                    <div>
                        <div style="font-weight: 600; font-size: 13px;">{{ $svc[0] }}</div>
                        <div style="color: #6B7280; font-size: 11px;">{{ $svc[1] }}</div>
                    </div>
                    <div style="text-align: right;">
                        <span style="background: #DCFCE7; color: #15803D; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">● Live</span>
                        <div style="color: #6B7280; font-size: 10px; margin-top: 2px;">{{ $svc[2] }}</div>
                    </div>
                </div>
            @endforeach
        </section>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi LAMPIRAN A (Integrasi rentas modul):</strong>
        <span style="color: #475569; font-size: 13px;"> 13 agensi luar wajib disambung mengikut tender InPreS. MyGDX (MAMPU Government Data Exchange) sebagai backbone tambahan menyambung 74 agensi tanpa point-to-point. Semua trafik melalui OAuth 2.0 + mTLS untuk pematuhan PDPA 2010.</span>
    </div>
</div>

<style>@keyframes agSpin { to { transform: rotate(360deg); } } .ag-spin { display: inline-block; width: 11px; height: 11px; border: 2px solid rgba(30,58,138,.3); border-top-color: #1E3A8A; border-radius: 50%; animation: agSpin .6s linear infinite; vertical-align: -1px; margin-right: 5px; }</style>
<script>
function agTest(btn) {
    var card = btn.closest('.ag-card');
    if (!card || btn.dataset.busy === '1') return;
    btn.dataset.busy = '1';
    var lat = parseInt(card.dataset.latency, 10) || 200;
    var degraded = card.dataset.status !== 'live';
    btn.innerHTML = '<span class="ag-spin"></span>Menghantar ping…';
    btn.style.cursor = 'wait';
    var dot = card.querySelector('.ag-dot');
    if (dot) dot.style.animation = 'agSpin .6s linear infinite';
    setTimeout(function () {
        if (dot) dot.style.animation = '';
        if (degraded) {
            btn.style.background = '#FEF3C7'; btn.style.color = '#B45309'; btn.style.borderColor = '#FDE68A';
            btn.textContent = 'Perlahan · ' + lat + 'ms';
        } else {
            btn.style.background = '#DCFCE7'; btn.style.color = '#15803D'; btn.style.borderColor = '#BBF7D0';
            btn.textContent = 'OK · 200 OK · ' + lat + 'ms';
        }
        btn.dataset.busy = '0'; btn.style.cursor = 'pointer';
        setTimeout(function () {
            btn.style.background = '#F1F5F9'; btn.style.color = '#1E3A8A'; btn.style.borderColor = '#E5E7EB';
            btn.textContent = 'Uji Sambungan';
        }, 2600);
    }, 600 + (lat % 700));
}
function agTestAll() {
    var btns = document.querySelectorAll('.ag-test');
    btns.forEach(function (b, i) { setTimeout(function () { agTest(b); }, i * 130); });
}
</script>
@endsection
