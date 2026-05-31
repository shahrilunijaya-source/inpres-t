@php
    use App\Models\Application;
    use App\Models\User;
@endphp

@extends('layouts.system', ['active' => 'utama', 'title' => 'Utama Penyelia'])

@section('content')
@php
    $hour = (int) now()->format('H');
    $greet = $hour < 12 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat tengah hari' : ($hour < 19 ? 'Selamat petang' : 'Selamat malam'));

    // Team = all officers (role='officer'); supervisor manages them
    $team = User::where('role', 'officer')->get();

    // Team workload (assigned + active apps per officer)
    $workload = $team->map(function ($officer) {
        $assigned = Application::where('assigned_officer_id', $officer->id)
            ->whereIn('status', ['received', 'verified', 'officer_review', 'approved'])
            ->count();
        $approved30 = \App\Models\AuditLog::where('officer_id', $officer->id)
            ->where('action', 'approve_application')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        return [
            'officer' => $officer,
            'assigned' => $assigned,
            'approved30' => $approved30,
            'load_pct' => min(100, $assigned * 8), // visual scale
        ];
    })->sortByDesc('assigned')->values();

    $totalAssigned = $workload->sum('assigned');
    $avgLoad = $team->count() > 0 ? round($totalAssigned / $team->count(), 1) : 0;

    // Escalations: late > 14 days, still pending
    $eskalasi = Application::with('citizen')
        ->where('sla_state', 'breached')
        ->whereIn('status', ['received', 'verified', 'officer_review'])
        ->where('created_at', '<=', now()->subDays(14))
        ->orderBy('created_at')
        ->take(5)
        ->get();

    // Pending assignment (unassigned, active)
    $unassigned = Application::whereNull('assigned_officer_id')
        ->whereIn('status', ['received', 'verified', 'officer_review'])
        ->count();

    $teamThroughputWeek = \App\Models\AuditLog::whereIn('officer_id', $team->pluck('id'))
        ->where('action', 'approve_application')
        ->where('created_at', '>=', now()->subWeek())
        ->count();
@endphp

