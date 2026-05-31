@extends('layouts.system', ['active' => 'kanban', 'title' => 'Tugasan Sistem'])

@section('content')
@php
    $typeChips = [
        'backup'      => ['code' => 'BKP', 'cls' => 'jb-chip--backup',      'label' => 'Sandaran'],
        'integration' => ['code' => 'INT', 'cls' => 'jb-chip--integration','label' => 'Integrasi'],
        'cert'        => ['code' => 'CRT', 'cls' => 'jb-chip--cert',        'label' => 'Sijil/Kunci'],
        'report'      => ['code' => 'RPT', 'cls' => 'jb-chip--report',      'label' => 'Laporan'],
        'cleanup'     => ['code' => 'CLN', 'cls' => 'jb-chip--cleanup',     'label' => 'Penyelenggaraan'],
    ];

    $colClass = [
        'queued'  => 'jb-col--queued',
        'running' => 'jb-col--running',
        'done'    => 'jb-col--done',
        'failed'  => 'jb-col--failed',
    ];
@endphp

<div class="ws-page is-full">
    <div class="ws-page__main">

        <div class="kan-head">
            <div>
                <h1 class="kan-head__h1">Tugasan Sistem<span class="dot"></span></h1>
                <p class="kan-head__sub">
                    Baris gilir kerja latar belakang.
                    <strong>{{ $totals['running'] }} berjalan</strong> ·
                    <strong>{{ $totals['queued'] }} berjadual</strong> ·
                    @if($totals['failed'] > 0)
                        <strong style="color: var(--orange);">{{ $totals['failed'] }} gagal</strong> · perlu campur tangan.
                    @else
                        tiada kegagalan.
                    @endif
                </p>
            </div>
            <div class="kan-head__cluster">
                <span class="kan-hint" style="background: rgba(16,185,129,0.10); color: var(--success); border: 1px solid rgba(16,185,129,0.30);">
                    <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background: var(--success); animation: pulse 2s infinite;"></span>
                    Penjadual aktif · 99.94% uptime
                </span>
            </div>
        </div>

        <div class="kan-summary">
            <div class="kan-kpi kan-kpi--total">
                <span class="kan-kpi__lbl">Jumlah Kerja</span>
                <span class="kan-kpi__num">{{ $totals['all'] }}</span>
                <span class="kan-kpi__hint">24 jam terakhir</span>
            </div>
            <div class="kan-kpi kan-kpi--ok">
                <span class="kan-kpi__lbl">Berjalan</span>
                <span class="kan-kpi__num">{{ $totals['running'] }}</span>
                <span class="kan-kpi__hint">aktif sekarang</span>
            </div>
            <div class="kan-kpi kan-kpi--risk">
                <span class="kan-kpi__lbl">Berjadual</span>
                <span class="kan-kpi__num">{{ $totals['queued'] }}</span>
                <span class="kan-kpi__hint">menunggu cron</span>
            </div>
            <div class="kan-kpi kan-kpi--late">
                <span class="kan-kpi__lbl">Gagal</span>
                <span class="kan-kpi__num">{{ $totals['failed'] }}</span>
                <span class="kan-kpi__hint">perlu ulang manual</span>
            </div>
        </div>

        <div class="jb-board">
            @foreach($columns as $col => $title)
                @php $jobs = $grouped[$col] ?? []; @endphp
                <div class="jb-col {{ $colClass[$col] }}">
                    <div class="jb-col__head">
                        <span class="jb-col__title">
                            <span class="jb-col__dot"></span>
                            {{ $title }}
                        </span>
                        <span class="jb-col__count">{{ count($jobs) }}</span>
                    </div>

                    <div class="jb-col__body">
                        @forelse($jobs as $job)
                            @php
                                $chip = $typeChips[$job['type']] ?? ['code' => '?', 'cls' => '', 'label' => $job['type']];
                                $sev = $job['severity'] ?? 'normal';
                            @endphp
                            <div class="jb-card jb-card--{{ $col }}">
                                <div class="jb-card__top">
                                    <span class="mono jb-card__id">{{ $job['id'] }}</span>
                                    <span class="jb-card__chip {{ $chip['cls'] }}">{{ $chip['code'] }}</span>
                                </div>

                                <div class="jb-card__name">{{ $job['name'] }}</div>

                                @if($col === 'running')
                                    <div class="jb-card__progress">
                                        <div class="jb-card__progress-bar">
                                            <div class="jb-card__progress-fill" style="width: {{ $job['progress'] }}%;"></div>
                                        </div>
                                        <span class="mono jb-card__progress-pct">{{ $job['progress'] }}%</span>
                                    </div>
                                @endif

                                @if($col === 'failed' && !empty($job['error']))
                                    <div class="jb-card__error">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        <span>{{ $job['error'] }}</span>
                                    </div>
                                @endif

                                <div class="jb-card__foot">
                                    @if($col === 'queued')
                                        <span class="mono jb-card__when">{{ $job['eta'] }}</span>
                                        <span class="jb-card__owner">{{ $job['owner'] === 'jadual' ? 'CRON' : strtoupper($job['owner']) }}</span>
                                    @elseif($col === 'running')
                                        <span class="mono jb-card__when">{{ $job['eta'] }}</span>
                                        <span class="jb-card__owner">{{ $job['started']?->diffForHumans(null, true) }}</span>
                                    @elseif($col === 'done')
                                        <span class="mono jb-card__when">✓ {{ $job['eta'] }}</span>
                                        <span class="jb-card__owner">{{ $job['started']?->diffForHumans() }}</span>
                                    @elseif($col === 'failed')
                                        <button type="button" class="jb-card__retry js-stub" data-module="Ulang Kerja {{ $job['id'] }}">
                                            ⟳ Ulang
                                        </button>
                                        <span class="jb-card__owner">{{ $job['started']?->diffForHumans() }}</span>
                                    @endif
                                </div>

                                @if($sev === 'high')
                                    <span class="jb-card__sev jb-card__sev--high">RISIKO TINGGI</span>
                                @elseif($sev === 'medium')
                                    <span class="jb-card__sev jb-card__sev--med">RISIKO SEDERHANA</span>
                                @endif
                            </div>
                        @empty
                            <div class="kan-col__empty">Kosong · tiada kerja {{ strtolower($title) }}</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
