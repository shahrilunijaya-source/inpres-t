@php
    use App\Models\Application;
@endphp

@extends('layouts.system', ['active' => 'kanban', 'title' => 'Tugasan Pasukan'])

@section('content')
@php
    $colTitles = [
        'received' => 'Diterima',
        'verified' => 'Disahkan',
        'officer_review' => 'Semakan',
        'approved' => 'Diluluskan',
    ];

    $docChips = [
        'birth' => ['code' => 'KEL', 'cls' => 'kan-card__chip--birth'],
        'marriage' => ['code' => 'KAH', 'cls' => 'kan-card__chip--marriage'],
        'mykad' => ['code' => 'KAD', 'cls' => 'kan-card__chip--mykad'],
    ];

    $avgLoad = $totals['team_size'] > 0
        ? round(($totals['total_apps'] - $totals['unassigned']) / $totals['team_size'], 1)
        : 0;
@endphp

<div class="ws-page is-full">
    <div class="ws-page__main">

        <div class="kan-head">
            <div>
                <h1 class="kan-head__h1">Tugasan Pasukan<span class="dot"></span></h1>
                <p class="kan-head__sub">
                    Papan penyeliaan. <strong>{{ $totals['team_size'] }} pegawai</strong> ·
                    <strong>{{ $totals['total_apps'] }} permohonan aktif</strong> ·
                    purata <strong class="mono">{{ $avgLoad }}</strong> kes / pegawai.
                    Seret kad antara pegawai untuk agih semula.
                </p>
            </div>
            <div class="kan-head__cluster">
                <span class="kan-hint">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M22 11h-6"/></svg>
                    Agih semula = audit log
                </span>
            </div>
        </div>

        <div class="kan-summary">
            <div class="kan-kpi kan-kpi--total">
                <span class="kan-kpi__lbl">Pegawai Aktif</span>
                <span class="kan-kpi__num">{{ $totals['team_size'] }}</span>
                <span class="kan-kpi__hint">semua dalam talian</span>
            </div>
            <div class="kan-kpi kan-kpi--ok">
                <span class="kan-kpi__lbl">Dalam Saluran</span>
                <span class="kan-kpi__num">{{ $totals['total_apps'] }}</span>
                <span class="kan-kpi__hint">merentas pasukan</span>
            </div>
            <div class="kan-kpi kan-kpi--risk">
                <span class="kan-kpi__lbl">Belum Diagihkan</span>
                <span class="kan-kpi__num">{{ $totals['unassigned'] }}</span>
                <span class="kan-kpi__hint">memerlukan agihan</span>
            </div>
            <div class="kan-kpi kan-kpi--late">
                <span class="kan-kpi__lbl">SLA Dilanggar</span>
                <span class="kan-kpi__num">{{ $totals['late'] }}</span>
                <span class="kan-kpi__hint">merentas pasukan</span>
            </div>
        </div>

        {{-- Unassigned pool --}}
        <div class="sv-pool">
            <div class="sv-pool__head">
                <div>
                    <div class="sv-pool__eyebrow">Kolam Belum Diagihkan</div>
                    <div class="sv-pool__title">{{ $unassigned->count() }} permohonan menunggu pegawai</div>
                </div>
                <button type="button" class="dash-trow__cta js-stub" data-module="Auto-Agihan Beban Sama">
                    Auto-agih ikut beban
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                </button>
            </div>
            <div class="sv-pool__strip">
                @forelse($unassigned->take(8) as $app)
                    @php $chip = $docChips[$app->doc_type] ?? ['code' => '?', 'cls' => '']; @endphp
                    <a href="{{ route('system.tapisan.show', $app->reference_number) }}" class="sv-pool__card">
                        <div class="sv-pool__card-top">
                            <span class="kan-card__chip {{ $chip['cls'] }}">{{ $chip['code'] }}</span>
                            <span class="mono sv-pool__ref">{{ $app->reference_number }}</span>
                        </div>
                        <div class="sv-pool__card-name">{{ $app->applicant_name }}</div>
                        <div class="sv-pool__card-sub">
                            <span class="mono">{{ $app->applicant_ic }}</span>
                            <span class="sep">·</span>
                            <span>{{ $app->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <div class="sv-pool__empty">Tiada permohonan tergantung · pasukan bersedia</div>
                @endforelse
                @if($unassigned->count() > 8)
                    <div class="sv-pool__more">+{{ $unassigned->count() - 8 }} lagi</div>
                @endif
            </div>
        </div>

        {{-- Swimlane matrix: rows = officers, cols = status --}}
        <div class="sv-matrix">
            <div class="sv-matrix__head">
                <div class="sv-matrix__head-officer">Pegawai · Beban</div>
                @foreach($columns as $col)
                    <div class="sv-matrix__head-col">
                        <span class="sv-matrix__col-dot sv-matrix__col-dot--{{ $col }}"></span>
                        {{ $colTitles[$col] }}
                    </div>
                @endforeach
            </div>

            @foreach($byOfficer as $row)
                @php
                    $officer = $row['officer'];
                    $initials = '';
                    foreach (explode(' ', $officer->name) as $part) {
                        if ($part && strlen($initials) < 2) $initials .= strtoupper(mb_substr($part, 0, 1));
                    }
                    $loadPct = min(100, $row['total'] * 8);
                    $isHeavy = $loadPct > 70;
                    $isLate = $row['late'] > 0;
                @endphp
                <div class="sv-lane">
                    <div class="sv-lane__officer">
                        <div class="sv-lane__avatar" style="background: {{ $isHeavy ? 'var(--orange-soft)' : 'var(--teal-soft)' }}; color: {{ $isHeavy ? 'var(--orange)' : 'var(--teal-deep)' }};">{{ $initials }}</div>
                        <div class="sv-lane__officer-body">
                            <div class="sv-lane__officer-name">{{ $officer->name }}</div>
                            <div class="sv-lane__officer-sub">
                                <span class="mono" style="text-transform: uppercase; letter-spacing: 0.06em; font-size: 10px;">{{ $officer->role }}</span>
                                <span class="sep">·</span>
                                <span class="mono" style="color: {{ $isHeavy ? 'var(--orange)' : 'var(--pine)' }}; font-weight: 700;">{{ $row['total'] }} kes</span>
                                @if($isLate)
                                    <span class="sep">·</span>
                                    <span class="mono" style="color: var(--orange); font-weight: 700;">{{ $row['late'] }} lewat</span>
                                @endif
                            </div>
                            <div class="sv-lane__bar">
                                <div class="sv-lane__bar-fill" style="width: {{ $loadPct }}%; background: {{ $isHeavy ? 'linear-gradient(90deg, #e85a26 0%, var(--orange) 100%)' : 'linear-gradient(90deg, var(--teal-deep) 0%, var(--teal) 100%)' }};"></div>
                            </div>
                        </div>
                    </div>

                    @foreach($columns as $col)
                        @php $count = $row['by_col'][$col] ?? 0; @endphp
                        <div class="sv-cell {{ $count === 0 ? 'is-empty' : '' }}">
                            @if($count === 0)
                                <span class="sv-cell__dash">—</span>
                            @else
                                <div class="sv-cell__num mono">{{ $count }}</div>
                                <div class="sv-cell__lbl">kes</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
