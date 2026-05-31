@php
    use App\Models\User;
    use App\Models\Application;
    use App\Models\AuditLog;
@endphp

@extends('layouts.system', ['active' => 'utama', 'title' => 'Utama Pentadbir'])

@section('content')
@php
    $hour = (int) now()->format('H');
    $greet = $hour < 12 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat tengah hari' : ($hour < 19 ? 'Selamat petang' : 'Selamat malam'));

    // Real counts
    $totalUsers = User::count();
    $totalApps = Application::count();
    $totalAudit = AuditLog::count();

    // Dummy system health metrics
    $health = [
        'uptime' => 99.94,
        'db_size_gb' => 14.2,
        'db_quota_gb' => 50,
        'storage_pct' => 28,
        'req_per_hour' => 12420,
        'avg_response_ms' => 142,
        'error_rate' => 0.04,
    ];

    // Dummy online users (3 of N)
    $onlineCount = 3;

    // Integrasi status (downstream systems)
    $integrasi = [
        ['name' => 'MyKad Master',         'code' => 'MYKAD-API',  'status' => 'ok',     'latency' => 89,  'desc' => 'Sistem MyKad pusat'],
        ['name' => 'Bank Negara Sanctions','code' => 'BNM-OFAC',   'status' => 'ok',     'latency' => 124, 'desc' => 'Senarai sekatan kewangan'],
        ['name' => 'PDRM Background Check','code' => 'PDRM-BG',    'status' => 'warn',   'latency' => 480, 'desc' => 'Semakan latar belakang polis'],
        ['name' => 'Suruhanjaya Pilihan Raya', 'code' => 'SPR-VOTE', 'status' => 'ok',   'latency' => 67,  'desc' => 'Status pengundi'],
        ['name' => 'Imigresen Gateway',    'code' => 'IMI-GW',     'status' => 'ok',     'latency' => 156, 'desc' => 'Pintu masuk imigresen'],
        ['name' => 'JPJ Driver Records',   'code' => 'JPJ-LIC',    'status' => 'down',   'latency' => 0,   'desc' => 'Rekod pemandu (penyelenggaraan)'],
    ];

    // Privileged audit events (dummy + real where possible)
    $privilegedEvents = [
        ['ts' => now()->subMinutes(8),  'actor' => 'Ibrahim Pentadbir', 'action' => 'role_assigned',     'target' => 'Hafiz bin Razak → Supervisor', 'sev' => 'high'],
        ['ts' => now()->subHour(),      'actor' => 'Sistem',             'action' => 'backup_completed',  'target' => 'snapshot_28GB.tar.gz',         'sev' => 'low'],
        ['ts' => now()->subHours(2),    'actor' => 'Ibrahim Pentadbir', 'action' => 'api_key_rotated',   'target' => 'PDRM-BG integration key',      'sev' => 'med'],
        ['ts' => now()->subHours(4),    'actor' => 'Sistem',             'action' => 'cert_renewed',      'target' => 'TLS *.jpn.gov.my',             'sev' => 'low'],
        ['ts' => now()->subHours(6),    'actor' => 'Ibrahim Pentadbir', 'action' => 'user_created',      'target' => 'aisyah@jpn.gov.my',            'sev' => 'med'],
        ['ts' => now()->subDay(),       'actor' => 'Sistem',             'action' => 'firewall_block',    'target' => '203.124.x.x (10 req/s)',       'sev' => 'high'],
    ];

    // Last backup
    $lastBackup = ['ago_min' => 10, 'size_gb' => 28.4, 'next_min' => 50];
@endphp