<div class="ws-page">
    <div class="ws-page__main">

        {{-- ==== Greeting (supervisor variant) ==== --}}
        <div class="dash-greet">
            <div>
                <h1 class="dash-greet__h1">{{ $greet }}, {{ explode(' ', auth()->user()->name)[0] }}.<span class="dot"></span></h1>
                <p class="dash-greet__sub">
                    <strong>{{ $team->count() }} pegawai</strong> bawah seliaan anda ·
                    <strong>{{ $totalAssigned }} permohonan</strong> dalam saluran ·
                    @if($eskalasi->count() > 0)
                        <span class="late">{{ $eskalasi->count() }} eskalasi memerlukan campur tangan</span>
                    @else
                        tiada eskalasi
                    @endif
                </p>
            </div>
            <div class="dash-contract">
                <div class="dash-contract__eyebrow">Pasukan Putrajaya</div>
                <div class="dash-contract__big">{{ $team->count() }} <small style="font-size: 11px; color: var(--mute); font-weight: 500;">pegawai</small></div>
                <div class="dash-contract__sub">Purata beban · {{ $avgLoad }} kes / pegawai</div>
            </div>
        </div>

        {{-- ==== KPIs (supervisor variant) ==== --}}
        <div class="dash-sec">
            <div class="dash-sec__head">
                <div class="dash-sec__eyebrow">Status Pasukan</div>
                <a class="dash-sec__cta" href="{{ route('system.statistik') }}">Lihat statistik penuh →</a>
            </div>
            <div class="dash-kpis">
                <div class="dash-kpi">
                    <div class="dash-kpi__eyebrow">Pegawai Aktif</div>
                    <div class="dash-kpi__value">{{ $team->count() }} / {{ $team->count() }}</div>
                    <div class="dash-kpi__sub">Semua dalam talian</div>
                </div>
                <div class="dash-kpi {{ $unassigned > 5 ? 'is-warn' : '' }}">
                    <div class="dash-kpi__eyebrow">Belum Diagihkan</div>
                    <div class="dash-kpi__value">{{ $unassigned }}</div>
                    <div class="dash-kpi__sub">Perlu agih segera</div>
                </div>
                <div class="dash-kpi is-ok">
                    <div class="dash-kpi__eyebrow">Diluluskan Minggu</div>
                    <div class="dash-kpi__value">{{ $teamThroughputWeek }}</div>
                    <div class="dash-kpi__sub">Pasukan keseluruhan</div>
                </div>
                <div class="dash-kpi {{ $eskalasi->count() > 0 ? 'is-warn' : '' }}">
                    <div class="dash-kpi__eyebrow">Eskalasi</div>
                    <div class="dash-kpi__value">{{ $eskalasi->count() }}</div>
                    <div class="dash-kpi__sub">> 14 hari</div>
                </div>
            </div>
        </div>

        {{-- ==== Team workload ==== --}}
        <div class="dash-sec">
            <div class="dash-sec__head">
                <div class="dash-sec__eyebrow">Beban Kerja Pasukan</div>
                <a class="dash-sec__cta js-stub" data-module="Agih Semula Pukal" href="#">Agih semula →</a>
            </div>

            <div class="dash-tugasan">
                @foreach($workload as $row)
                    @php
                        $initials = '';
                        foreach (explode(' ', $row['officer']->name) as $part) {
                            if ($part && strlen($initials) < 2) $initials .= strtoupper(mb_substr($part, 0, 1));
                        }
                        $isHeavy = $row['load_pct'] > 70;
                    @endphp
                    <div class="dash-trow" style="grid-template-columns: 44px 1fr 220px 100px;">
                        <div class="dash-trow__panel" style="width: 36px; height: 36px; border-radius: 50%; background: {{ $isHeavy ? 'var(--orange-soft)' : 'var(--teal-soft)' }}; color: {{ $isHeavy ? 'var(--orange)' : 'var(--teal-deep)' }}; font-size: 12px;">{{ $initials }}</div>
                        <div class="dash-trow__title-row">
                            <div class="dash-trow__title">{{ $row['officer']->name }}</div>
                            <div class="dash-trow__sub">
                                <span class="mono" style="text-transform: uppercase; letter-spacing: 0.06em; font-size: 10px;">{{ $row['officer']->role }}</span>
                                <span class="sep">·</span>
                                <span>{{ $row['approved30'] }} diluluskan (30h)</span>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--mute); display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span>Beban semasa</span>
                                <span class="mono" style="font-weight: 700; color: {{ $isHeavy ? 'var(--orange)' : 'var(--pine)' }};">{{ $row['assigned'] }} kes</span>
                            </div>
                            <div style="height: 6px; background: var(--paper-2); border-radius: 3px; overflow: hidden;">
                                <div style="height: 100%; width: {{ $row['load_pct'] }}%; background: {{ $isHeavy ? 'linear-gradient(90deg, #e85a26 0%, var(--orange) 100%)' : 'linear-gradient(90deg, var(--teal-deep) 0%, var(--teal) 100%)' }}; border-radius: 3px;"></div>
                            </div>
                        </div>
                        <button type="button" class="dash-trow__cta dash-trow__cta--ghost js-stub" data-module="Profil Pegawai · {{ $row['officer']->name }}">
                            Lihat
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ==== Eskalasi ==== --}}
        @if($eskalasi->count() > 0)
            <div class="dash-sec">
                <div class="dash-sec__head">
                    <div class="dash-sec__eyebrow" style="color: var(--orange);">Eskalasi · Memerlukan Campur Tangan</div>
                    <a class="dash-sec__cta" href="{{ route('system.tapisan', ['status' => 'late']) }}">Semua eskalasi →</a>
                </div>

                <div class="dash-tugasan">
                    @foreach($eskalasi as $app)
                        @php
                            $docCode = ['birth' => 'KEL', 'marriage' => 'KAH', 'mykad' => 'KAD'][$app->doc_type] ?? '?';
                        @endphp
                        <a href="{{ route('system.tapisan.show', $app->reference_number) }}" class="dash-trow is-late">
                            <div class="dash-trow__panel">{{ $docCode }}</div>
                            <div class="dash-trow__title-row">
                                <div class="dash-trow__title">{{ $app->applicant_name }}</div>
                                <div class="dash-trow__sub">
                                    <span class="mono">{{ $app->reference_number }}</span>
                                    <span class="sep">·</span>
                                    <span>{{ Application::STAGE_LABELS[$app->status] ?? $app->status }}</span>
                                </div>
                            </div>
                            <div class="dash-trow__due">
                                <div class="dash-trow__due-label">Lewat {{ $app->created_at->diffForHumans(null, true) }}</div>
                                <div class="dash-trow__due-sub">Belum diagihkan / tertunda</div>
                            </div>
                            <span class="dash-trow__cta dash-trow__cta--orange">
                                Rujuk Atasan
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ==== Modul Sistem LAMPIRAN A · backbone monitor ==== --}}
        <div class="dash-sec">
            <div class="dash-sec__head">
                <div class="dash-sec__eyebrow">Modul Sistem · LAMPIRAN A</div>
                <a class="dash-sec__cta" href="{{ route('system.katalog') }}">Lihat katalog 63 fungsi →</a>
            </div>

            @php
            // Supervisor: no Kafka, no MyDigital (admin-only)
            $sysModules = [
                ['route' => 'system.biometric', 'icon' => 'fingerprint', 'name' => 'Penangkapan Biometrik', 'stat' => '241', 'sub' => 'kaunter aktif · ISO 19794', 'tone' => '#15803D'],
                ['route' => 'system.abis',       'icon' => 'eye', 'name' => 'ABIS 1:N Biometrik', 'stat' => '4,821', 'sub' => 'padanan hari ini · GPU H200', 'tone' => '#1E40AF'],
                ['route' => 'system.kaveat',    'icon' => 'heart', 'name' => 'Kaveat 21 Hari', 'stat' => '6', 'sub' => 'aktif · Akta 164 S.22', 'tone' => '#B45309'],
                ['route' => 'system.hospital',  'icon' => 'hospital', 'name' => 'Hospital KKM Pra-Daftar', 'stat' => '234', 'sub' => 'hari ini · FHIR R4', 'tone' => '#DC2626'],
                ['route' => 'system.familytree','icon' => 'tree', 'name' => 'Salasilah Family Tree', 'stat' => '32.8M', 'sub' => 'nod · Neo4j cluster', 'tone' => '#16A34A'],
                ['route' => 'system.clms',      'icon' => 'id-card', 'name' => 'CLMS Kitar Hayat Kad', 'stat' => '421', 'sub' => 'baris gilir cetak · ICAO 9303', 'tone' => '#D97706'],
                ['route' => 'system.katalog',   'icon' => 'list', 'name' => 'Katalog Sub-Fungsi', 'stat' => '31 / 63', 'sub' => 'AKTIF · 3 modul', 'tone' => '#475569'],
                ['route' => 'system.blockchain','icon' => 'link', 'name' => 'Hyperledger Fabric', 'stat' => '1.93M', 'sub' => 'blok kekal · Akta S.90A', 'tone' => '#7C3AED'],
                ['route' => 'system.agensi',    'icon' => 'refresh', 'name' => 'Integrasi Agensi', 'stat' => '17 / 17', 'sub' => 'live · + 74 via MyGDX', 'tone' => '#0EA5E9'],
                ['route' => 'system.perkakasan','icon' => 'printer', 'name' => 'Perkakasan Kaunter', 'stat' => '99.2%', 'sub' => 'uptime · 9 jenis APPENDIX C', 'tone' => '#0891B2'],
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

    {{-- ==== Right rail (supervisor) ==== --}}
    <aside class="ws-page__rail">

        <div class="ai-card">
            <div class="ai-card__eyebrow"><span class="dot"></span> CADANGAN PENYELIAAN</div>
            <div class="ai-card__body">
                @if($unassigned > 5)
                    <strong>{{ $unassigned }} permohonan belum diagihkan</strong>. Agih ikut beban semasa — Aisyah & Hafiz mempunyai kapasiti tertinggi minggu ini.
                @elseif($eskalasi->count() > 0)
                    <strong>{{ $eskalasi->count() }} kes melepasi 14 hari</strong>. Pertimbang ambil alih atau eskalasi ke Pentadbir.
                @else
                    Pasukan beroperasi dalam SLA. <strong>Auto-lulus AI</strong> dicadangkan untuk kes berskor &gt;90%.
                @endif
            </div>
            <div class="ai-card__meta">MODEL PENYELIAAN v0.4 · DIKEMASKINI {{ now()->format('H:i') }}</div>
        </div>

        <div class="rail-card">
            <div class="rail-card__head">
                <div class="rail-card__eyebrow">Tindakan Pukal</div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <a href="{{ route('system.tapisan') }}" class="rail-card__link" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; background: var(--paper); border-radius: 6px;">
                    <span style="color: var(--ink); font-weight: 600; font-size: 12.5px;">Lulus pukal AI &gt;85%</span>
                    <span class="mono" style="font-size: 11px; color: var(--teal-deep); font-weight: 700;">→</span>
                </a>
                <button type="button" class="rail-card__link js-stub" data-module="Agihan Auto" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; background: var(--paper); border-radius: 6px; border: 0; cursor: pointer; font-family: inherit; width: 100%;">
                    <span style="color: var(--ink); font-weight: 600; font-size: 12.5px;">Auto-agih backlog</span>
                    <span class="mono" style="font-size: 11px; color: var(--teal-deep); font-weight: 700;">→</span>
                </button>
                <button type="button" class="rail-card__link js-stub" data-module="Notifikasi Pasukan" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; background: var(--paper); border-radius: 6px; border: 0; cursor: pointer; font-family: inherit; width: 100%;">
                    <span style="color: var(--ink); font-weight: 600; font-size: 12.5px;">Notifikasi pasukan</span>
                    <span class="mono" style="font-size: 11px; color: var(--teal-deep); font-weight: 700;">→</span>
                </button>
            </div>
        </div>

        <div class="rail-card">
            <div class="rail-card__head">
                <div class="rail-card__eyebrow">Prestasi Pasukan · Minggu Ini</div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                    <span style="color: var(--mute);">Throughput</span>
                    <span class="mono" style="font-weight: 700; color: var(--pine);">{{ $teamThroughputWeek }} kes</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                    <span style="color: var(--mute);">Purata SLA</span>
                    <span class="mono" style="font-weight: 700; color: var(--success);">2.1 jam</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                    <span style="color: var(--mute);">Konsistensi</span>
                    <span class="mono" style="font-weight: 700; color: var(--teal-deep);">94.2%</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                    <span style="color: var(--mute);">Eskalasi minggu</span>
                    <span class="mono" style="font-weight: 700; color: {{ $eskalasi->count() > 0 ? 'var(--orange)' : 'var(--mute)' }};">{{ $eskalasi->count() }}</span>
                </div>
            </div>
            <div class="rail-card__foot">
                <a class="rail-card__link" href="{{ route('system.audit') }}">Audit pasukan →</a>
            </div>
        </div>

    </aside>
</div>
@endsection
