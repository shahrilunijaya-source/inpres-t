@extends('layouts.system', ['active' => 'abis', 'title' => 'ABIS · 1:N Biometric Matching'])

@section('content')
@php
$abis = $case['abis'] ?? ['result' => 'MATCH', 'tone' => 'green', 'score' => 99.90, 'time' => 3.40, 'summary' => '', 'candidates' => []];
$recent = [
    ['ts' => '2026-05-28 11:01:02', 'subj' => $case['doc_label'] . ' · ' . $case['name'], 'score' => $abis['score'], 'time' => $abis['time'], 'result' => $abis['result'], 'tone' => $abis['tone'], 'current' => true],
    ['ts' => '2026-05-29 20:41:18', 'subj' => 'MyKad Gantian · Encik Arjun', 'score' => 99.94, 'time' => 3.18, 'result' => 'MATCH', 'tone' => 'green'],
    ['ts' => '2026-05-29 20:39:02', 'subj' => 'Kelahiran · Baby Adam', 'score' => 0.00, 'time' => 4.71, 'result' => 'NO MATCH', 'tone' => 'amber'],
    ['ts' => '2026-05-29 20:37:45', 'subj' => 'MyKad Kali Pertama · Siti Aminah', 'score' => 0.21, 'time' => 4.02, 'result' => 'NO MATCH', 'tone' => 'amber'],
    ['ts' => '2026-05-29 20:35:11', 'subj' => 'MyPR · Raj Kumar', 'score' => 98.71, 'time' => 3.54, 'result' => 'MATCH', 'tone' => 'green'],
    ['ts' => '2026-05-29 20:31:29', 'subj' => 'Siasatan Pendua · INV-2026-1129', 'score' => 99.99, 'time' => 2.89, 'result' => 'DUP FOUND', 'tone' => 'red'],
    ['ts' => '2026-05-29 20:29:00', 'subj' => 'MyKad Gantian · Faridah binti Hasan', 'score' => 99.88, 'time' => 3.61, 'result' => 'MATCH', 'tone' => 'green'],
];
// Case-file mode (Semak): only this applicant's match. Browse (sidebar): full live log.
$threaded = !empty($case['threaded']);
if ($threaded) {
    $recent = array_values(array_filter($recent, fn ($r) => !empty($r['current'])));
}
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Para 2.2 · Modul 04 ms.55</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">ABIS · 1:N Biometric Matching</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Automated Biometric Identification System · 30M+ rekod · GPU NVIDIA H200</p>
    </header>

    {{-- Hero: current case 1:N probe result — only when threaded from Semak --}}
    @if($threaded)
    @php
        $heroTone = ['green' => ['#ECFDF5','#16A34A','#15803D'], 'amber' => ['#FFFBEB','#F59E0B','#B45309'], 'red' => ['#FEF2F2','#EF4444','#B91C1C']][$abis['tone']] ?? ['#ECFDF5','#16A34A','#15803D'];
    @endphp

    {{-- Interactive: run the 1:N match against the gallery --}}
    <div id="abisScan" style="background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px 22px; margin-bottom: 18px;">
        <div style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; text-transform: uppercase; color: #1E40AF; font-weight: 700;">Padanan Biometrik 1:N</div>
                <div style="font-size: 16px; font-weight: 700; color: var(--ink-navy); margin-top: 2px;">{{ $case['name'] }} · {{ $case['ic'] }}</div>
                <div style="font-size: 12.5px; color: #475569; margin-top: 4px;">Bandingkan templat biometrik subjek dengan <strong>30,847,221</strong> rekod ABIS (GPU NVIDIA H200).</div>
            </div>
            <button type="button" id="abisRunBtn" onclick="abisRun()" style="background: #1E3A8A; color: #fff; border: 0; padding: 12px 22px; border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer; white-space: nowrap;">Jalankan Padanan 1:N →</button>
        </div>
        <div id="abisProgress" style="display: none; margin-top: 16px;">
            <div style="display: flex; justify-content: space-between; font-size: 11.5px; color: #475569; margin-bottom: 5px;">
                <span><span id="abisScanned">0</span> / 30,847,221 templat diimbas</span>
                <span id="abisPct" style="font-family: ui-monospace, monospace; font-weight: 700;">0%</span>
            </div>
            <div style="height: 8px; background: #E5E7EB; border-radius: 999px; overflow: hidden;">
                <div id="abisBar" style="height: 100%; width: 0; background: linear-gradient(90deg,#1E3A8A,#3B82F6); transition: width .2s;"></div>
            </div>
            <div id="abisStage" style="font-size: 11px; color: #6B7280; margin-top: 6px; font-family: ui-monospace, monospace;">Memulakan enjin padanan…</div>
        </div>
    </div>

    <div id="abisResult" style="display: none;">
    <section style="background: {{ $heroTone[0] }}; border: 1px solid {{ $heroTone[1] }}; border-radius: 12px; padding: 18px 22px; margin-bottom: 18px;">
        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; text-transform: uppercase; color: {{ $heroTone[2] }}; font-weight: 700;">Padanan 1:N Semasa</div>
                <div style="font-size: 19px; font-weight: 700; color: var(--ink-navy); margin-top: 2px;">{{ $case['name'] }}</div>
                <div style="font-size: 12px; color: #475569; font-family: ui-monospace, monospace;">{{ $case['ic'] }} · {{ $case['reference'] }}</div>
                <div style="font-size: 12.5px; color: #334155; margin-top: 8px;">{{ $abis['summary'] }}</div>
            </div>
            <div style="text-align: center; padding: 0 16px;">
                <div style="font-size: 34px; font-weight: 800; color: {{ $heroTone[2] }};">{{ number_format($abis['score'], 2) }}%</div>
                <div style="font-size: 11px; color: #6B7280;">skor padanan · {{ number_format($abis['time'], 2) }}s</div>
            </div>
            <div style="background: {{ $heroTone[1] }}; color: #fff; padding: 8px 18px; border-radius: 999px; font-size: 15px; font-weight: 700;">{{ $abis['result'] }}</div>
        </div>

        @if(!empty($abis['candidates']))
            <div style="margin-top: 14px; border-top: 1px dashed {{ $heroTone[1] }}55; padding-top: 12px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; text-transform: uppercase; color: #6B7280; margin-bottom: 8px;">Galeri Calon · Top {{ count($abis['candidates']) }} dari 30,847,221 rekod</div>
                <div style="display: grid; grid-template-columns: repeat({{ min(count($abis['candidates']), 4) }}, 1fr); gap: 10px;">
                    @foreach($abis['candidates'] as $cand)
                        @php($ct = ['green' => ['#DCFCE7','#15803D'], 'amber' => ['#FEF3C7','#B45309'], 'red' => ['#FEE2E2','#B91C1C']][$cand['tone']] ?? ['#F1F5F9','#475569'])
                        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px; text-align: center;">
                            <div style="aspect-ratio: 1; border-radius: 8px; margin-bottom: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                @include('system._mykad-photo', ['ic' => $cand['id'], 'name' => ($cand['rank'] === 1 ? ($case['name'] ?? 'x') : $cand['id']), 'shape' => 'circle', 'size' => 72, 'gender' => $case['gender'] ?? 'F'])
                            </div>
                            <div style="font-size: 10px; color: #6B7280;">#{{ $cand['rank'] }} · {{ $cand['id'] }}</div>
                            <div style="display: inline-block; background: {{ $ct[0] }}; color: {{ $ct[1] }}; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 700; margin: 4px 0;">{{ number_format($cand['score'], 2) }}%</div>
                            <div style="font-size: 10px; color: #6B7280; line-height: 1.3;">{{ $cand['note'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div style="margin-top: 12px; font-size: 12.5px; color: {{ $heroTone[2] }}; font-weight: 600;">✓ Tiada calon melebihi ambang padanan — subjek disahkan unik dalam pangkalan data 30M+ rekod.</div>
        @endif
    </section>
    </div>{{-- /#abisResult --}}
    @endif

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Rekod Biometrik','30,847,221','10 cap jari + muka + iris','#1E40AF'],['Padanan Terakhir','3.18s','SLA: < 5s','#10B981'],['GPU Aktif','H200 · 141GB','NEC NeoFace v9.2','#6366F1'],['Padanan Hari Ini','4,821','7 pendua dikesan','#FF6B35']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 22px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid #E5E7EB;">
                <h3 style="margin: 0; font-size: 14px;">Padanan Terkini · Log Masa Nyata</h3>
                <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">● Live</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead style="background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr><th style="padding: 10px 16px; text-align: left;">Masa</th><th style="padding: 10px 16px; text-align: left;">Subjek</th><th style="padding: 10px 16px; text-align: left;">Skor</th><th style="padding: 10px 16px; text-align: left;">Tempoh</th><th style="padding: 10px 16px; text-align: left;">Keputusan</th></tr>
                </thead>
                <tbody>
                @foreach($recent as $r)
                    @php($tones = ['green' => ['#DCFCE7','#15803D'], 'amber' => ['#FEF3C7','#B45309'], 'red' => ['#FEE2E2','#B91C1C']])
                    <tr style="border-top: 1px solid #F1F5F9; {{ !empty($r['current']) ? 'background: #EEF2FF; box-shadow: inset 3px 0 0 #6366F1;' : '' }}">
                        <td style="padding: 10px 16px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $r['ts'] }}</td>
                        <td style="padding: 10px 16px;">{{ $r['subj'] }}@if(!empty($r['current']))<span style="margin-left: 8px; background: #6366F1; color: #fff; padding: 1px 7px; border-radius: 999px; font-size: 9.5px; font-weight: 700;">KES SEMASA</span>@endif</td>
                        <td style="padding: 10px 16px; font-weight: 700;">{{ number_format($r['score'], 2) }}%</td>
                        <td style="padding: 10px 16px;">{{ number_format($r['time'], 2) }}s</td>
                        <td style="padding: 10px 16px;"><span style="background: {{ $tones[$r['tone']][0] }}; color: {{ $tones[$r['tone']][1] }}; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ $r['result'] }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px;">
            <h3 style="margin: 0 0 12px; font-size: 14px;">Pelan Padanan</h3>
            @foreach([['Capture Biometrik','#22c55e','1.2s'],['Quality Check ISO 19794','#22c55e','0.3s'],['Liveness PAD 30107','#22c55e','0.4s'],['ABIS 1:N (GPU H200)','#3b82f6','3.2s'],['Score Fusion','#22c55e','0.1s'],['Audit Submit HL Fabric','#a855f7','0.6s']] as $step)
                <div style="display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px dashed #F1F5F9;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $step[1] }};"></span>
                    <span style="flex: 1; font-size: 13px;">{{ $step[0] }}</span>
                    <span style="font-family: ui-monospace, monospace; font-size: 11px; color: #6B7280;">{{ $step[2] }}</span>
                </div>
            @endforeach
            <div style="margin-top: 14px; font-size: 11.5px; color: #6B7280;">ISO/IEC 19794 · ISO/IEC 30107 PAD · NIST MINEX</div>
        </section>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi LAMPIRAN A Para 2.2 (Modul 04 ms.55):</strong>
        <span style="color: #475569; font-size: 13px;"> ABIS 1:N matching wajib bagi setiap permohonan MyKad untuk halang pendua identiti. CPU ambil ~5 minit untuk banding 30M rekod; GPU NVIDIA H200 tensor cores ambil < 5 saat.</span>
    </div>

    @if($threaded && ($case['key'] ?? '') === 'birth')
        {{-- Birth: parents verified unique → proceed to Salasilah (where birth is registered to blockchain) --}}
        <div style="margin-top: 16px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <div style="font-weight: 700; color: #B45309; font-size: 14px;">Biometrik ibu bapa disahkan — teruskan ke Salasilah</div>
                <div style="font-size: 12.5px; color: #475569; margin-top: 2px;">Bayi disahkan bukan pendaftaran berganda. Auto-link ke salasilah keluarga, kemudian daftar &amp; rekod ke blockchain.</div>
            </div>
            <a href="{{ route('system.familytree', ['ref' => $case['reference']]) }}" style="text-decoration: none; background: var(--ink-navy); color: #fff; padding: 11px 20px; border-radius: 9px; font-size: 13px; font-weight: 700;">Teruskan ke Salasilah →</a>
        </div>
    @endif

    @if($threaded && ($case['key'] ?? '') === 'mykad')
        {{-- MyKad: identity confirmed against existing enrolment → proceed to card production --}}
        <div style="margin-top: 16px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <div style="font-weight: 700; color: #B45309; font-size: 14px;">Identiti disahkan terhadap enrolmen sedia ada — teruskan ke pengeluaran kad</div>
                <div style="font-size: 12.5px; color: #475569; margin-top: 2px;">Padanan 1:N mengesahkan pemohon ialah pemilik sah MyKad yang hilang. Hantar ke CLMS untuk personalisation kad gantian.</div>
            </div>
            <a href="{{ route('system.clms', ['ref' => $case['reference']]) }}" style="text-decoration: none; background: var(--ink-navy); color: #fff; padding: 11px 20px; border-radius: 9px; font-size: 13px; font-weight: 700;">Teruskan ke CLMS →</a>
        </div>
    @endif
</div>

<style>@keyframes abisFade { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }</style>
<script>
function abisRun() {
    var btn = document.getElementById('abisRunBtn'), prog = document.getElementById('abisProgress');
    var scanned = document.getElementById('abisScanned'), pct = document.getElementById('abisPct'), bar = document.getElementById('abisBar'), stage = document.getElementById('abisStage');
    if (!btn) return;
    btn.disabled = true; btn.style.opacity = '.55'; btn.style.cursor = 'wait'; btn.textContent = 'Mengimbas…';
    prog.style.display = 'block';
    var total = 30847221, dur = 2200, t0 = null;
    var stages = [[0, 'Memulakan enjin padanan…'], [18, 'Ekstrak minutiae + templat muka…'], [42, 'Carian GPU H200 · tensor cores…'], [72, 'Score fusion + ranking calon…'], [93, 'Submit audit chaincode…']];
    function frame(ts) {
        if (t0 === null) t0 = ts;
        var p = Math.min(1, (ts - t0) / dur);
        scanned.textContent = Math.floor(p * total).toLocaleString('en-US');
        var pc = Math.floor(p * 100); pct.textContent = pc + '%'; bar.style.width = pc + '%';
        for (var i = stages.length - 1; i >= 0; i--) { if (pc >= stages[i][0]) { stage.textContent = stages[i][1]; break; } }
        if (p < 1) { requestAnimationFrame(frame); }
        else {
            document.getElementById('abisScan').style.display = 'none';
            var r = document.getElementById('abisResult'); r.style.display = 'block'; r.style.animation = 'abisFade .5s ease';
        }
    }
    requestAnimationFrame(frame);
}
</script>
@endsection
