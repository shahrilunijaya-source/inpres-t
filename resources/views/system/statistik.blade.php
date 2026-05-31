@php
    use App\Models\Application;
@endphp

@extends('layouts.system', ['active' => 'statistik', 'title' => 'Statistik & Analitik'])

@section('content')
@php
    // ---- Line chart geometry ----
    $maxN = max(array_column($series, 'n')) ?: 1;
    $chartW = 760;
    $chartH = 160;
    $padL = 24; $padR = 12; $padT = 12; $padB = 24;
    $innerW = $chartW - $padL - $padR;
    $innerH = $chartH - $padT - $padB;
    $stepX = count($series) > 1 ? $innerW / (count($series) - 1) : 0;

    $points = [];
    foreach ($series as $i => $p) {
        $x = $padL + $i * $stepX;
        $y = $padT + $innerH - ($p['n'] / $maxN * $innerH);
        $points[] = ['x' => round($x, 2), 'y' => round($y, 2), 'n' => $p['n'], 'date' => $p['date']];
    }
    $pathStr = '';
    foreach ($points as $i => $p) {
        $pathStr .= ($i === 0 ? 'M ' : 'L ') . $p['x'] . ' ' . $p['y'] . ' ';
    }
    $areaStr = 'M ' . $points[0]['x'] . ' ' . ($padT + $innerH) . ' ';
    foreach ($points as $p) {
        $areaStr .= 'L ' . $p['x'] . ' ' . $p['y'] . ' ';
    }
    $areaStr .= 'L ' . end($points)['x'] . ' ' . ($padT + $innerH) . ' Z';

    // ---- By doc type bar data ----
    $docMax = max($byDoc ?: [1]);
    $docColors = [
        'birth' => '',
        'marriage' => 'is-pine',
        'mykad' => 'is-orange',
        'death' => 'is-mute',
        'adoption' => 'is-pine',
        'name_change' => '',
    ];
    $docLabels = [
        'birth' => 'Sijil Kelahiran',
        'marriage' => 'Sijil Perkahwinan',
        'mykad' => 'MyKAD',
        'death' => 'Sijil Kematian',
        'adoption' => 'Pengangkatan',
        'name_change' => 'Pertukaran Nama',
    ];
    $docStubKeys = ['death', 'adoption', 'name_change'];

    // ---- Donut math ----
    $statusOrder = ['received', 'verified', 'officer_review', 'approved', 'issued', 'rejected'];
    $statusColors = [
        'received' => '#8A99B0',
        'verified' => '#0FA975',
        'officer_review' => '#84286B',
        'approved' => '#0FA975',
        'issued' => '#1B3A6B',
        'rejected' => '#C0392B',
    ];
    $totalStatus = array_sum($byStatus);
    $circumference = 2 * M_PI * 56; // r=56
    $donutSegments = [];
    $offset = 0;
    foreach ($statusOrder as $s) {
        $n = $byStatus[$s] ?? 0;
        if ($n === 0) continue;
        $frac = $n / max($totalStatus, 1);
        $len = $frac * $circumference;
        $donutSegments[] = [
            'status' => $s,
            'n' => $n,
            'color' => $statusColors[$s],
            'len' => $len,
            'offset' => -$offset,
        ];
        $offset += $len;
    }

    // ---- Heatmap intensity ----
    $heatMax = 1;
    foreach ($heatmap as $row) {
        foreach ($row as $v) if ($v > $heatMax) $heatMax = $v;
    }
    $heatColorFor = function ($v) use ($heatMax) {
        if ($v === 0) return 'background: var(--paper-2);';
        $ratio = min(1, $v / $heatMax);
        // teal-soft → teal
        $alpha = 0.10 + $ratio * 0.85;
        return "background: rgba(var(--brand-rgb), {$alpha});";
    };
    $dowLabels = ['Aha', 'Isn', 'Sel', 'Rab', 'Kha', 'Jum', 'Sab'];

    // ---- KPI sparkline (last 10 days from series) ----
    $sparkPoints = array_slice($points, -10);
    $sparkMin = min(array_column($sparkPoints, 'y'));
    $sparkMax = max(array_column($sparkPoints, 'y'));
    $sparkH = 36;
    $sparkW = 240;
    $sparkPath = '';
    foreach ($sparkPoints as $i => $p) {
        $sx = $i * ($sparkW / (count($sparkPoints) - 1));
        $sy = $sparkH - ((($p['y'] - $sparkMin) / max(1, $sparkMax - $sparkMin)) * ($sparkH - 4)) - 2;
        // Note: y from points is already inverted (smaller y = bigger n). So we map directly.
        $sparkPath .= ($i === 0 ? 'M ' : 'L ') . round($sx, 1) . ' ' . round($sparkH - $sy + 2, 1) . ' ';
    }
