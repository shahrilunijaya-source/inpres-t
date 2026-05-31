@php
    use App\Models\Application;
@endphp

@extends('layouts.system', ['active' => 'kanban', 'title' => 'Tugasan Saya'])

@section('content')
@php
    $colTitles = [
        'received' => 'Diterima',
        'verified' => 'Disahkan',
        'officer_review' => 'Semakan Pegawai',
        'approved' => 'Diluluskan',
    ];

    $docChips = [
        'birth' => ['code' => 'KEL', 'cls' => 'kan-card__chip--birth'],
        'marriage' => ['code' => 'KAH', 'cls' => 'kan-card__chip--marriage'],
        'mykad' => ['code' => 'KAD', 'cls' => 'kan-card__chip--mykad'],
    ];

    $slaClass = function ($app) {
        if ($app->sla_state === 'breached') return 'kan-card__sla--late';
        if ($app->sla_state === 'at_risk') return 'kan-card__sla--risk';
        return 'kan-card__sla--ok';
    };
    $slaLabel = function ($app) {
        if ($app->sla_state === 'breached') return 'LEWAT';
        if ($app->sla_state === 'at_risk') return 'KRITIKAL';
        return 'ON TRACK';
    };

    $totalCards = collect($grouped)->map->count()->sum();

    $laneBreakdown = [];
    $totals = ['late' => 0, 'risk' => 0, 'ok' => 0];
    foreach ($columns as $col) {
        $items = $grouped[$col] ?? collect();
        $late = $items->where('sla_state', 'breached')->count();
        $risk = $items->where('sla_state', 'at_risk')->count();
        $ok   = $items->count() - $late - $risk;
        $laneBreakdown[$col] = ['late' => $late, 'risk' => $risk, 'ok' => $ok];
        $totals['late'] += $late;
        $totals['risk'] += $risk;
        $totals['ok']   += $ok;
    }
@endphp

