@extends('layouts.system', ['active' => 'familytree', 'title' => 'Salasilah Keluarga'])

@section('content')
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Family Tree Service · Neo4j Graph</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Salasilah Keluarga · Family Tree Service</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Auto-update dari Modul Kelahiran, Perkahwinan, Kematian, Anak Angkat</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Nod Individu','32,841,287','setiap warganegara','#1E40AF'],['Edge Hubungan','187,212,438','ibu/bapa/anak/pasangan','#15803D'],['Query Hari Ini','18,421','dari 6 modul','#B45309'],['Latency Purata','67ms','Neo4j cluster','#6366F1']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 22px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    @if(!empty($case['threaded']))
    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px; margin-top: 18px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
            <h3 style="margin: 0; font-size: 14px;">Pohon Salasilah · {{ $case['name'] }} · {{ $case['ic'] }}</h3>
            <span style="background: #FEF3C7; color: #B45309; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ $case['key'] === 'marriage' ? 'Semakan pra-kahwin' : ($case['key'] === 'birth' ? 'Auto-link kelahiran' : 'Salasilah subjek') }}</span>
        </div>

        @php
            $node = function (string $name, string $sym, string $role, string $color, bool $hero = false, ?string $ic = null, ?string $dob = null) {
                $size = $hero ? '66px' : '54px';
                $bd = $hero ? '4px solid #D97706' : '3px solid ' . $color;
                // MyKad-pulled portrait (photographic face seeded per IC, gender from symbol)
                $seed = abs(crc32(($ic ?? '') . $name));
                $hue = $seed % 360;
                $skin = 'hsl(' . (($hue % 40) + 20) . ',35%,62%)';
                $nn = ' ' . mb_strtolower($name) . ' ';
                if (str_contains($nn, ' bin ') || str_contains($nn, ' binti ') || preg_match('/\b(muhammad|mohd|nur|siti|ahmad|abdul|nor|wan|amir|hamzah|farah|nadia|salleh|roslan|karim|halimah|rohana|idris|wahab|omar)\b/u', $nn)) {
                    $eth = 'malay';
                } elseif (preg_match('#a/[lp]#u', $nn) || preg_match('/\b(kumar|raj|nair|pillai|singh|kaur|devi|subramaniam|krishnan|maniam|gopal|ramasamy|govindasamy|anand|munusamy|rajan|pillay|samy)\b/u', $nn)) {
                    $eth = 'indian';
                } elseif (preg_match('/\b(tan|lim|wong|lee|ong|goh|teoh|chan|lau|yeoh|loh|chua|ng|chong|chen|khoo|yap|gan|low|chin|wei|hui|mei|chee|boon|kok|hwa|peng|seng|hao|jie|shan|ying|yen|qi|pei|siew|goh|lily|robert)\b/u', $nn)) {
                    $eth = 'chinese';
                } else {
                    $eth = 'default';
                }
                $skinHex = ['malay' => 'cb9e6e', 'indian' => '8d5524', 'chinese' => 'f2d3b1', 'default' => 'd8a47f'][$eth];
                $g = ($sym === '♀') ? 'F' : 'M';
                $src = 'https://api.dicebear.com/9.x/personas/svg?seed=' . urlencode(($ic ?? $name) . $g . $eth) . '&skinColor=' . $skinHex . '&backgroundColor=cdddf0';
                $photo = '<div style="position:relative;width:' . $size . ';height:' . $size . ';margin:0 auto;">'
                    . '<div style="width:100%;height:100%;border-radius:50%;overflow:hidden;border:' . $bd . ';position:relative;background:linear-gradient(160deg,hsl(' . $hue . ',48%,90%),hsl(' . (($hue + 35) % 360) . ',42%,76%));">'
                    . '<svg viewBox="0 0 100 100" width="100%" height="100%" preserveAspectRatio="xMidYMax meet" style="position:absolute;inset:0;"><circle cx="50" cy="40" r="19" fill="' . $skin . '"/><path d="M18 100 C18 73 35 62 50 62 C65 62 82 73 82 100 Z" fill="' . $skin . '"/></svg>'
                    . '<img src="' . $src . '" loading="lazy" referrerpolicy="no-referrer" onerror="this.style.display=\'none\'" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>'
                    . '</div>'
                    . '<span style="position:absolute;bottom:-2px;right:-2px;width:18px;height:18px;border-radius:50%;background:' . $color . ';color:#fff;font-size:11px;display:flex;align-items:center;justify-content:center;border:2px solid #fff;">' . $sym . '</span>'
                    . '</div>';
                $meta = '';
                if ($ic) {
                    $meta .= '<div style="font-family:ui-monospace,monospace;font-size:9.5px;color:#94A3B8;margin-top:1px;">' . e($ic) . '</div>';
                }
                if ($dob) {
                    $meta .= '<div style="font-size:9px;color:#94A3B8;">Lahir ' . e(\Carbon\Carbon::parse($dob)->format('d/m/Y')) . '</div>';
                }
                return '<div style="text-align:center;">'
                    . $photo
                    . '<div style="font-size:' . ($hero ? '12.5' : '11') . 'px;font-weight:' . ($hero ? '700' : '600') . ';margin-top:4px;">' . e($name) . '</div>'
                    . '<div style="color:' . ($hero ? '#D97706' : '#6B7280') . ';font-size:10px;">' . e($role) . '</div>'
                    . $meta
                    . '</div>';
            };
        @endphp
        <div style="display: flex; flex-direction: column; align-items: center; gap: 20px; padding: 30px 0; background: linear-gradient(180deg, #F8FAFC, #FFF); border-radius: 10px;">

            @if($case['key'] === 'marriage')
                {{-- Civil marriage: two families joining --}}
                <div style="display: flex; gap: 120px;">
                    <div style="display: flex; gap: 22px;">
                        {!! $node('Robert Tan', '♂', 'Bapa (suami)', '#3B82F6', false, '600318-14-5093', '1960-03-18') !!}
                        {!! $node('Lily Goh', '♀', 'Ibu (suami)', '#EC4899', false, '631122-14-5128', '1963-11-22') !!}
                    </div>
                    <div style="display: flex; gap: 22px;">
                        {!! $node('Anthony Pereira', '♂', 'Bapa (isteri)', '#3B82F6', false, '590704-07-5331', '1959-07-04') !!}
                        {!! $node('Maria Pereira', '♀', 'Ibu (isteri)', '#EC4899', false, '620215-07-5246', '1962-02-15') !!}
                    </div>
                </div>
                <div style="color: #CBD5E1; font-size: 12px;">│ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; │</div>
                <div style="display: flex; align-items: center; gap: 18px;">
                    {!! $node($case['groom']['name'], '♂', 'Bakal suami · SUBJEK', '#1E3A8A', true, $case['groom']['ic'] ?? null, $case['groom']['dob'] ?? null) !!}
                    <div style="color: #D97706; font-size: 11px; font-weight: 700; text-align:center;">═══<br>kaveat<br>21 hari<br>═══</div>
                    {!! $node($case['bride']['name'], '♀', 'Bakal isteri', '#BE185D', true, $case['bride']['ic'] ?? null, $case['bride']['dob'] ?? null) !!}
                </div>

            @elseif($case['key'] === 'birth')
                @php
                    // Race-consistent grandparents: use the case-provided lineage when present
                    // (dynamic applicants), else the Malay anchor family.
                    $lin = $case['lineage'] ?? [
                        ['name' => 'Roslan bin Omar',     'sym' => '♂', 'role' => 'Datuk (sebelah bapa)'],
                        ['name' => 'Halimah binti Wahab', 'sym' => '♀', 'role' => 'Nenek (sebelah bapa)'],
                        ['name' => 'Salleh bin Karim',    'sym' => '♂', 'role' => 'Datuk (sebelah ibu)'],
                        ['name' => 'Rohana binti Idris',  'sym' => '♀', 'role' => 'Nenek (sebelah ibu)'],
                    ];
                    $babySym = (($case['gender'] ?? 'F') === 'F') ? '♀' : '♂';
                @endphp
                {{-- Birth: newborn auto-linked to parents + grandparents --}}
                <div style="display: flex; gap: 60px;">
                    @foreach($lin as $gp)
                        {!! $node($gp['name'], $gp['sym'], $gp['role'], $gp['sym'] === '♂' ? '#3B82F6' : '#EC4899') !!}
                    @endforeach
                </div>
                <div style="color: #CBD5E1; font-size: 12px;">│ &nbsp;&nbsp;&nbsp; │</div>
                <div style="display: flex; align-items: center; gap: 18px;">
                    {!! $node($case['father']['name'], '♂', 'Bapa', '#1E3A8A', false, $case['father']['ic'] ?? null, $case['father']['dob'] ?? null) !!}
                    <div style="color: #94A3B8;">═ kahwin ═</div>
                    {!! $node($case['mother']['name'], '♀', 'Ibu', '#BE185D', false, $case['mother']['ic'] ?? null, $case['mother']['dob'] ?? null) !!}
                </div>
                <div style="color: #CBD5E1; font-size: 12px;">│</div>
                {!! $node($case['name'], $babySym, 'Bayi · SUBJEK (baru lahir)', '#16A34A', true, $case['ic'] ?? null, $case['dob'] ?? null) !!}
                <div style="margin-top: 6px; font-size: 11.5px; color: #15803D; background:#ECFDF5; border:1px solid #BBF7D0; border-radius:8px; padding:8px 14px;">✓ Auto-link ke 2 ibu bapa + 4 datuk nenek sebaik rekod kelahiran dicipta.</div>

            @else
                {{-- MyKad subject: lineage of the cardholder --}}
                <div style="display: flex; gap: 60px;">
                    {!! $node('Lim Ah Seng', '♂', 'Bapa', '#3B82F6') !!}
                    {!! $node('Wong Siew Mei', '♀', 'Ibu', '#EC4899') !!}
                </div>
                <div style="color: #CBD5E1; font-size: 12px;">│</div>
                {!! $node($case['name'], '♀', 'SUBJEK (pemegang kad)', '#1E3A8A', true, $case['ic'] ?? null, $case['dob'] ?? null) !!}
            @endif
        </div>

        @if($case['key'] === 'marriage')
            @php
                $gAge = !empty($case['groom']['dob']) ? \Carbon\Carbon::parse($case['groom']['dob'])->age : 30;
                $bAge = !empty($case['bride']['dob']) ? \Carbon\Carbon::parse($case['bride']['dob'])->age : 30;
                $checks = [
                    ['Pertalian Darah · Jadual Akta 164 S.11', 'Bukan dalam darjah dilarang — tiada nenek-moyang sepunya', true],
                    ['Perkahwinan Aktif Sedia Ada', 'Kedua-dua pihak bujang — tiada rekod perkahwinan belum bubar', true],
                    ['Umur Minimum · Akta 164 S.10', 'Lelaki ' . $gAge . ' · Perempuan ' . $bAge . ' tahun (≥ 18)', true],
                    ['Senarai Hitam JPN', 'Bersih — tiada sekatan permohonan', true],
                ];
            @endphp
            <div style="margin-top: 16px; border: 1px solid #BBF7D0; background: #F0FDF4; border-radius: 12px; padding: 16px 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h3 style="margin: 0; font-size: 14px; color: #14532D;">Semakan Halangan Perkahwinan · Akta 164</h3>
                    <span style="background: #16A34A; color: #fff; padding: 5px 14px; border-radius: 999px; font-size: 12px; font-weight: 700;">✓ LAYAK BERKAHWIN</span>
                </div>
                @foreach($checks as $c)
                    <div style="display: flex; align-items: center; gap: 12px; padding: 9px 0; border-bottom: 1px dashed #BBF7D0;">
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #DCFCE7; color: #15803D; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0;">✓</span>
                        <div style="flex: 1;">
                            <div style="font-size: 13px; font-weight: 600; color: var(--ink-navy);">{{ $c[0] }}</div>
                            <div style="font-size: 11.5px; color: #6B7280;">{{ $c[1] }}</div>
                        </div>
                        <span style="background: #DCFCE7; color: #15803D; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">LULUS</span>
                    </div>
                @endforeach
                <div style="margin-top: 10px; font-family: ui-monospace, monospace; font-size: 11px; color: #475569; background: #fff; border: 1px solid #E5E7EB; border-radius: 8px; padding: 8px 12px;">
                    <span style="color:#7C3AED;">Neo4j</span> MATCH (a)-[:DARAH*1..6]-(b) → traversal 6 generasi · 0 nenek-moyang sepunya · <strong>67ms</strong>
                </div>
            </div>
        @endif
    </section>
    @else
        {{-- Browse (sidebar): pick whose salasilah to view — not fixed to one family --}}
        @php
            $docMeta = [
                'birth'    => ['KEL', '#15803D'],
                'marriage' => ['KAH', '#BE185D'],
                'mykad'    => ['KAD', '#D97706'],
            ];
        @endphp
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px; margin-top: 18px;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 14px; flex-wrap: wrap;">
                <div>
                    <h3 style="margin: 0; font-size: 14px;">Carian Salasilah</h3>
                    <div style="font-size: 12px; color: #6B7280; margin-top: 2px;">Pilih individu / permohonan untuk lihat pohon salasilahnya.</div>
                </div>
                <div style="position: relative;">
                    <input type="text" placeholder="Cari nama atau No. KP…" style="width: 280px; max-width: 60vw; padding: 9px 14px; border: 1px solid #E5E7EB; border-radius: 9px; font-size: 13px; background: #F8FAFC;" oninput="ftFilter(this.value)">
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; font-size: 13px;" id="ftDir">
                <thead style="background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase; letter-spacing: .4px;">
                    <tr>
                        <th style="text-align: left; padding: 10px 14px;">Individu</th>
                        <th style="text-align: left; padding: 10px 14px;">No. KP</th>
                        <th style="text-align: left; padding: 10px 14px;">Modul</th>
                        <th style="text-align: left; padding: 10px 14px;">Rujukan</th>
                        <th style="padding: 10px 14px;"></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($directory as $app)
                    @php $dm = $docMeta[$app->doc_type] ?? ['?', '#64748B']; @endphp
                    <tr class="ft-row" data-find="{{ strtolower($app->applicant_name . ' ' . $app->applicant_ic) }}" style="border-top: 1px solid #F1F5F9;">
                        <td style="padding: 11px 14px; font-weight: 600; color: var(--ink-navy);">{{ $app->applicant_name }}</td>
                        <td style="padding: 11px 14px; font-family: ui-monospace, monospace; font-size: 11.5px; color: #475569;">{{ $app->applicant_ic }}</td>
                        <td style="padding: 11px 14px;"><span style="background: {{ $dm[1] }}1a; color: {{ $dm[1] }}; padding: 2px 9px; border-radius: 999px; font-size: 10.5px; font-weight: 700;">{{ $dm[0] }}</span></td>
                        <td style="padding: 11px 14px; font-family: ui-monospace, monospace; font-size: 11px; color: #6B7280;">{{ $app->reference_number }}</td>
                        <td style="padding: 11px 14px; text-align: right;">
                            <a href="{{ route('system.familytree', ['ref' => $app->reference_number]) }}" style="text-decoration: none; background: var(--pine); color: #fff; padding: 7px 13px; border-radius: 7px; font-size: 12px; font-weight: 700; white-space: nowrap;">Lihat Salasilah →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding: 24px; text-align: center; color: #94A3B8;">Tiada permohonan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </section>
        <script>
            function ftFilter(q) {
                q = (q || '').toLowerCase();
                document.querySelectorAll('#ftDir .ft-row').forEach(function (r) {
                    r.style.display = r.dataset.find.indexOf(q) !== -1 ? '' : 'none';
                });
            }
        </script>
    @endif

    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px; margin-top: 18px;">
        <h3 style="margin: 0 0 10px; font-size: 14px;">Modul Pengguna Family Tree</h3>
        @foreach([['Modul Perkahwinan · Hubungan Darah','Cek adik beradik / sedarah sebelum kaveat'],['Modul Pencen · Ahli Waris','Auto-link suami isteri + anak'],['Modul KWSP · Penama','Validate hubungan saudara'],['Modul Tabung Pendidikan','Auto-eligibility anak tanggungan'],['Modul Warisan · Probate','Bukti hubungan untuk geran'],['Modul Siasatan · Identity Theft','Cek silang keluarga']] as $u)
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #F1F5F9;">
                <div style="font-weight: 600; font-size: 13px;">{{ $u[0] }}</div>
                <div style="color: #6B7280; font-size: 12px;">{{ $u[1] }}</div>
            </div>
        @endforeach
    </section>

    {{-- Birth: last step before Sijil — register + blockchain passthrough → certificate --}}
    @if(($case['key'] ?? '') === 'birth')
        @php $bcBlock = $case['blockchain']['events'][0]['block'] ?? ($case['sijil']['block'] ?? 1925014); @endphp
        <div style="margin-top: 16px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <div style="font-weight: 700; color: #B45309; font-size: 14px;">Salasilah dikemaskini — sedia untuk pendaftaran</div>
                <div style="font-size: 12.5px; color: #475569; margin-top: 2px;">Daftarkan kelahiran &amp; catat rekod kekal ke blockchain, kemudian jana Sijil JPN.LM05 + MyKid.</div>
            </div>
            <button type="button" onclick="bcSend()" style="background: #7C3AED; color: #fff; border: 0; padding: 10px 18px; border-radius: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer;">Daftar &amp; Hantar ke Blockchain</button>
        </div>
    @endif

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi · Graph Database:</strong>
        <span style="color: #475569; font-size: 13px;"> Hubungan keluarga ialah masalah graf, bukan jadual. Neo4j cluster sokong query traversal "ada hubungan darah dengan bakal pasangan?" dalam <100ms walaupun pohon ada 6+ generasi. SQL biasa tidak praktikal untuk traversal mendalam.</span>
    </div>