<div class="ws-page">
    <div class="ws-page__main">

        {{-- ==== Greeting (admin variant) ==== --}}
        <div class="dash-greet">
            <div>
                <h1 class="dash-greet__h1">{{ $greet }}, {{ explode(' ', auth()->user()->name)[0] }}.<span class="dot"></span></h1>
                <p class="dash-greet__sub">
                    Operasi sistem <strong style="color: var(--success);">NORMAL</strong> ·
                    <strong>{{ number_format($totalUsers) }} pengguna berdaftar</strong> ·
                    <strong>{{ $onlineCount }} dalam talian sekarang</strong> ·
                    {{ number_format($health['req_per_hour']) }} req / jam
                </p>
            </div>
            <div class="dash-contract" style="border-color: var(--success); background: linear-gradient(0deg, rgba(16,185,129,0.04), rgba(16,185,129,0.04)), #fff;">
                <div class="dash-contract__eyebrow" style="color: var(--success);">Status Pusat</div>
                <div class="dash-contract__big" style="color: var(--success);">NORMAL</div>
                <div class="dash-contract__sub mono">{{ $health['uptime'] }}% · 30 hari</div>
            </div>
        </div>

        {{-- ==== System health KPIs ==== --}}
        <div class="dash-sec">
            <div class="dash-sec__head">
                <div class="dash-sec__eyebrow">Kesihatan Sistem</div>
                <span class="dash-sec__eyebrow mono" style="color: var(--mute-2);">DIKEMASKINI · {{ now()->format('H:i:s') }}</span>
            </div>

            <div class="dash-kpis">
                <div class="dash-kpi is-ok">
                    <div class="dash-kpi__eyebrow">Masa Operasi</div>
                    <div class="dash-kpi__value">{{ $health['uptime'] }}%</div>
                    <div class="dash-kpi__sub">SLA target: 99.9%</div>
                </div>
                <div class="dash-kpi">
                    <div class="dash-kpi__eyebrow">Pengguna Berdaftar</div>
                    <div class="dash-kpi__value">{{ number_format($totalUsers) }}</div>
                    <div class="dash-kpi__sub">{{ $onlineCount }} aktif sekarang</div>
                </div>
                <div class="dash-kpi">
                    <div class="dash-kpi__eyebrow">Storan Pangkalan</div>
                    <div class="dash-kpi__value">{{ $health['db_size_gb'] }} <small style="font-size: 12px; color: var(--mute); font-weight: 500;">/ {{ $health['db_quota_gb'] }} GB</small></div>
                    <div class="dash-kpi__sub">{{ $health['storage_pct'] }}% digunakan</div>
                </div>
                <div class="dash-kpi {{ $health['error_rate'] > 0.5 ? 'is-warn' : 'is-ok' }}">
                    <div class="dash-kpi__eyebrow">Kadar Ralat</div>
                    <div class="dash-kpi__value">{{ $health['error_rate'] }}%</div>
                    <div class="dash-kpi__sub">Tindak balas {{ $health['avg_response_ms'] }}ms</div>
                </div>
                <div class="dash-kpi">
                    <div class="dash-kpi__eyebrow">Trafik 1 Jam</div>
                    <div class="dash-kpi__value">{{ number_format($health['req_per_hour'] / 1000, 1) }}K</div>
                    <div class="dash-kpi__sub">req · normal</div>
                </div>
            </div>
        </div>

        {{-- ==== Integrasi sistem hiliran ==== --}}
        <div class="dash-sec">
            <div class="dash-sec__head">
                <div class="dash-sec__eyebrow">Integrasi Hiliran</div>
                <button type="button" class="dash-sec__cta js-stub" data-module="Pengurusan Integrasi" style="border:0;background:transparent;cursor:pointer;font-family:inherit;">Urus integrasi →</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                @foreach($integrasi as $i)
                    @php
                        $statusColor = ['ok' => 'var(--success)', 'warn' => 'var(--warning)', 'down' => 'var(--danger)'][$i['status']];
                        $statusBg = ['ok' => 'rgba(16,185,129,0.10)', 'warn' => 'rgba(245,158,11,0.10)', 'down' => 'rgba(239,68,68,0.10)'][$i['status']];
                        $statusLabel = ['ok' => 'OK', 'warn' => 'PERLAHAN', 'down' => 'TUMPAS'][$i['status']];
                    @endphp
                    <div style="background:#fff; border: 1px solid var(--line); border-radius: var(--r-md); padding: 12px 14px; display: grid; grid-template-columns: 36px 1fr auto; gap: 12px; align-items: center;">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: {{ $statusBg }}; color: {{ $statusColor }}; display: grid; place-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                @if($i['status'] === 'ok')
                                    <path d="M20 6 9 17l-5-5"/>
                                @elseif($i['status'] === 'warn')
                                    <path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                @else
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                @endif
                            </svg>
                        </div>
                        <div style="min-width: 0;">
                            <div style="font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 2px;">{{ $i['name'] }}</div>
                            <div class="mono" style="font-size: 10.5px; color: var(--mute);">{{ $i['code'] }} · {{ $i['desc'] }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: {{ $statusColor }};">{{ $statusLabel }}</div>
                            <div class="mono" style="font-size: 10px; color: var(--mute); margin-top: 2px;">{{ $i['latency'] }}ms</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ==== Privileged events ==== --}}
        <div class="dash-sec">
            <div class="dash-sec__head">
                <div class="dash-sec__eyebrow">Peristiwa Sistem Berisiko Tinggi</div>
                <a class="dash-sec__cta" href="{{ route('system.audit') }}">Audit penuh →</a>
            </div>

            <div style="background: #fff; border: 1px solid var(--line); border-radius: var(--r-lg); overflow: hidden;">
                @foreach($privilegedEvents as $ev)
                    @php
                        $sevColor = ['high' => 'var(--orange)', 'med' => 'var(--warning)', 'low' => 'var(--mute)'][$ev['sev']];
                        $sevBg = ['high' => 'var(--orange-soft)', 'med' => 'rgba(245,158,11,0.10)', 'low' => 'var(--paper-2)'][$ev['sev']];
                    @endphp
                    <div style="display: grid; grid-template-columns: 4px 140px 1fr 140px; gap: 12px; align-items: center; padding: 11px 18px 11px 0; border-bottom: 1px solid var(--line);">
                        <div style="width: 4px; height: 24px; background: {{ $sevColor }}; border-radius: 0 2px 2px 0;"></div>
                        <div class="mono" style="font-size: 11px; color: var(--mute);">{{ $ev['ts']->format('Y-m-d H:i') }}</div>
                        <div>
                            <div style="font-size: 13px; color: var(--ink);">
                                <strong style="font-weight: 600; color: var(--pine);">{{ ucwords(str_replace('_', ' ', $ev['action'])) }}</strong>
                                · <span style="color: var(--mute);">oleh</span> {{ $ev['actor'] }}
                            </div>
                            <div style="font-size: 11.5px; color: var(--mute); margin-top: 2px; font-family: var(--mono);">{{ $ev['target'] }}</div>
                        </div>
                        <div style="text-align: right;">
                            <span style="display: inline-block; padding: 3px 9px; border-radius: 4px; background: {{ $sevBg }}; color: {{ $sevColor }}; font-size: 10px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;">
                                {{ strtoupper($ev['sev']) }} RISK
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ==== Modul Sistem LAMPIRAN A · backbone monitor ==== --}}
        <div class="dash-sec">
            <div class="dash-sec__head">
                <div class="dash-sec__eyebrow">Modul Sistem · LAMPIRAN A</div>
                <a class="dash-sec__cta" href="{{ route('system.katalog') }}">Lihat katalog 63 fungsi →</a>
            </div>

            @php
            // Admin sees ALL 12 modules
            $sysModules = [
                ['route' => 'system.biometric', 'icon' => 'fingerprint', 'name' => 'Penangkapan Biometrik', 'stat' => '241', 'sub' => 'kaunter aktif · ISO 19794', 'tone' => '#15803D'],
                ['route' => 'system.abis',       'icon' => 'eye', 'name' => 'ABIS 1:N Biometrik', 'stat' => '4,821', 'sub' => 'padanan hari ini · GPU H200', 'tone' => '#1E40AF'],
                ['route' => 'system.kaveat',    'icon' => 'heart', 'name' => 'Kaveat 21 Hari', 'stat' => '6', 'sub' => 'aktif · Akta 164 S.22', 'tone' => '#B45309'],
                ['route' => 'system.hospital',  'icon' => 'hospital', 'name' => 'Hospital KKM Pra-Daftar', 'stat' => '234', 'sub' => 'hari ini · FHIR R4', 'tone' => '#DC2626'],
                ['route' => 'system.clms',      'icon' => 'id-card', 'name' => 'CLMS Kitar Hayat Kad', 'stat' => '421', 'sub' => 'baris gilir cetak · ICAO 9303', 'tone' => '#D97706'],
                ['route' => 'system.katalog',   'icon' => 'list', 'name' => 'Katalog Sub-Fungsi', 'stat' => '31 / 63', 'sub' => 'AKTIF · 3 modul', 'tone' => '#475569'],
                ['route' => 'system.blockchain','icon' => 'link', 'name' => 'Hyperledger Fabric', 'stat' => '1.93M', 'sub' => 'blok kekal · Akta S.90A', 'tone' => '#7C3AED'],
                ['route' => 'system.agensi',    'icon' => 'refresh', 'name' => 'Integrasi Agensi', 'stat' => '17 / 17', 'sub' => 'live · + 74 via MyGDX', 'tone' => '#0EA5E9'],
                ['route' => 'system.perkakasan','icon' => 'printer', 'name' => 'Perkakasan Kaunter', 'stat' => '99.2%', 'sub' => 'uptime · 9 jenis APPENDIX C', 'tone' => '#0891B2'],
                ['route' => 'system.kafka',     'icon' => 'event', 'name' => 'Kafka Event Bus', 'stat' => '4.8k/s', 'sub' => '8 topic · 3 broker', 'tone' => '#6366F1'],
                ['route' => 'system.mydigital', 'icon' => 'key', 'name' => 'MyDigital ID', 'stat' => '21.8M', 'sub' => 'akaun aktif · MAMPU', 'tone' => '#4338CA'],
            ];
            @endphp

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
                @foreach($sysModules as $m)
                    <a href="{{ route($m['route']) }}" style="display: flex; gap: 12px; padding: 14px; background: #fff; border: 1px solid var(--line, #E5E7EB); border-left: 3px solid {{ $m['tone'] }}; border-radius: 10px; text-decoration: none; color: inherit;">
                        <div style="width: 42px; height: 42px; background: {{ $m['tone'] }}15; border-radius: 8px; display: flex; align-items: center; justify-content: center;">@include('system._icon', ['name' => $m['icon'], 'color' => $m['tone'], 'size' => 20])</div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">{{ $m['name'] }}</div>
                            <div style="font-size: 20px; font-weight: 700; color: {{ $m['tone'] }}; font-family: ui-monospace, monospace; margin-top: 2px;">{{ $m['stat'] }}</div>
                            <div style="font-size: 11px; color: #6B7280; margin-top: 2px;">{{ $m['sub'] }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ==== Right rail (admin) ==== --}}
    <aside class="ws-page__rail">

        <div class="ai-card">
            <div class="ai-card__eyebrow"><span class="dot"></span> AMARAN SISTEM</div>
            <div class="ai-card__body">
                <strong>PDRM-BG sentiasa perlahan</strong> minggu ini (480ms purata). Pertimbang naik taraf rate-limit pakej atau alih ke cache. JPJ-LIC dalam <strong>penyelenggaraan terancang</strong> sehingga 18:00.
            </div>
            <div class="ai-card__meta">PEMANTAU SISTEM · DIKEMASKINI {{ now()->format('H:i') }}</div>
        </div>

        <div class="rail-card">
            <div class="rail-card__head">
                <div class="rail-card__eyebrow">Pengguna Dalam Talian</div>
                <span class="mono" style="font-size: 11px; color: var(--teal-deep); font-weight: 700;">{{ $onlineCount }}</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 6px;">
                @php
                    $onlineList = [
                        ['name' => 'Demo Officer', 'role' => 'Officer', 'idle' => '2m'],
                        ['name' => 'Nurul Iman', 'role' => 'Supervisor', 'idle' => 'aktif'],
                        ['name' => 'Aisyah binti Yusof', 'role' => 'Officer', 'idle' => '8m'],
                    ];
                @endphp
                @foreach($onlineList as $u)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px dashed var(--line); font-size: 12.5px;">
                        <div>
                            <div style="color: var(--ink); font-weight: 600;">
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--success); margin-right: 6px; vertical-align: 2px;"></span>
                                {{ $u['name'] }}
                            </div>
                            <div style="font-size: 10.5px; color: var(--mute); letter-spacing: 0.06em; text-transform: uppercase; font-weight: 600; margin-top: 2px; margin-left: 12px;">{{ $u['role'] }}</div>
                        </div>
                        <span class="mono" style="font-size: 10.5px; color: var(--mute);">{{ $u['idle'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="rail-card__foot">
                <button type="button" class="rail-card__link js-stub" data-module="Sesi Aktif" style="border:0;background:transparent;cursor:pointer;font-family:inherit;color:var(--pine);font-weight:500;font-size:11.5px;">Urus sesi →</button>
            </div>
        </div>

        <div class="rail-card">
            <div class="rail-card__head">
                <div class="rail-card__eyebrow">Sandaran Sistem</div>
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></span>
            </div>
            <div style="font-size: 13px; color: var(--ink); font-weight: 600; margin-bottom: 4px;">{{ $lastBackup['ago_min'] }} minit yang lepas</div>
            <div style="font-size: 11.5px; color: var(--mute); margin-bottom: 10px;">Saiz: <span class="mono" style="color: var(--ink); font-weight: 600;">{{ $lastBackup['size_gb'] }} GB</span> · Auto</div>
            <div style="height: 4px; background: var(--paper-2); border-radius: 2px; overflow: hidden; margin-bottom: 6px;">
                <div style="height: 100%; width: 35%; background: var(--success); border-radius: 2px;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 10.5px; color: var(--mute);">
                <span>Lepas</span>
                <span class="mono">+{{ $lastBackup['next_min'] }} min · seterusnya</span>
            </div>
            <div class="rail-card__foot">
                <button type="button" class="rail-card__link js-stub" data-module="Sandaran Manual" style="border:0;background:transparent;cursor:pointer;font-family:inherit;color:var(--pine);font-weight:500;font-size:11.5px;">Mulakan sandaran manual →</button>
            </div>
        </div>

        <div class="rail-card">
            <div class="rail-card__head">
                <div class="rail-card__eyebrow">Pengurusan Pantas</div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <button type="button" class="rail-card__link js-stub" data-module="Pengurusan Pengguna" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: var(--paper); border-radius: 6px; border: 0; cursor: pointer; font-family: inherit; width: 100%;">
                    <span style="color: var(--ink); font-weight: 600; font-size: 12.5px;">Pengguna & Peranan</span>
                    <span class="mono" style="font-size: 11px; color: var(--teal-deep); font-weight: 700;">→</span>
                </button>
                <button type="button" class="rail-card__link js-stub" data-module="Tetapan Sistem" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: var(--paper); border-radius: 6px; border: 0; cursor: pointer; font-family: inherit; width: 100%;">
                    <span style="color: var(--ink); font-weight: 600; font-size: 12.5px;">Tetapan Sistem</span>
                    <span class="mono" style="font-size: 11px; color: var(--teal-deep); font-weight: 700;">→</span>
                </button>
                <button type="button" class="rail-card__link js-stub" data-module="Integrasi API" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: var(--paper); border-radius: 6px; border: 0; cursor: pointer; font-family: inherit; width: 100%;">
                    <span style="color: var(--ink); font-weight: 600; font-size: 12.5px;">Integrasi API</span>
                    <span class="mono" style="font-size: 11px; color: var(--teal-deep); font-weight: 700;">→</span>
                </button>
            </div>
        </div>

    </aside>
</div>
@endsection