<div class="ws-page is-full">
    <div class="ws-page__main">

        <div class="kan-head">
            <div>
                <h1 class="kan-head__h1">Tugasan Saya<span class="dot"></span></h1>
                <p class="kan-head__sub">
                    Papan kerja peribadi. <strong>{{ $totalCards }} permohonan</strong> aktif merentas 4 lajur.
                    Seret kad untuk alih status.
                </p>
            </div>
            <div class="kan-head__cluster">
                <span class="kan-hint">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    Audit log mencatatkan setiap pergerakan
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('system.kanban.move') }}" id="kanMoveForm" style="display:none;">
            @csrf
            <input type="hidden" name="reference" id="kanMoveRef">
            <input type="hidden" name="to" id="kanMoveTo">
        </form>

        <div class="kan-summary" id="kanSummary"
             data-total="{{ $totalCards }}"
             data-late="{{ $totals['late'] }}"
             data-risk="{{ $totals['risk'] }}"
             data-ok="{{ $totals['ok'] }}">
            <div class="kan-kpi kan-kpi--total">
                <span class="kan-kpi__lbl">Jumlah Aktif</span>
                <span class="kan-kpi__num" data-kpi="total">{{ $totalCards }}</span>
                <span class="kan-kpi__hint">merentas 4 lajur</span>
            </div>
            <div class="kan-kpi kan-kpi--ok">
                <span class="kan-kpi__lbl">On Track</span>
                <span class="kan-kpi__num" data-kpi="ok">{{ $totals['ok'] }}</span>
                <span class="kan-kpi__hint">dalam SLA</span>
            </div>
            <div class="kan-kpi kan-kpi--risk">
                <span class="kan-kpi__lbl">Kritikal</span>
                <span class="kan-kpi__num" data-kpi="risk">{{ $totals['risk'] }}</span>
                <span class="kan-kpi__hint">hampir LEWAT</span>
            </div>
            <div class="kan-kpi kan-kpi--late">
                <span class="kan-kpi__lbl">Lewat</span>
                <span class="kan-kpi__num" data-kpi="late">{{ $totals['late'] }}</span>
                <span class="kan-kpi__hint">SLA dilanggar</span>
            </div>
        </div>

        <div class="kan-board">
            @foreach($columns as $col)
                @php
                    $items = $grouped[$col] ?? collect();
                    $b = $laneBreakdown[$col];
                @endphp
                <div class="kan-col kan-col--{{ $col }}"
                     data-column="{{ $col }}"
                     ondragover="event.preventDefault(); this.classList.add('is-dragover');"
                     ondragleave="this.classList.remove('is-dragover');"
                     ondrop="kanHandleDrop(event, '{{ $col }}', this);">

                    <div class="kan-col__head">
                        <span class="kan-col__title">{{ $colTitles[$col] }}</span>
                        <span class="kan-col__count">{{ $items->count() }}</span>
                    </div>

                    <div class="kan-col__meta">
                        <span class="kan-pip kan-pip--late {{ $b['late'] === 0 ? 'is-zero' : '' }}" data-pip="late" title="SLA dilanggar">
                            <span class="kan-pip__dot"></span>
                            <span class="kan-pip__lbl">Lewat</span>
                            <span class="kan-pip__n">{{ $b['late'] }}</span>
                        </span>
                        <span class="kan-pip kan-pip--risk {{ $b['risk'] === 0 ? 'is-zero' : '' }}" data-pip="risk" title="Hampir LEWAT">
                            <span class="kan-pip__dot"></span>
                            <span class="kan-pip__lbl">Kritikal</span>
                            <span class="kan-pip__n">{{ $b['risk'] }}</span>
                        </span>
                        <span class="kan-pip kan-pip--ok {{ $b['ok'] === 0 ? 'is-zero' : '' }}" data-pip="ok" title="Dalam SLA">
                            <span class="kan-pip__dot"></span>
                            <span class="kan-pip__lbl">On track</span>
                            <span class="kan-pip__n">{{ $b['ok'] }}</span>
                        </span>
                    </div>

                    <div class="kan-col__body">
                        @forelse($items as $app)
                            @php
                                $chip = $docChips[$app->doc_type] ?? ['code' => '?', 'cls' => ''];
                                $isDemo = in_array($app->reference_number, $anchorRefs ?? [], true);
                            @endphp
                            <a href="{{ route('system.tapisan.show', $app->reference_number) }}"
                               class="kan-card {{ $isDemo ? 'kan-card--demo' : '' }}"
                               draggable="true"
                               data-ref="{{ $app->reference_number }}"
                               data-sla="{{ $app->sla_state === 'breached' ? 'late' : ($app->sla_state === 'at_risk' ? 'risk' : 'ok') }}"
                               ondragstart="kanHandleDragStart(event, this);"
                               ondragend="this.classList.remove('is-dragging');"
                               onclick="if(window.__kanDragged){window.__kanDragged=false;event.preventDefault();}">

                                @if($isDemo)
                                    <span class="kan-card__demo">CONTOH · KLIK UNTUK MULA</span>
                                @endif

                                <div class="kan-card__top">
                                    <span class="kan-card__ref">{{ $app->reference_number }}</span>
                                    <span class="kan-card__chip {{ $chip['cls'] }}">{{ $chip['code'] }}</span>
                                </div>

                                <div>
                                    <div class="kan-card__name">{{ $app->applicant_name }}</div>
                                    <div class="kan-card__sub">{{ $app->applicant_ic }}</div>
                                </div>

                                <div class="kan-card__foot">
                                    <span class="kan-card__score">AI · {{ number_format(($app->ai_score ?? 0) * 100, 0) }}%</span>
                                    <span class="kan-card__sla {{ $slaClass($app) }}">{{ $slaLabel($app) }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="kan-col__empty">Kosong · tiada permohonan</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

<style>
    .kan-card--demo { border: 2px solid #FF6B35 !important; animation: kanBlink 1.2s ease-in-out infinite; }
    .kan-card--demo:hover { animation: none; box-shadow: 0 0 0 5px rgba(var(--orange-rgb),.30); }
    .kan-card__demo { display: block; background: #FF6B35; color: #fff; font-size: 9px; font-weight: 800; letter-spacing: .6px; text-align: center; padding: 3px 0; border-radius: 5px; margin-bottom: 8px; }
    @keyframes kanBlink {
        0%, 100% { box-shadow: 0 0 0 0 rgba(var(--orange-rgb),0); }
        50% { box-shadow: 0 0 0 5px rgba(var(--orange-rgb),.30); }
    }
</style>
<script>
    let kanDraggedRef = null;

    function kanHandleDragStart(e, el) {
        kanDraggedRef = el.dataset.ref;
        el.classList.add('is-dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', kanDraggedRef);
        window.__kanDragged = true;
    }

    function kanHandleDrop(e, targetCol, colEl) {
        e.preventDefault();
        colEl.classList.remove('is-dragover');
        const ref = kanDraggedRef || e.dataTransfer.getData('text/plain');
        if (!ref) return;

        const card = document.querySelector(`[data-ref="${ref}"]`);
        if (!card) return;

        const currentCol = card.closest('.kan-col')?.dataset.column;
        if (currentCol === targetCol) return;

        // Optimistic: move DOM
        const body = colEl.querySelector('.kan-col__body');
        const emptyState = body.querySelector('.kan-col__empty');
        if (emptyState) emptyState.remove();
        body.prepend(card);

        // Update counts (recompute all)
        const grand = { late: 0, risk: 0, ok: 0, total: 0 };
        document.querySelectorAll('.kan-col').forEach(col => {
            const cards = col.querySelectorAll('.kan-card');
            const n = cards.length;
            col.querySelector('.kan-col__count').textContent = n;
            grand.total += n;

            const tally = { late: 0, risk: 0, ok: 0 };
            cards.forEach(c => {
                const s = c.dataset.sla || 'ok';
                tally[s] = (tally[s] || 0) + 1;
            });
            ['late', 'risk', 'ok'].forEach(k => {
                grand[k] += tally[k];
                const pip = col.querySelector(`.kan-pip[data-pip="${k}"]`);
                if (pip) {
                    pip.querySelector('.kan-pip__n').textContent = tally[k];
                    pip.classList.toggle('is-zero', tally[k] === 0);
                }
            });
        });

        // KPI strip
        const sum = document.getElementById('kanSummary');
        if (sum) {
            ['total', 'late', 'risk', 'ok'].forEach(k => {
                const el = sum.querySelector(`[data-kpi="${k}"]`);
                if (el) el.textContent = grand[k];
            });
        }

        // Persist
        document.getElementById('kanMoveRef').value = ref;
        document.getElementById('kanMoveTo').value = targetCol;
        document.getElementById('kanMoveForm').submit();
    }
</script>
@endsection
