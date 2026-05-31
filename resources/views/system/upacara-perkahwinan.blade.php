@extends('layouts.system', ['active' => 'upacara', 'title' => 'Upacara & Pendaftaran Perkahwinan'])

@section('content')
@php
    $up = $case['upacara'] ?? [
        'venue' => 'JPN Putrajaya · Kaunter Perkahwinan', 'venue_type' => 'jpn', 'venue_detail' => [],
        'registrar' => 'Pendaftar Perkahwinan', 'registrar_id' => 'REG-PK-0000',
        'date' => '—', 'time' => '—', 'reg_no' => $case['reference'], 'status' => 'selesai',
        'witnesses' => [['name' => 'Saksi 1', 'ic' => '—', 'rel' => '—'], ['name' => 'Saksi 2', 'ic' => '—', 'rel' => '—']],
    ];
    $vt = $up['venue_type'] ?? 'jpn';
    $vd = $up['venue_detail'] ?? [];
    $groom = $case['groom'] ?? ['name' => $case['name'], 'ic' => $case['ic'], 'status' => 'Bujang'];
    $bride = $case['bride'] ?? ['name' => 'Pasangan', 'ic' => '—', 'status' => 'Bujang'];
    $venues = [
        'jpn'       => ['JPN', 'Kaunter Pendaftaran'],
        'ibadat'    => ['Rumah Ibadat', 'Gereja / Kuil — Penolong Pendaftar'],
        'malawakil' => ['MALAWAKIL', 'Kedutaan / luar negara'],
        'tribunal'  => ['Tribunal', 'Badan Pendamai'],
    ];
    $signatories = [
        ['Pengantin Lelaki', $groom['name']],
        ['Pengantin Perempuan', $bride['name']],
        ['Saksi 1', $up['witnesses'][0]['name'] ?? '—'],
        ['Saksi 2', $up['witnesses'][1]['name'] ?? '—'],
        ['Pendaftar', $up['registrar']],
    ];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 05 ms.63 · Akta 164 S.24 (Upacara)</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Upacara & Pendaftaran Perkahwinan Sivil</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Pengupacaraan di hadapan Pendaftar + 2 saksi · ikrar & tandatangan · {{ $up['venue'] }} · {{ $up['date'] }} {{ $up['time'] }}</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Lokasi Upacara', $venues[$up['venue_type']][0] ?? 'JPN', '1 dari 4 lokasi sah', '#1E40AF'], ['Saksi Hadir', '2 / 2', 'IC disahkan', '#15803D'], ['Status Kaveat', 'TAMAT', '21 hari · tiada bantahan', '#15803D'], ['No. Daftar', $up['reg_no'], 'Akta 164 · TDE encrypted', '#6366F1']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 20px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px; font-family: ui-monospace, monospace;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Couple + witnesses --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px;">
            <h3 style="margin: 0 0 14px; font-size: 14px;">Pasangan Berkahwin</h3>
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="flex: 1; text-align: center; border: 1px solid #DBEAFE; background: #EFF6FF; border-radius: 12px; padding: 18px;">
                    <div style="display: flex; justify-content: center; margin-bottom: 8px;">@include('system._mykad-photo', ['ic' => $groom['ic'], 'name' => $groom['name'], 'shape' => 'circle', 'size' => 92, 'gender' => 'M'])</div>
                    <div style="font-weight: 700; font-size: 15px; color: var(--ink-navy);">{{ $groom['name'] }}</div>
                    <div style="font-family: ui-monospace, monospace; font-size: 11px; color: #6B7280;">{{ $groom['ic'] }}</div>
                    <span style="display: inline-block; margin-top: 6px; background: #DCFCE7; color: #15803D; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ $groom['status'] ?? 'Bujang' }} · disahkan</span>
                </div>
                <div style="color: #BE185D; display: flex; align-items: center;">@include('system._icon', ['name' => 'heart', 'color' => '#BE185D', 'size' => 28])</div>
                <div style="flex: 1; text-align: center; border: 1px solid #FCE7F3; background: #FDF2F8; border-radius: 12px; padding: 18px;">
                    <div style="display: flex; justify-content: center; margin-bottom: 8px;">@include('system._mykad-photo', ['ic' => $bride['ic'], 'name' => $bride['name'], 'shape' => 'circle', 'size' => 92, 'gender' => 'F'])</div>
                    <div style="font-weight: 700; font-size: 15px; color: var(--ink-navy);">{{ $bride['name'] }}</div>
                    <div style="font-family: ui-monospace, monospace; font-size: 11px; color: #6B7280;">{{ $bride['ic'] }}</div>
                    <span style="display: inline-block; margin-top: 6px; background: #DCFCE7; color: #15803D; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ $bride['status'] ?? 'Bujang' }} · disahkan</span>
                </div>
            </div>

            {{-- Lokasi upacara (4 jenis) --}}
            <h3 style="margin: 20px 0 10px; font-size: 14px;">Lokasi Pengupacaraan · 4 Jenis Sah (Akta 164)</h3>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                @foreach($venues as $vk => $v)
                    @php $on = $vk === ($up['venue_type'] ?? 'jpn'); @endphp
                    <div style="border: 2px solid {{ $on ? '#1E3A8A' : '#E5E7EB' }}; background: {{ $on ? '#EFF6FF' : '#fff' }}; border-radius: 10px; padding: 12px; text-align: center;">
                        <div style="font-weight: 700; font-size: 12.5px; color: {{ $on ? '#1E3A8A' : '#475569' }};">{{ $v[0] }}</div>
                        <div style="font-size: 10px; color: #6B7280; margin-top: 2px; line-height: 1.3;">{{ $v[1] }}</div>
                        @if($on)<div style="margin-top: 6px; font-size: 10px; color: #15803D; font-weight: 700;">● Dipilih</div>@endif
                    </div>
                @endforeach
            </div>

            {{-- Conditional location detail — depends on venue type --}}
            <div style="margin-top: 14px; border: 1px solid #C7D2FE; background: #EEF2FF; border-radius: 10px; padding: 14px 16px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; text-transform: uppercase; color: #4338CA; font-weight: 700; margin-bottom: 8px;">Butiran Lokasi Dipilih · {{ $venues[$vt][0] ?? 'JPN' }}</div>
                @if($vt === 'ibadat')
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px; padding: 5px 0; border-bottom: 1px dashed #C7D2FE;"><span style="color:#6B7280;">Nama Rumah Ibadat</span><span style="font-weight:600;">{{ $vd['place'] ?? '—' }}</span></div>
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px; padding: 5px 0; border-bottom: 1px dashed #C7D2FE;"><span style="color:#6B7280;">Alamat</span><span style="font-weight:600;">{{ $vd['address'] ?? '—' }}</span></div>
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px; padding: 5px 0;"><span style="color:#6B7280;">Penolong Pendaftar</span><span style="font-weight:600;">{{ $vd['authority'] ?? '—' }}</span></div>
                @elseif($vt === 'malawakil')
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px; padding: 5px 0; border-bottom: 1px dashed #C7D2FE;"><span style="color:#6B7280;">Negara</span><span style="font-weight:600;">{{ $vd['country'] ?? 'Australia' }}</span></div>
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px; padding: 5px 0;"><span style="color:#6B7280;">Kedutaan / Wakil</span><span style="font-weight:600;">{{ $vd['mission'] ?? 'Suruhanjaya Tinggi Malaysia, Canberra' }}</span></div>
                @elseif($vt === 'tribunal')
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px; padding: 5px 0; border-bottom: 1px dashed #C7D2FE;"><span style="color:#6B7280;">Tribunal</span><span style="font-weight:600;">{{ $vd['tribunal'] ?? 'Tribunal Perkahwinan' }}</span></div>
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px; padding: 5px 0;"><span style="color:#6B7280;">Badan Pendamai</span><span style="font-weight:600;">{{ $vd['body'] ?? 'Badan Pendamai JPN' }}</span></div>
                @else
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px; padding: 5px 0;"><span style="color:#6B7280;">Kaunter JPN</span><span style="font-weight:600;">{{ $up['venue'] }}</span></div>
                @endif
            </div>
        </section>

        <aside>
            <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px; margin-bottom: 14px;">
                <h3 style="margin: 0 0 10px; font-size: 14px;">2 Saksi · Akta 164 S.24</h3>
                @foreach($up['witnesses'] as $i => $w)
                    <div style="display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px dashed #F1F5F9;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569;">{{ $i + 1 }}</div>
                        <div>
                            <div style="font-weight: 600; font-size: 13px;">{{ $w['name'] }}</div>
                            <div style="font-family: ui-monospace, monospace; font-size: 10.5px; color: #6B7280;">{{ $w['ic'] }} · {{ $w['rel'] }}</div>
                        </div>
                        <span style="margin-left: auto; background: #DCFCE7; color: #15803D; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 600; height: fit-content;">✓ Sah</span>
                    </div>
                @endforeach
            </div>
            <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px;">
                <h3 style="margin: 0 0 8px; font-size: 14px;">Pendaftar Perkahwinan</h3>
                <div style="font-weight: 700; font-size: 14px; color: var(--ink-navy);">{{ $up['registrar'] }}</div>
                <div style="font-family: ui-monospace, monospace; font-size: 11px; color: #6B7280;">{{ $up['registrar_id'] }}</div>
                <div style="margin-top: 8px; font-size: 11.5px; color: #475569;">Mengupacarakan ikrar perkahwinan dan menyaksikan tandatangan kedua-dua pihak.</div>
            </div>
        </aside>
    </div>

    {{-- Ikrar + signatures + registration --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px;">
            <h3 style="margin: 0 0 4px; font-size: 14px;">Ikrar & Tandatangan Digital</h3>
            <p style="margin: 0 0 12px; font-size: 12px; color: #6B7280;">Tablet tandatangan · ikrar di hadapan Pendaftar</p>
            @foreach($signatories as $s)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 9px 0; border-bottom: 1px dashed #F1F5F9;">
                    <div>
                        <div style="font-size: 10.5px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">{{ $s[0] }}</div>
                        <div style="font-weight: 600; font-size: 13px;">{{ $s[1] }}</div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-family: 'Brush Script MT', 'Segoe Script', 'Snell Roundhand', cursive; font-style: italic; font-size: 22px; color: #1E3A8A; display: inline-block; transform: rotate(-4deg); line-height: 1;">{{ $s[1] }}</span>
                        <div style="border-top: 1px solid #CBD5E1; margin-top: 3px; font-size: 9px; color: #94A3B8; letter-spacing: 0.4px;">✓ Ditandatangani secara digital</div>
                    </div>
                </div>
            @endforeach
        </section>

        <section style="background: #ECFDF5; border: 1px solid #22C55E; border-radius: 10px; padding: 20px;">
            <div style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #15803D; font-weight: 700;">Perkahwinan Didaftarkan</div>
            <div style="font-size: 28px; font-weight: 800; color: #15803D; margin: 6px 0; font-family: ui-monospace, monospace;">{{ $up['reg_no'] }}</div>
            <div style="font-size: 12.5px; color: #475569; line-height: 1.6;">
                INSERT <strong>perkahwinan_master</strong> · Oracle RAC (TDE encrypted)<br>
                Akta 164 S.24 · status undang-undang: <strong>suami isteri sah</strong><br>
                Replikasi PDSA Data Guard (SYNC) · sedia untuk catatan blockchain.
            </div>
            <div style="margin-top: 14px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <span style="background: #DCFCE7; color: #15803D; padding: 5px 12px; border-radius: 999px; font-size: 11px; font-weight: 700;">UPACARA SELESAI</span>
                <button type="button" onclick="bcSend()" style="background: #7C3AED; color: #fff; border: 0; padding: 9px 18px; border-radius: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 7px;">@include('system._icon', ['name' => 'link', 'color' => '#fff', 'size' => 15]) Hantar ke Blockchain</button>
                <span style="font-size: 11px; color: #94A3B8;">Modul ledger terkunci (admin) — proses tetap lalu</span>
            </div>
        </section>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi LAMPIRAN A Modul 05 ms.63 (Akta 164 S.24):</strong>
        <span style="color: #475569; font-size: 13px;"> Perkahwinan sivil mesti diupacarakan di hadapan Pendaftar dengan 2 saksi sah selepas tempoh kaveat 21 hari tamat tanpa bantahan. 4 lokasi dibenarkan (JPN, Rumah Ibadat berdaftar, MALAWAKIL luar negara, Tribunal). Ikrar + tandatangan kedua-dua pihak mengubah status undang-undang — dicatat kekal di Hyperledger Fabric.</span>
    </div>
</div>

@php $bcBlock = $case['blockchain']['events'][0]['block'] ?? ($case['sijil']['block'] ?? 1925015); @endphp
<style>@keyframes bcspin{to{transform:rotate(360deg)}} .bcspin{display:inline-block;animation:bcspin .8s linear infinite}</style>

<div id="bcModal" style="display:none; position:fixed; inset:0; background:rgba(var(--ink-navy-rgb),.55); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:26px 30px; width:460px; max-width:92vw; box-shadow:0 24px 70px rgba(0,0,0,.35);">
        <div style="display:flex; align-items:center; gap:11px; margin-bottom:18px;">
            <div style="width:38px; height:38px; border-radius:9px; background:#F3E8FF; display:flex; align-items:center; justify-content:center;">@include('system._icon', ['name' => 'link', 'color' => '#7C3AED', 'size' => 19])</div>
            <div>
                <div style="font-weight:700; font-size:15px; color:var(--ink-navy);">Catatan ke Hyperledger Fabric</div>
                <div style="font-size:12px; color:#6B7280;">Rekod perkahwinan {{ $up['reg_no'] }}</div>
            </div>
        </div>

        @php
            $bcRows = [
                ['Menghantar transaksi · chaincode kahwin-cc', 'TDE encrypted payload'],
                ['Konsensus RAFT · 4 peer + 3 orderer', 'inpres-main channel'],
                ['Blok #' . $bcBlock . ' dilog · immutable', '612ms · tiada boleh ubah'],
                ['Publish event.kahwin.registered', '6 modul pelanggan melanggan'],
            ];
        @endphp
        @foreach($bcRows as $i => $r)
            <div id="bcrow{{ $i }}" style="display:flex; align-items:center; gap:11px; padding:10px 0; border-bottom:1px dashed #F1F5F9; opacity:.4; transition:opacity .25s;">
                <span class="ic" style="width:22px; height:22px; border-radius:50%; background:#E2E8F0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; flex-shrink:0;">○</span>
                <div style="flex:1;">
                    <div style="font-size:13px; font-weight:600; color:var(--ink-navy);">{{ $r[0] }}</div>
                    <div style="font-size:11px; color:#94A3B8;">{{ $r[1] }}</div>
                </div>
            </div>
        @endforeach

        <div id="bcDone" style="display:none; margin-top:18px; text-align:center;">
            <div style="background:#ECFDF5; color:#15803D; border:1px solid #BBF7D0; border-radius:8px; padding:9px; font-size:12.5px; font-weight:700; margin-bottom:12px;">✓ Status undang-undang berubah · suami isteri sah</div>
            <a href="{{ route('system.sijil', ['ref' => $case['reference']]) }}" style="display:inline-block; text-decoration:none; background:#16A34A; color:#fff; padding:11px 22px; border-radius:9px; font-size:13px; font-weight:700;">Jana Sijil Perkahwinan →</a>
        </div>
    </div>
</div>

<script>
function bcSend() {
    var modal = document.getElementById('bcModal');
    modal.style.display = 'flex';
    var rows = document.querySelectorAll('#bcModal [id^="bcrow"]');
    var done = document.getElementById('bcDone');
    done.style.display = 'none';
    rows.forEach(function (row) {
        row.style.opacity = '.4';
        var ic = row.querySelector('.ic');
        ic.innerHTML = '○'; ic.style.background = '#E2E8F0';
    });
    var i = 0;
    function step() {
        if (i > 0) {
            var prev = rows[i - 1].querySelector('.ic');
            prev.innerHTML = '✓'; prev.style.background = '#16A34A';
        }
        if (i < rows.length) {
            rows[i].style.opacity = '1';
            rows[i].querySelector('.ic').innerHTML = '<span class="bcspin">◜</span>';
            i++;
            setTimeout(step, 1000);
        } else {
            done.style.display = 'block';
        }
    }
    step();
}
</script>
@endsection
