@php
    use App\Models\Application;
@endphp

@extends('layouts.system', ['active' => 'tapisan', 'title' => $app->reference_number])

@section('content')
@php
    $isLate = $app->sla_state === 'breached';
    $statusLabel = Application::STAGE_LABELS[$app->status] ?? $app->status;
    $docLabel = Application::DOC_LABELS[$app->doc_type] ?? $app->doc_type;
    $statusClass = [
        'received' => 'pill--received',
        'verified' => 'pill--verified',
        'officer_review' => 'pill--review',
        'approved' => 'pill--approved',
        'issued' => 'pill--issued',
        'rejected' => 'pill--rejected',
    ][$app->status] ?? 'pill--received';
    $canDecide = in_array($app->status, ['received', 'verified', 'officer_review'], true);
@endphp

{{-- ==== Breadcrumb nav ==== --}}
<div class="tap-nav">
    <a class="tap-nav__back" href="{{ route('system.tapisan') }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m11 18-6-6 6-6"/></svg>
        Kembali ke senarai
    </a>
    <div class="tap-nav__crumb">
        <span>Permohonan</span> <span>/</span> <span>{{ $docLabel }}</span> <span>/</span> <span class="now">{{ $app->reference_number }}</span>
    </div>
    <div class="tap-nav__cluster">
        <span class="tap-nav__step">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            Langkah {{ $app->stageIndex() + 1 }} / 5
        </span>
    </div>
</div>

{{-- ==== Title block ==== --}}
<div class="tap-title">
    <div>
        <h1 class="tap-title__h1">{{ $app->applicant_name }}<span class="dot"></span></h1>
        <p class="tap-title__sub">
            Rujukan <strong>{{ $app->reference_number }}</strong>
            · Diterima {{ $app->created_at->format('d M Y, H:i') }}
            · {{ $app->created_at->diffForHumans() }}
        </p>
        <div class="tap-title__chips">
            <span class="tap-title__chip">{{ $docLabel }}</span>
            <span class="pill {{ $statusClass }}">{{ $statusLabel }}</span>
            @if($isLate)
                <span class="tap-title__chip" style="background: var(--orange-soft); color: var(--orange);">SLA LEWAT</span>
            @endif
            <span class="tap-title__chip"><span class="mono">Skor AI · {{ number_format(($app->ai_score ?? 0) * 100, 0) }}%</span></span>
        </div>
    </div>
    <div class="tap-title__meta">
        @if($isLate)
            <div class="late" style="color: var(--orange); font-weight: 700; font-size: 13px;">Lewat {{ $app->created_at->diffForHumans(null, true) }}</div>
        @elseif($app->ai_eta)
            <div class="due">ETA · {{ $app->ai_eta->format('d M, H:i') }}</div>
        @endif
        <div style="margin-top: 4px; font-family: var(--mono);">{{ $app->updated_at->format('Y-m-d H:i') }}</div>
    </div>
</div>

{{-- ==== Sistem Wajib quick-links (LAMPIRAN A) — threaded to this case ==== --}}
@php
    $caseKey = in_array($app->doc_type, ['birth', 'marriage', 'mykad'], true) ? $app->doc_type : 'mykad';
    $caseSteps = config("demo_cases.$caseKey.steps", []);
    $swMap = [
        'hospital'   => ['system.hospital',   'Pra-Daftar Hospital'],
        'borang'     => ['system.borang',     'Borang Daftar'],
        'lapor'      => ['system.lapor',      'Lapor Kehilangan'],
        'biometric'  => ['system.biometric',  'Biometrik'],
        'abis'       => ['system.abis',       'ABIS 1:N'],
        'kaveat'     => ['system.kaveat',     'Kaveat 21 Hari'],
        'familytree' => ['system.familytree', 'Salasilah'],
        'upacara'    => ['system.upacara',    'Upacara & Daftar'],
        'sijil'      => ['system.sijil',      'Sijil Perkahwinan'],
        'clms'       => ['system.clms',       'CLMS Kad'],
        'kad'        => ['system.kad',        'Kad Dikeluarkan'],
        'blockchain' => ['system.blockchain', 'Blockchain'],
        'mydigital'  => ['system.mydigital',  'MyDigital ID'],
    ];
