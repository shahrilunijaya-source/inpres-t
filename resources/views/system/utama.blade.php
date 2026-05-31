@php
    use App\Models\Application;
@endphp

@extends('layouts.system', ['active' => 'utama', 'title' => 'Utama'])

@section('content')
@php
    $hour = (int) now()->format('H');
    $greet = $hour < 12 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat tengah hari' : ($hour < 19 ? 'Selamat petang' : 'Selamat malam'));

    $counts = [
        'received'        => Application::where('status', 'received')->count(),
        'verified'        => Application::where('status', 'verified')->count(),
        'officer_review'  => Application::where('status', 'officer_review')->count(),
        'approved'        => Application::where('status', 'approved')->count(),
        'issued'          => Application::where('status', 'issued')->count(),
    ];

    $todayApps = Application::with('citizen')
        ->whereIn('status', ['received', 'verified', 'officer_review'])
        ->orderBy('created_at')
        ->take(6)
        ->get();

    $pendingTotal = $counts['received'] + $counts['verified'] + $counts['officer_review'];
    $lateCount = Application::where('sla_state', 'breached')
        ->whereIn('status', ['received', 'verified', 'officer_review'])
        ->count();
@endphp

<div class="ws-page">
    <div class="ws-page__main">

        {{-- ==== Greeting ==== --}}
        <div class="dash-greet">
            <div>
                <h1 class="dash-greet__h1">{{ $greet }}, {{ explode(' ', auth()->user()->name)[0] }}.<span class="dot"></span></h1>
                <p class="dash-greet__sub">
                    Anda mempunyai <strong>{{ $pendingTotal }} permohonan</strong> menunggu semakan
                    @if($lateCount > 0)
                        , <span class="late">{{ $lateCount }} sudah lewat SLA</span>
                    @else
                        . Semua dalam SLA.
                    @endif
                </p>
            </div>
            <div class="dash-contract {{ $lateCount > 0 ? 'is-orange' : '' }}">
                <div class="dash-contract__eyebrow">Hari Ini</div>
                <div class="dash-contract__big mono">{{ now()->format('d M Y') }}</div>
                <div class="dash-contract__sub">{{ now()->format('l · H:i') }} · {{ now()->timezoneName }}</div>
            </div>
        </div>

        {{-- ==== KPIs ==== --}}
        <div class="dash-sec">
            <div class="dash-sec__head">
                <div class="dash-sec__eyebrow">Saluran Tugasan</div>
                <a class="dash-sec__cta" href="{{ route('system.tapisan') }}">Lihat senarai penuh →</a>
            </div>

            <div class="dash-kpis">
                <div class="dash-kpi">
                    <div class="dash-kpi__eyebrow">Diterima</div>
                    <div class="dash-kpi__value">{{ $counts['received'] }}</div>
                    <div class="dash-kpi__sub">Baru tiba</div>
                </div>
                <div class="dash-kpi">
                    <div class="dash-kpi__eyebrow">Disahkan</div>
                    <div class="dash-kpi__value">{{ $counts['verified'] }}</div>
                    <div class="dash-kpi__sub">OCR siap</div>
                </div>
                <div class="dash-kpi {{ $counts['officer_review'] > 10 ? 'is-warn' : '' }}">
                    <div class="dash-kpi__eyebrow">Semakan Pegawai</div>
                    <div class="dash-kpi__value">{{ $counts['officer_review'] }}</div>
                    <div class="dash-kpi__sub">Perlu keputusan</div>
                </div>
                <div class="dash-kpi is-ok">
                    <div class="dash-kpi__eyebrow">Diluluskan (24j)</div>
                    <div class="dash-kpi__value">{{ Application::where('status', 'approved')->where('updated_at', '>=', now()->subDay())->count() }}</div>
                    <div class="dash-kpi__sub">Sedia keluarkan</div>
                </div>
                <div class="dash-kpi">
                    <div class="dash-kpi__eyebrow">Dikeluarkan</div>
                    <div class="dash-kpi__value">{{ $counts['issued'] }}</div>
                    <div class="dash-kpi__sub">Selesai · arkib</div>
                </div>
            </div>
        </div>

        {{-- ==== Today's tasks ==== --}}
        <div class="dash-sec">
            <div class="dash-sec__head">
                <div class="dash-sec__eyebrow">Tugasan Diutamakan</div>
                <a class="dash-sec__cta" href="{{ route('system.tapisan') }}">Buka semua →</a>
            </div>

            @if($todayApps->isEmpty())
                <div class="dash-empty">
                    <div class="dash-empty__title">Tiada permohonan menunggu<span class="dot"></span></div>
                    <div class="dash-empty__sub">Backlog kosong. Ruang kerja anda lapang.</div>
                </div>
            @else
                <div class="dash-tugasan">
                    @foreach($todayApps as $app)
                        @php
                            $isLate = $app->sla_state === 'breached';
                            $docLabel = Application::DOC_LABELS[$app->doc_type] ?? $app->doc_type;
                            $shortDoc = ['birth' => 'KEL', 'marriage' => 'KAH', 'mykad' => 'KAD'][$app->doc_type] ?? '?';
                            $statusLabel = Application::STAGE_LABELS[$app->status] ?? $app->status;
                        @endphp
                        <a href="{{ route('system.tapisan.show', $app->reference_number) }}" class="dash-trow {{ $isLate ? 'is-late' : '' }}">
                            <div class="dash-trow__panel">{{ $shortDoc }}</div>
                            <div class="dash-trow__title-row">
                                <div class="dash-trow__title">{{ $app->applicant_name }}</div>
                                <div class="dash-trow__sub">
                                    <span class="mono">{{ $app->reference_number }}</span>
                                    <span class="sep">·</span>
                                    <span>{{ $docLabel }}</span>
                                    <span class="sep">·</span>
                                    <span>{{ $statusLabel }}</span>
                                </div>
                            </div>
                            <div class="dash-trow__due">
                                <div class="dash-trow__due-label">
                                    @if($isLate)
                                        Lewat {{ $app->created_at->diffForHumans(null, true) }}
                                    @elseif($app->ai_eta)
                                        ETA {{ $app->ai_eta->diffForHumans() }}
                                    @else
                                        {{ $app->created_at->diffForHumans() }}
                                    @endif
                                </div>
                                <div class="dash-trow__due-sub mono">Skor AI · {{ number_format(($app->ai_score ?? 0) * 100, 0) }}%</div>
                            </div>
                            <span class="dash-trow__cta {{ $isLate ? 'dash-trow__cta--orange' : 'dash-trow__cta--pine' }}">
                                Semak
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ==== Right rail ==== --}}
    <aside class="ws-page__rail">

        <div class="ai-card">
            <div class="ai-card__eyebrow"><span class="dot"></span> CADANGAN AI</div>
            <div class="ai-card__body">
                @if($counts['officer_review'] > 5)
                    <strong>{{ $counts['officer_review'] }} permohonan</strong> berskor tinggi AI menunggu kelulusan. Pertimbang <strong>lulus pukal</strong> di senarai tapisan untuk kosongkan backlog.
                @elseif($lateCount > 0)
                    <strong>{{ $lateCount }} permohonan</strong> melepasi SLA. Susun ikut <strong>tarikh paling lama</strong> untuk minimumkan eskalasi.
                @else
                    Backlog stabil. AI mengesyorkan rehat 5 minit sebelum batch seterusnya tiba.
                @endif
            </div>
            <div class="ai-card__meta">DIKEMASKINI · {{ now()->format('H:i') }} · MODEL v0.4-jpn</div>
        </div>

        <div class="rail-card">
            <div class="rail-card__head">
                <div class="rail-card__eyebrow">Prestasi Minggu Ini</div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                    <span style="color: var(--mute);">Diluluskan</span>
                    <span class="mono" style="font-weight: 600; color: var(--pine);">{{ Application::where('status', 'approved')->where('updated_at', '>=', now()->subWeek())->count() }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                    <span style="color: var(--mute);">Purata SLA</span>
                    <span class="mono" style="font-weight: 600; color: var(--success);">2.4 jam</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                    <span style="color: var(--mute);">Lewat</span>
                    <span class="mono" style="font-weight: 600; color: {{ $lateCount > 0 ? 'var(--orange)' : 'var(--mute)' }};">{{ $lateCount }}</span>
                </div>
            </div>
            <div class="rail-card__foot">
                <a class="rail-card__link" href="{{ route('system.audit') }}">Lihat log penuh →</a>
            </div>
        </div>

        <div class="rail-card">
            <div class="rail-card__head">
                <div class="rail-card__eyebrow">Aliran 5-langkah</div>
            </div>
            <div style="font-size: 12px; color: var(--mute); line-height: 1.6;">
                <div>1 · <strong style="color: var(--ink);">Diterima</strong> — sistem</div>
                <div>2 · <strong style="color: var(--ink);">Disahkan</strong> — OCR auto</div>
                <div>3 · <strong style="color: var(--ink);">Semakan Pegawai</strong> — anda</div>
                <div>4 · <strong style="color: var(--ink);">Diluluskan</strong> — anda / penyelia</div>
                <div>5 · <strong style="color: var(--ink);">Dikeluarkan</strong> — auto</div>
            </div>
        </div>

    </aside>
</div>
@endsection