</div>

@if(($case['key'] ?? '') === 'birth')
<style>@keyframes bcspin{to{transform:rotate(360deg)}} .bcspin{display:inline-block;animation:bcspin .8s linear infinite}</style>
<div id="bcModal" style="display:none; position:fixed; inset:0; background:rgba(var(--ink-navy-rgb),.55); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:26px 30px; width:460px; max-width:92vw; box-shadow:0 24px 70px rgba(0,0,0,.35);">
        <div style="display:flex; align-items:center; gap:11px; margin-bottom:18px;">
            <div style="width:38px; height:38px; border-radius:9px; background:#F3E8FF; display:flex; align-items:center; justify-content:center;">@include('system._icon', ['name' => 'link', 'color' => '#7C3AED', 'size' => 19])</div>
            <div>
                <div style="font-weight:700; font-size:15px; color:var(--ink-navy);">Pendaftaran Kelahiran</div>
                <div style="font-size:12px; color:#6B7280;">No. Daftar {{ $case['sijil']['reg_no'] ?? $case['cert_no'] ?? '—' }}</div>
            </div>
        </div>
        @php
            $bcRows = [
                ['INSERT kelahiran_master · Oracle RAC', 'TDE encrypted · SCN'],
                ['Submit audit chaincode · kelahiran-cc', 'inpres-main channel'],
                ['Blok #' . $bcBlock . ' dilog · immutable', '612ms · bukti mahkamah S.90A'],
                ['Publish event.kelahiran.registered', '7 modul: MyKid, warganegara, LHDN…'],
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
            <div style="background:#ECFDF5; color:#15803D; border:1px solid #BBF7D0; border-radius:8px; padding:9px; font-size:12.5px; font-weight:700; margin-bottom:12px;">✓ Rekod kekal · kelayakan MyKid dicetus</div>
            <a href="{{ route('system.sijil', ['ref' => $case['reference']]) }}" style="display:inline-block; text-decoration:none; background:#16A34A; color:#fff; padding:11px 22px; border-radius:9px; font-size:13px; font-weight:700;">Jana Sijil Kelahiran →</a>
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
    rows.forEach(function (row) { row.style.opacity = '.4'; var ic = row.querySelector('.ic'); ic.innerHTML = '○'; ic.style.background = '#E2E8F0'; });
    var i = 0;
    function step() {
        if (i > 0) { var prev = rows[i - 1].querySelector('.ic'); prev.innerHTML = '✓'; prev.style.background = '#16A34A'; }
        if (i < rows.length) {
            rows[i].style.opacity = '1';
            rows[i].querySelector('.ic').innerHTML = '<span class="bcspin">◜</span>';
            i++; setTimeout(step, 1000);
        } else { done.style.display = 'block'; }
    }
    step();
}
</script>
@endif
@endsection