@endphp
@if(!empty($caseSteps))
<div style="background: #fff; border: 1px solid var(--line, #E5E7EB); border-radius: 12px; padding: 14px 18px; margin: 0 0 18px;">
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <span style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #6B7280; font-weight: 700;">Sistem Wajib · LAMPIRAN A →</span>
        @foreach($caseSteps as $i => $step)
            @php $m = $swMap[$step] ?? null; @endphp
            @if($m)
                <a href="{{ route($m[0], ['ref' => $app->reference_number]) }}"
                   style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; border-radius: 999px; padding: 6px 13px; font-size: 12px; font-weight: 600;">
                    <span style="font-family: ui-monospace, monospace; font-size: 10px; opacity: .7;">{{ $i + 1 }}</span>{{ $m[1] }}
                </a>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- ==== Body ==== --}}
<div class="tap-body">
    <div class="tap-body__main">

        @php
            $isMarriage = $app->doc_type === 'marriage';
            $groomC = $app->citizen;
            $brideC = ($isMarriage && $app->spouse_ic) ? \App\Models\Citizen::where('ic', $app->spouse_ic)->first() : null;
            $groomName = $groomC->full_name ?? explode(' & ', $app->applicant_name)[0];
            $brideName = $brideC->full_name ?? ($app->spouse_name ?? '—');
        @endphp

        @if($isMarriage)
            {{-- Both parties — adults with MyKad + biometric already on file --}}
            <div class="tap-card">
                <div class="tap-card__eyebrow">Pasangan Berkahwin · Perkahwinan Sivil (Bukan Islam)</div>
                <h3 class="tap-card__h">Kedua-dua pihak — MyKad & biometrik atas fail</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 10px;">
                    @foreach([['♂', 'Bakal Suami', $groomName, $app->applicant_ic, $groomC, '#1E3A8A', '#EFF6FF', '#DBEAFE'], ['♀', 'Bakal Isteri', $brideName, $app->spouse_ic, $brideC, '#BE185D', '#FDF2F8', '#FCE7F3']] as $p)
                        <div style="border: 1px solid {{ $p[7] }}; background: {{ $p[6] }}; border-radius: 12px; padding: 14px 16px;">
                            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 10px;">
                                @include('system._mykad-photo', ['ic' => $p[3], 'name' => $p[2], 'shape' => 'rect', 'size' => 104, 'gender' => ($p[0] === '♂' ? 'M' : 'F')])
                                <div>
                                    <div style="font-size: 10px; letter-spacing: 0.5px; text-transform: uppercase; color: #6B7280; font-weight: 700;">{{ $p[0] }} {{ $p[1] }}</div>
                                    <div style="font-weight: 700; font-size: 14px; color: var(--ink-navy);">{{ $p[2] }}</div>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 4px 0; border-top: 1px dashed #E5E7EB;"><span style="color:#6B7280;">No. MyKad</span><span style="font-family: ui-monospace, monospace; font-weight: 600;">{{ $p[3] }}</span></div>
                            @if($p[4])
                                <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 4px 0;"><span style="color:#6B7280;">Tarikh Lahir</span><span>{{ \Carbon\Carbon::parse($p[4]->dob)->format('d M Y') }}</span></div>
                            @endif
                            <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 4px 0;"><span style="color:#6B7280;">Biometrik</span><span style="background:#DCFCE7;color:#15803D;padding:1px 8px;border-radius:999px;font-size:10.5px;font-weight:600;">✓ Atas fail · NFIQ 96</span></div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 4px 0;"><span style="color:#6B7280;">Status Sivil</span><span style="background:#DCFCE7;color:#15803D;padding:1px 8px;border-radius:999px;font-size:10.5px;font-weight:600;">Bujang · disahkan</span></div>
                        </div>
                    @endforeach
                </div>
                <div class="tap-card__row" style="margin-top: 12px;">
                    <span class="k">Alamat</span>
                    <span class="v">{{ $app->applicant_address }}</span>
                </div>
            </div>
        @else
            {{-- Applicant card (single) --}}
            <div class="tap-card">
                <div class="tap-card__eyebrow">Maklumat Pemohon</div>
                <h3 class="tap-card__h">Identiti & alamat</h3>
                <div class="tap-card__row">
                    <span class="k">Nama Penuh</span>
                    <span class="v">{{ $app->applicant_name }}</span>
                </div>
                <div class="tap-card__row">
                    <span class="k">No. Kad Pengenalan</span>
                    <span class="v mono">{{ $app->applicant_ic }}</span>
                </div>
                @if($app->citizen)
                    <div class="tap-card__row">
                        <span class="k">Tarikh Lahir</span>
                        <span class="v">{{ \Carbon\Carbon::parse($app->citizen->dob)->format('d M Y') }}</span>
                    </div>
                    <div class="tap-card__row">
                        <span class="k">Jantina</span>
                        <span class="v">{{ $app->citizen->gender === 'M' ? 'Lelaki' : 'Perempuan' }}</span>
                    </div>
                @endif
                <div class="tap-card__row">
                    <span class="k">Alamat</span>
                    <span class="v">{{ $app->applicant_address }}</span>
                </div>
            </div>
        @endif

        {{-- Validasi Sistem Backend · LAMPIRAN A integration trail --}}
        @php
            $blockNo = '#' . (1925000 + ($app->id ?? 12));
            $txHash = '0x' . substr(md5($app->reference_number), 0, 10) . '...';
            $abisScore = number_format(99.50 + (($app->id ?? 1) % 50) / 100, 2);
            $dt = $app->doc_type ?? '';

            // Role access (officer/supervisor view this page; some modules are restricted)
            $vrole = auth()->user()->role ?? 'officer';
            $accessRoles = [
                'biometric' => ['officer','supervisor','admin'], 'abis' => ['officer','supervisor','admin'],
                'kaveat' => ['officer','supervisor','admin'], 'hospital' => ['officer','supervisor','admin'],
                'clms' => ['officer','supervisor','admin'], 'familytree' => ['officer','supervisor'],
                'borang' => ['officer','supervisor','admin'],
                'lapor' => ['officer','supervisor','admin'], 'kad' => ['officer','supervisor'],
                'upacara' => ['officer','supervisor'], 'sijil' => ['officer','supervisor'],
                'blockchain' => ['supervisor','admin'], 'agensi' => ['supervisor','admin'],
                'kafka' => ['admin'], 'mydigital' => ['admin'],
            ];
            $canTrail = fn ($k) => in_array($vrole, $accessRoles[$k] ?? ['officer'], true);
            $routeMapTrail = [
                'biometric'=>'system.biometric','abis'=>'system.abis','blockchain'=>'system.blockchain',
                'kafka'=>'system.kafka','hospital'=>'system.hospital','familytree'=>'system.familytree',
                'kaveat'=>'system.kaveat','upacara'=>'system.upacara','sijil'=>'system.sijil',
                'borang'=>'system.borang','lapor'=>'system.lapor','kad'=>'system.kad',
                'clms'=>'system.clms','mydigital'=>'system.mydigital','agensi'=>'system.agensi',
            ];

            // Cross-system trail for this case (no emoji — accent-coded), ordered per module flow
            $lockedBc = ['key'=>'blockchain','accent'=>'#7C3AED','title'=>'Hyperledger Fabric','sub'=>'Catatan automatik · proses tetap lalu','meta'=>'Modul terkunci — pegawai tidak melihat ledger','locked'=>true];
            $trail = [];
            if ($dt === 'birth') {
                $trail[] = ['key'=>'hospital','accent'=>'#DC2626','title'=>'Sumber Hospital KKM','sub'=>'Pra-daftar FHIR R4 · auto-pull','meta'=>'Data klinikal bayi'];
                $trail[] = ['key'=>'borang','accent'=>'#1E40AF','title'=>'Borang Pendaftaran · JPN.LM01','sub'=>'Diisi ibu bapa dalam talian','meta'=>'Data hospital pra-isi'];
                $trail[] = ['key'=>'biometric','accent'=>'#16A34A','title'=>'Pengesahan Ibu Bapa · Kaunter','sub'=>'10 cap jari + muka · ABIS 1:N','meta'=>'Sahkan identiti ibu bapa'];
                $trail[] = ['key'=>'familytree','accent'=>'#16A34A','title'=>'Salasilah Family Tree','sub'=>'Auto-link 2 ibu bapa + datuk nenek','meta'=>'Kemaskini Neo4j graph'];
                $trail[] = ['key'=>'sijil','accent'=>'#15803D','title'=>'Sijil Kelahiran · JPN.LM05 + MyKid','sub'=>'Selepas catatan blockchain','meta'=>'Cetus kelayakan MyKid · QR'];
                $trail[] = $lockedBc;
            } elseif ($dt === 'marriage') {
                $trail[] = $lockedBc;
                $trail[] = ['key'=>'kaveat','accent'=>'#D97706','title'=>'Kaveat 21 Hari · Akta 164 S.22','sub'=>'Tempoh pengiklanan','meta'=>'4 saluran rasmi · semakan bantahan'];
                $trail[] = ['key'=>'familytree','accent'=>'#16A34A','title'=>'Salasilah · Hubungan Darah','sub'=>'Tiada hubungan darah dikesan','meta'=>'Semakan 6 generasi · Neo4j'];
                $trail[] = ['key'=>'upacara','accent'=>'#BE185D','title'=>'Upacara & Pendaftaran · Akta 164 S.24','sub'=>'Pendaftar + 2 saksi · ikrar','meta'=>'No. Daftar perkahwinan sivil'];
                $trail[] = ['key'=>'sijil','accent'=>'#16A34A','title'=>'Sijil Perkahwinan · JPN.KC02','sub'=>'Selepas catatan blockchain','meta'=>'2 salinan · QR crypto signature'];
            } elseif ($dt === 'mykad') {
                $trail[] = ['key'=>'lapor','accent'=>'#DC2626','title'=>'Lapor Kehilangan + Laporan Polis','sub'=>'Kad lama dibatalkan · yuran RM110','meta'=>'Gantian Hilang · Akta 174'];
                $trail[] = ['key'=>'biometric','accent'=>'#16A34A','title'=>'Biometrik Capture','sub'=>'10 cap jari + muka + iris','meta'=>'Lulus quality check · NFIQ 96'];
                $trail[] = ['key'=>'abis','accent'=>'#16A34A','title'=>'ABIS 1:N Match','sub'=>'Skor '.$abisScore.'% · 3.2s · GPU H200','meta'=>'Sahkan pemilik sah · enrolmen sedia ada'];
                $trail[] = ['key'=>'clms','accent'=>'#D97706','title'=>'CLMS Card Pipeline','sub'=>'Personalisation → Key Injection PKI','meta'=>'Polikarbonat · ICAO Doc 9303'];
                $trail[] = ['key'=>'kad','accent'=>'#15803D','title'=>'Kad MyKad Dikeluarkan','sub'=>'Selepas catatan blockchain','meta'=>'Kad baru + MyDigital ID auto-provision'];
                $trail[] = $lockedBc;
                $trail[] = ['key'=>'mydigital','accent'=>'#4338CA','title'=>'MyDigital ID Provision','sub'=>'Auto-create akaun · SSO 74 agensi','meta'=>'Trigger selepas kad dikeluarkan'];
            } else {
                $trail[] = ['key'=>'biometric','accent'=>'#16A34A','title'=>'Biometrik Capture','sub'=>'10 cap jari + muka + iris','meta'=>'Lulus quality check · NFIQ 96'];
                $trail[] = ['key'=>'blockchain','accent'=>'#7C3AED','title'=>'Hyperledger Fabric','sub'=>'Blok '.$blockNo,'meta'=>$txHash.' · immutable'];
            }
            $trail[] = ['key'=>'kafka','accent'=>'#1E40AF','title'=>'Kafka Event Bus','sub'=>'event.'.($dt ?: 'doc').'.registered','meta'=>'Publish ke 7 modul pelanggan'];
            $trail[] = ['key'=>'agensi','accent'=>'#64748B','title'=>'Agensi Cross-Check','sub'=>'PDRM · LHDN · MAMPU','meta'=>'13 agensi · mTLS + OAuth 2.0'];
        @endphp
        <div class="tap-card">
            <div class="tap-card__eyebrow">Validasi Sistem · Backend LAMPIRAN A</div>
            <h3 class="tap-card__h">Jejak audit bersilang sistem</h3>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 12px;">
                @foreach($trail as $t)
                    @php $ok = empty($t['locked']) && $canTrail($t['key']); @endphp
                    @if($ok)
                        <a href="{{ route($routeMapTrail[$t['key']], ['ref' => $app->reference_number]) }}" style="display: flex; align-items: stretch; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; text-decoration: none; color: inherit; background: #fff;">
                    @else
                        <div style="display: flex; align-items: stretch; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; background: #F8FAFC; opacity: .65;" title="Modul terhad untuk peranan anda">
                    @endif
                        <div style="width: 4px; background: {{ $ok ? $t['accent'] : '#CBD5E1' }}; flex-shrink: 0;"></div>
                        <div style="flex: 1; min-width: 0; padding: 10px 14px;">
                            <div style="font-size: 11px; color: {{ $ok ? $t['accent'] : '#94A3B8' }}; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;">{{ $t['title'] }}</div>
                            <div style="font-size: 12px; font-weight: 600; color: {{ $ok ? 'var(--ink-navy)' : '#64748B' }}; margin-top: 1px;">{{ $t['sub'] }}</div>
                            <div style="font-size: 10.5px; color: #64748B; margin-top: 2px;">{{ $t['meta'] }}</div>
                        </div>
                        <div style="display: flex; align-items: center; padding-right: 12px; font-size: 11px; font-weight: 600; white-space: nowrap; color: {{ $ok ? $t['accent'] : '#94A3B8' }};">
                            @if($ok) Buka → @elseif(!empty($t['locked'])) Terkunci @else Akses terhad @endif
                        </div>
                    @if($ok)</a>@else</div>@endif
                @endforeach
            </div>

            <div style="margin-top: 12px; padding: 10px 12px; background: #F8FAFC; border-radius: 8px; font-size: 11.5px; color: #475569;">
                <strong>Pematuhan:</strong> Akta Keterangan 1950 S.90A · ISO/IEC 19794 + 30107 PAD · PDPA 2010 · LAMPIRAN A Para 2.1(viii) Blockchain
            </div>
        </div>

        {{-- Document card --}}
        <div class="tap-card">
            <div class="tap-card__eyebrow">Dokumen Dipohon</div>
            <h3 class="tap-card__h">{{ $docLabel }}</h3>
            <div class="tap-card__row">
                <span class="k">Jenis</span>
                <span class="v">{{ $docLabel }}</span>
            </div>
            <div class="tap-card__row">
                <span class="k">Sumber OCR</span>
                <span class="v" style="color: var(--success); font-weight: 600;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><path d="M20 6 9 17l-5-5"/></svg>
                    Padan dengan rekod warganegara
                </span>
            </div>
            <div class="tap-card__row">
                <span class="k">Skor Padanan AI</span>
                <span class="v mono">{{ number_format(($app->ai_score ?? 0) * 100, 1) }}%</span>
            </div>
        </div>

        {{-- Audit timeline --}}
        <div class="tap-card">
            <div class="tap-card__eyebrow">Log Aliran</div>
            <h3 class="tap-card__h">Sejarah permohonan</h3>
            <div class="audit-list">
                @foreach($app->auditLogs as $log)
                    <div class="audit-row">
                        <div class="audit-row__dot"></div>
                        <div class="audit-row__body">
                            <strong>{{ ucwords(str_replace('_', ' ', $log->action)) }}</strong>
                            @if($log->officer)
                                · oleh {{ $log->officer->name }}
                            @endif
                            @php $payload = is_array($log->payload) ? $log->payload : (json_decode($log->payload ?? '{}', true) ?: []); @endphp
                            @if(!empty($payload['from']) && !empty($payload['to']))
                                · <span style="color: var(--mute);">{{ Application::STAGE_LABELS[$payload['from']] ?? $payload['from'] }} → <strong style="color: var(--pine);">{{ Application::STAGE_LABELS[$payload['to']] ?? $payload['to'] }}</strong></span>
                            @endif
                            @if(!empty($payload['notes']))
                                <div style="font-size: 11.5px; color: var(--mute); margin-top: 2px;">{{ $payload['notes'] }}</div>
                            @endif
                        </div>
                        <div class="audit-row__when">{{ $log->created_at->format('d M H:i') }}</div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Right rail --}}
    <aside class="tap-body__rail">

        {{-- Decision panel --}}
        @if($canDecide)
            <div class="tap-action">
                <div class="tap-action__eyebrow">KEPUTUSAN</div>
                <h3 class="tap-action__h">Tindakan diperlukan.</h3>
                <p style="font-size: 12.5px; color: rgba(255,255,255,0.7); margin: 0 0 14px; line-height: 1.5;">
                    AI mencadangkan <strong style="color: #fff;">LULUS</strong> berdasarkan padanan {{ number_format(($app->ai_score ?? 0) * 100, 0) }}% dengan rekod warganegara.
                </p>
                <div class="tap-action__btns">
                    <form method="POST" action="{{ route('system.tapisan.approve', $app->reference_number) }}">
                        @csrf
                        <button type="submit" class="tap-action__btn tap-action__btn--approve" style="width: 100%; height: auto; min-height: 44px; padding: 11px 16px; background: #16A34A; color: #fff; font-size: 13.5px; font-weight: 700; white-space: nowrap; line-height: 1;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            Luluskan permohonan
                        </button>
                    </form>
                    <form method="POST" action="{{ route('system.tapisan.reject', $app->reference_number) }}">
                        @csrf
                        <button type="submit" class="tap-action__btn tap-action__btn--reject" style="width: 100%; height: auto; min-height: 44px; padding: 11px 16px; font-size: 13.5px; font-weight: 700; white-space: nowrap; line-height: 1; color: #fff; background: rgba(255,255,255,0.06); border: 1px solid rgba(239,68,68,0.45);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Tolak permohonan
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="rail-card" style="background: var(--paper);">
                <div class="rail-card__eyebrow">Status Akhir</div>
                <div style="font-size: 14px; font-weight: 600; color: var(--pine); margin-top: 6px;">{{ $statusLabel }}<span class="dot"></span></div>
                <div style="font-size: 12px; color: var(--mute); margin-top: 4px;">Permohonan ini sudah dilesepasi. Tiada tindakan diperlukan.</div>
            </div>
        @endif

        {{-- AI rationale --}}
        <div class="ai-card">
            <div class="ai-card__eyebrow"><span class="dot"></span> ANALISIS AI</div>
            <div class="ai-card__body">
                <strong>Skor padanan {{ number_format(($app->ai_score ?? 0) * 100, 1) }}%.</strong>
                Nama, IC, dan alamat pemohon padan dengan rekod sedia ada. Tiada anomali dikesan dalam metadata OCR. Cadangan: <strong>luluskan tanpa pengubahsuaian</strong>.
            </div>
            <div class="ai-card__meta">MODEL v0.4-jpn · DIKEMASKINI {{ $app->updated_at->format('H:i') }}</div>
        </div>

        {{-- Quick reference --}}
        <div class="rail-card">
            <div class="rail-card__eyebrow">Rujukan Pantas</div>
            <div style="font-size: 12px; color: var(--mute); margin-top: 8px; line-height: 1.7; font-family: var(--mono);">
                <div>REF · <strong style="color: var(--ink);">{{ $app->reference_number }}</strong></div>
                <div>IC · <strong style="color: var(--ink);">{{ $app->applicant_ic }}</strong></div>
                <div>JENIS · <strong style="color: var(--ink);">{{ strtoupper($app->doc_type) }}</strong></div>
                <div>SLA · <strong style="color: var(--ink);">{{ strtoupper($app->sla_state ?? 'on_track') }}</strong></div>
            </div>
            <div class="rail-card__foot">
                <a class="rail-card__link" href="{{ route('track.show', $app->reference_number) }}" target="_blank">Lihat dari Portal →</a>
            </div>
        </div>

    </aside>
</div>
@endsection