@endphp

<div class="ws-page is-full">
    <div class="ws-page__main">

        {{-- ==== Head ==== --}}
        <div class="stat-head">
            <div>
                <h1 class="stat-head__h1">Statistik & Analitik<span class="dot"></span></h1>
                <p class="stat-head__sub">
                    Pandangan menyeluruh permohonan, prestasi pegawai, dan pola aktiviti.
                    <strong>30 hari</strong> terakhir.
                </p>
            </div>
            <div class="stat-range">
                <button type="button" class="stat-range__btn js-stub" data-module="Penapis: Hari Ini">Hari Ini</button>
                <button type="button" class="stat-range__btn js-stub" data-module="Penapis: 7 Hari">7 Hari</button>
                <button type="button" class="stat-range__btn is-active">30 Hari</button>
                <button type="button" class="stat-range__btn js-stub" data-module="Penapis: Tahun Ini">Tahun Ini</button>
            </div>
        </div>

        {{-- ==== KPI strip ==== --}}
        <div class="stat-kpis">
            <div class="stat-kpi">
                <span class="stat-kpi__eyebrow">Jumlah Permohonan</span>
                <span class="stat-kpi__big">{{ number_format($kpi['total']) }}</span>
                <span class="stat-kpi__delta">▲ 12.4% berbanding bulan lepas</span>
                <svg class="stat-kpi__spark" viewBox="0 0 240 36" preserveAspectRatio="none">
                    <path d="M 0 28 L 24 22 L 48 24 L 72 18 L 96 14 L 120 16 L 144 10 L 168 12 L 192 8 L 216 6 L 240 4"
                          fill="none" stroke="var(--teal)" stroke-width="1.5"/>
                </svg>
            </div>

            <div class="stat-kpi is-ok">
                <span class="stat-kpi__eyebrow">Diluluskan</span>
                <span class="stat-kpi__big">{{ number_format($kpi['approved']) }}</span>
                <span class="stat-kpi__delta">▲ 8.7%</span>
                <svg class="stat-kpi__spark" viewBox="0 0 240 36" preserveAspectRatio="none">
                    <path d="M 0 30 L 24 28 L 48 24 L 72 22 L 96 18 L 120 20 L 144 14 L 168 12 L 192 10 L 216 8 L 240 5"
                          fill="none" stroke="var(--success)" stroke-width="1.5"/>
                </svg>
            </div>

            <div class="stat-kpi {{ $kpi['late'] > 10 ? 'is-warn' : '' }}">
                <span class="stat-kpi__eyebrow">Lewat SLA</span>
                <span class="stat-kpi__big">{{ number_format($kpi['late']) }}</span>
                <span class="stat-kpi__delta is-down">▲ 4.2% perlu perhatian</span>
                <svg class="stat-kpi__spark" viewBox="0 0 240 36" preserveAspectRatio="none">
                    <path d="M 0 26 L 24 24 L 48 22 L 72 20 L 96 18 L 120 16 L 144 14 L 168 16 L 192 12 L 216 10 L 240 8"
                          fill="none" stroke="var(--orange)" stroke-width="1.5"/>
                </svg>
            </div>

            <div class="stat-kpi">
                <span class="stat-kpi__eyebrow">Purata Masa Proses</span>
                <span class="stat-kpi__big">{{ $kpi['avg_hours'] }} <small style="font-size: 14px; color: var(--mute); font-weight: 500;">jam</small></span>
                <span class="stat-kpi__delta">▼ 18 minit lebih pantas</span>
                <svg class="stat-kpi__spark" viewBox="0 0 240 36" preserveAspectRatio="none">
                    <path d="M 0 16 L 24 18 L 48 14 L 72 16 L 96 12 L 120 10 L 144 14 L 168 8 L 192 10 L 216 6 L 240 8"
                          fill="none" stroke="var(--pine)" stroke-width="1.5"/>
                </svg>
            </div>
        </div>

        {{-- ==== Main grid: 2-col ==== --}}
        <div class="stat-grid">

            <div class="stat-card">
                <div class="stat-card__head">
                    <div>
                        <div class="stat-card__eyebrow">Trend</div>
                        <div class="stat-card__h">Permohonan harian — 30 hari</div>
                    </div>
                    <div class="stat-card__meta">{{ end($series)['date'] }}</div>
                </div>

                <svg class="stat-line__svg" viewBox="0 0 {{ $chartW }} {{ $chartH }}" preserveAspectRatio="none">
                    {{-- y-grid --}}
                    @foreach([0.25, 0.5, 0.75, 1] as $r)
                        @php $y = $padT + $innerH * (1 - $r); @endphp
                        <line class="stat-line__grid" x1="{{ $padL }}" x2="{{ $chartW - $padR }}" y1="{{ $y }}" y2="{{ $y }}"/>
                    @endforeach
                    {{-- area --}}
                    <path class="stat-line__area" d="{{ $areaStr }}"/>
                    {{-- line --}}
                    <path class="stat-line__path" d="{{ $pathStr }}"/>
                    {{-- last dot --}}
                    <circle class="stat-line__dot" cx="{{ end($points)['x'] }}" cy="{{ end($points)['y'] }}" r="4"/>
                    {{-- y axis labels --}}
                    <text class="stat-line__axis" x="4" y="{{ $padT + 4 }}">{{ $maxN }}</text>
                    <text class="stat-line__axis" x="4" y="{{ $padT + $innerH + 4 }}">0</text>
                    {{-- x axis (first, mid, last) --}}
                    <text class="stat-line__axis" x="{{ $padL }}" y="{{ $chartH - 6 }}">{{ \Carbon\Carbon::parse($series[0]['date'])->format('d M') }}</text>
                    <text class="stat-line__axis" x="{{ $padL + $innerW / 2 - 18 }}" y="{{ $chartH - 6 }}">{{ \Carbon\Carbon::parse($series[15]['date'])->format('d M') }}</text>
                    <text class="stat-line__axis" x="{{ $chartW - $padR - 30 }}" y="{{ $chartH - 6 }}">{{ \Carbon\Carbon::parse(end($series)['date'])->format('d M') }}</text>
                    {{-- invisible hit circles for tooltip --}}
                    @foreach($points as $p)
                        <circle class="stat-line__hit" cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="10"
                                data-tip-label="{{ \Carbon\Carbon::parse($p['date'])->isoFormat('ddd, D MMM Y') }}"
                                data-tip-big="{{ $p['n'] }} permohonan"
                                data-tip-sub="Diterima sepanjang hari ini"></circle>
                    @endforeach
                </svg>
            </div>

            <div class="stat-card">
                <div class="stat-card__head">
                    <div>
                        <div class="stat-card__eyebrow">Taburan</div>
                        <div class="stat-card__h">Status semasa</div>
                    </div>
                </div>

                <div class="stat-donut">
                    <div class="stat-donut__center">
                        <svg class="stat-donut__svg" viewBox="0 0 160 160">
                            <circle cx="80" cy="80" r="56" fill="none" stroke="var(--paper-2)" stroke-width="18"/>
                            @foreach($donutSegments as $seg)
                                <circle cx="80" cy="80" r="56" fill="none"
                                        stroke="{{ $seg['color'] }}"
                                        stroke-width="18"
                                        stroke-dasharray="{{ round($seg['len'], 1) }} {{ round($circumference - $seg['len'], 1) }}"
                                        stroke-dashoffset="{{ round($seg['offset'], 1) }}"/>
                            @endforeach
                        </svg>
                        <div class="stat-donut__center-text">
                            <span class="stat-donut__center-big">{{ number_format($totalStatus) }}</span>
                            <span class="stat-donut__center-sub">Jumlah</span>
                        </div>
                    </div>

                    <div class="stat-donut__legend">
                        @foreach($donutSegments as $seg)
                            @php $pct = $totalStatus > 0 ? round($seg['n'] / $totalStatus * 100, 1) : 0; @endphp
                            <div class="stat-donut__row"
                                 data-tip-label="{{ strtoupper(Application::STAGE_LABELS[$seg['status']] ?? $seg['status']) }}"
                                 data-tip-big="{{ $seg['n'] }} permohonan · {{ $pct }}%"
                                 data-tip-sub="Daripada {{ number_format($totalStatus) }} permohonan keseluruhan dalam sistem">
                                <span class="stat-donut__sw" style="background: {{ $seg['color'] }};"></span>
                                <span class="stat-donut__label">{{ Application::STAGE_LABELS[$seg['status']] ?? $seg['status'] }}</span>
                                <span class="stat-donut__num">{{ $seg['n'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <div class="stat-grid">

            <div class="stat-card">
                <div class="stat-card__head">
                    <div>
                        <div class="stat-card__eyebrow">Mengikut Jenis</div>
                        <div class="stat-card__h">Permohonan dokumen — semua jenis</div>
                    </div>
                    <div class="stat-card__meta">6 jenis</div>
                </div>

                <div class="stat-bar">
                    @php $docTotal = array_sum($byDoc); @endphp
                    @foreach($docLabels as $key => $label)
                        @php
                            $n = $byDoc[$key] ?? 0;
                            $pct = round($n / max($docMax, 1) * 100);
                            $sharePct = $docTotal > 0 ? round($n / $docTotal * 100, 1) : 0;
                            $cls = $docColors[$key];
                            $isStub = in_array($key, $docStubKeys, true);
                        @endphp
                        <div class="stat-bar__row"
                             data-tip-label="{{ strtoupper($label) }}"
                             data-tip-big="{{ number_format($n) }} permohonan · {{ $sharePct }}%"
                             data-tip-sub="{{ $isStub ? 'Modul anggaran — belum dilaksanakan dalam prototaip' : 'Modul aktif dalam pengeluaran' }}">
                            <span class="stat-bar__label">
                                {{ $label }}
                                @if($isStub)
                                    <span style="font-size: 9.5px; color: var(--mute-2); letter-spacing: 0.06em; font-weight: 600; text-transform: uppercase; margin-left: 4px;">· anggaran</span>
                                @endif
                            </span>
                            <span class="stat-bar__track">
                                <span class="stat-bar__fill {{ $cls }}" style="width: {{ $pct }}%;"></span>
                            </span>
                            <span class="stat-bar__num">{{ number_format($n) }}</span>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; font-size: 11.5px; color: var(--mute);">
                    <span>Jumlah keseluruhan permohonan</span>
                    <span class="mono" style="color: var(--pine); font-weight: 700;">{{ number_format(array_sum($byDoc)) }} permohonan</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card__head">
                    <div>
                        <div class="stat-card__eyebrow">Prestasi</div>
                        <div class="stat-card__h">Pegawai berprestasi tinggi</div>
                    </div>
                    <div class="stat-card__meta">30 hari</div>
                </div>

                <div class="stat-lead">
                    @forelse($topOfficers as $idx => $row)
                        <div class="stat-lead__row">
                            <span class="stat-lead__rank">{{ $idx + 1 }}</span>
                            <div class="stat-lead__body">
                                <div class="stat-lead__name">{{ $row->officer?->name ?? 'Pegawai #' . $row->officer_id }}</div>
                                <div class="stat-lead__role">{{ strtoupper($row->officer?->role ?? 'officer') }}</div>
                            </div>
                            <div class="stat-lead__n">
                                {{ $row->n }}
                                <small>Lulus</small>
                            </div>
                        </div>
                    @empty
                        <div style="font-size: 12.5px; color: var(--mute); padding: 14px 0;">Tiada data kelulusan dalam tempoh ini.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ==== Aktiviti Modul Sistem (all sidebar groups) ==== --}}
        <div class="stat-card">
            <div class="stat-card__head">
                <div>
                    <div class="stat-card__eyebrow">Liputan Sistem</div>
                    <div class="stat-card__h">Aktiviti mengikut modul</div>
                </div>
                <div class="stat-card__meta">6 kumpulan modul</div>
            </div>

            <div class="stat-mods">
                @php
                    $maxMod = max(array_column($modules, 'count')) ?: 1;
                    $iconSvg = [
                        'inbox' => '<polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11Z"/>',
                        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                        'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>',
                        'globe' => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
                        'chart' => '<line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/>',
                        'cog' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
                    ];
                @endphp

                @php $modTotal = array_sum(array_column($modules, 'count')); @endphp
                @foreach($modules as $mod)
                    @php
                        $pct = round($mod['count'] / max($maxMod, 1) * 100);
                        $sharePct = $modTotal > 0 ? round($mod['count'] / $modTotal * 100, 1) : 0;
                    @endphp
                    <div class="stat-mod {{ $mod['cls'] }}"
                         data-tip-label="{{ strtoupper($mod['label']) }}"
                         data-tip-big="{{ number_format($mod['count']) }} interaksi · {{ $sharePct }}%"
                         data-tip-sub="{{ $mod['sub'] }} · 30 hari terakhir">
                        <div class="stat-mod__head">
                            <div class="stat-mod__icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $iconSvg[$mod['icon']] ?? '' !!}
                                </svg>
                            </div>
                            <div class="stat-mod__body">
                                <div class="stat-mod__label">{{ $mod['label'] }}</div>
                                <div class="stat-mod__sub">{{ $mod['sub'] }}</div>
                            </div>
                            <div class="stat-mod__count">{{ number_format($mod['count']) }}</div>
                        </div>
                        <div class="stat-mod__track">
                            <span class="stat-mod__fill" style="width: {{ $pct }}%;"></span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; font-size: 11.5px; color: var(--mute);">
                <span>Liputan modul · 30 hari</span>
                <span class="mono" style="color: var(--pine); font-weight: 700;">{{ number_format(array_sum(array_column($modules, 'count'))) }} interaksi sistem</span>
            </div>
        </div>

        {{-- ==== Heatmap full-width ==== --}}
        <div class="stat-card">
            <div class="stat-card__head">
                <div>
                    <div class="stat-card__eyebrow">Pola Aktiviti</div>
                    <div class="stat-card__h">Heatmap audit · 14 hari × 24 jam</div>
                </div>
                <div class="stat-card__meta">{{ array_sum(array_map('array_sum', $heatmap)) }} peristiwa</div>
            </div>

            <div class="stat-heatmap__grid">
                @foreach($dowLabels as $dow => $label)
                    <div class="stat-heatmap__row-label">{{ $label }}</div>
                    @for($hr = 0; $hr < 24; $hr++)
                        @php $v = $heatmap[$dow][$hr] ?? 0; @endphp
                        <div class="stat-heatmap__cell"
                             style="{{ $heatColorFor($v) }}"
                             data-tip="{{ $label }} {{ str_pad($hr, 2, '0', STR_PAD_LEFT) }}:00 · {{ $v }} peristiwa"></div>
                    @endfor
                @endforeach
            </div>

            <div class="stat-heatmap__axis">
                <div></div>
                @for($hr = 0; $hr < 24; $hr++)
                    <div>{{ $hr % 3 === 0 ? str_pad($hr, 2, '0', STR_PAD_LEFT) : '' }}</div>
                @endfor
            </div>

            <div class="stat-heatmap__legend">
                <span>SUNYI</span>
                <div class="stat-heatmap__scale">
                    <div style="background: var(--paper-2);"></div>
                    <div style="background: rgba(var(--brand-rgb),0.20);"></div>
                    <div style="background: rgba(var(--brand-rgb),0.45);"></div>
                    <div style="background: rgba(var(--brand-rgb),0.70);"></div>
                    <div style="background: rgba(var(--brand-rgb),0.95);"></div>
                </div>
                <span>SIBUK</span>
            </div>
        </div>

    </div>
</div>

{{-- ============ Floating tooltip ============ --}}
<div class="chart-tip" id="chartTip" role="tooltip" aria-hidden="true">
    <div class="chart-tip__label" id="chartTipLabel"></div>
    <div class="chart-tip__big" id="chartTipBig"></div>
    <div class="chart-tip__sub" id="chartTipSub"></div>
</div>

<script>
(function () {
    const tip = document.getElementById('chartTip');
    const tipLabel = document.getElementById('chartTipLabel');
    const tipBig = document.getElementById('chartTipBig');
    const tipSub = document.getElementById('chartTipSub');

    function show(e, label, big, sub) {
        tipLabel.textContent = label || '';
        tipBig.textContent = big || '';
        tipSub.textContent = sub || '';
        tipLabel.style.display = label ? 'block' : 'none';
        tipSub.style.display = sub ? 'block' : 'none';
        tip.classList.add('is-visible');
        move(e);
    }

    function hide() {
        tip.classList.remove('is-visible');
    }

    function move(e) {
        // Position above the cursor; clamp inside viewport
        const padding = 16;
        const rect = tip.getBoundingClientRect();
        const tipW = rect.width || 200;
        const vw = window.innerWidth;
        let x = e.clientX;
        if (x - tipW / 2 < padding) x = padding + tipW / 2;
        if (x + tipW / 2 > vw - padding) x = vw - padding - tipW / 2;
        tip.style.left = x + 'px';
        tip.style.top = (e.clientY) + 'px';
    }

    document.addEventListener('mousemove', function (e) {
        const t = e.target.closest('[data-tip-big]');
        if (t) {
            show(e, t.dataset.tipLabel, t.dataset.tipBig, t.dataset.tipSub);
        } else {
            hide();
        }
    });

    document.addEventListener('mouseleave', hide);
    window.addEventListener('scroll', hide);
})();
</script>
@endsection
