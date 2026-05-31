@php
    use App\Models\Application;
@endphp

@extends('layouts.system', ['active' => $activeNav ?? 'tapisan', 'title' => 'Senarai Tapisan'])

@section('content')
@php
    $docFilter = request('doc');
    $statusFilter = request('status');
    $search = trim((string) request('q'));

    $baseQuery = Application::with('citizen');

    if (in_array($docFilter, ['birth', 'marriage', 'mykad'], true)) {
        $baseQuery->where('doc_type', $docFilter);
    }

    $counts = [
        'all'     => (clone $baseQuery)->whereNotIn('status', ['issued', 'rejected'])->count(),
        'received'       => (clone $baseQuery)->where('status', 'received')->count(),
        'verified'       => (clone $baseQuery)->where('status', 'verified')->count(),
        'officer_review' => (clone $baseQuery)->where('status', 'officer_review')->count(),
        'approved'       => (clone $baseQuery)->where('status', 'approved')->count(),
        'late'           => (clone $baseQuery)->where('sla_state', 'breached')->whereNotIn('status', ['issued', 'rejected'])->count(),
    ];

    $rows = (clone $baseQuery)
        ->when($statusFilter === 'late', fn ($q) => $q->where('sla_state', 'breached')->whereNotIn('status', ['issued', 'rejected']))
        ->when(in_array($statusFilter, ['received', 'verified', 'officer_review', 'approved'], true), fn ($q) => $q->where('status', $statusFilter))
        ->when($statusFilter === null, fn ($q) => $q->whereNotIn('status', ['issued', 'rejected']))
        ->when($search !== '', function ($q) use ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('reference_number', 'like', "%{$search}%")
                   ->orWhere('applicant_name', 'like', "%{$search}%")
                   ->orWhere('applicant_ic', 'like', "%{$search}%");
            });
        })
        ->orderByRaw("CASE status WHEN 'officer_review' THEN 1 WHEN 'verified' THEN 2 WHEN 'received' THEN 3 WHEN 'approved' THEN 4 WHEN 'issued' THEN 5 ELSE 6 END")
        ->orderBy('created_at')
        ->paginate(20)
        ->withQueryString();

    $docTitle = [
        'birth' => 'Sijil Kelahiran',
        'marriage' => 'Sijil Perkahwinan',
        'mykad' => 'MyKAD',
    ][$docFilter] ?? 'Semua Dokumen';
@endphp

<form method="POST" action="{{ route('system.tapisan.bulk-approve') }}" id="bulkForm">
@csrf

<div class="ws-page is-full">
    <div class="ws-page__main">

        {{-- ==== Head ==== --}}
        <div class="tap-head">
            <div>
                <h1 class="tap-head__title">Senarai Tapisan · {{ $docTitle }}</h1>
                <p class="tap-head__sub">
                    <strong>{{ $counts['all'] }} permohonan</strong> aktif
                    @if($counts['late'] > 0)
                        · <span class="late">{{ $counts['late'] }} lewat SLA</span>
                    @endif
                </p>
            </div>
            <div class="tap-head__cluster">
                <a class="tap-head__btn" href="{{ route('system.tapisan', request()->query()) }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                    Muat Semula
                </a>
            </div>
        </div>

        {{-- ==== Aliran strip ==== --}}
        <div class="tap-aliran">
            <span class="tap-aliran__eyebrow">ALIRAN</span>
            <span class="tap-aliran__chip"><span class="n">1</span> Diterima</span>
            <span class="tap-aliran__next">→</span>
            <span class="tap-aliran__chip"><span class="n">2</span> Disahkan</span>
            <span class="tap-aliran__next">→</span>
            <span class="tap-aliran__chip"><span class="n">3</span> Semakan</span>
            <span class="tap-aliran__next">→</span>
            <span class="tap-aliran__chip"><span class="n">4</span> Diluluskan</span>
            <span class="tap-aliran__next">→</span>
            <span class="tap-aliran__chip"><span class="n">5</span> Dikeluarkan</span>
            <a class="tap-aliran__link" href="{{ route('system.audit') }}">Lihat audit →</a>
        </div>

        {{-- ==== Filter chips ==== --}}
        <div class="tap-filters">
            @php
                $q = request()->except('status', 'page');
                $chips = [
                    ['key' => null,            'label' => 'Semua aktif',    'count' => $counts['all']],
                    ['key' => 'received',      'label' => 'Diterima',       'count' => $counts['received']],
                    ['key' => 'verified',      'label' => 'Disahkan',       'count' => $counts['verified']],
                    ['key' => 'officer_review','label' => 'Semakan Pegawai','count' => $counts['officer_review']],
                    ['key' => 'approved',      'label' => 'Diluluskan',     'count' => $counts['approved']],
                    ['key' => 'late',          'label' => 'Lewat SLA',      'count' => $counts['late']],
                ];
            @endphp
            @foreach($chips as $chip)
                <a href="{{ route('system.tapisan', array_filter(array_merge($q, ['status' => $chip['key']]))) }}"
                   class="tap-chip {{ $statusFilter == $chip['key'] ? 'is-active' : '' }}">
                    <span>{{ $chip['label'] }}</span>
                    <span class="tap-chip__count">{{ $chip['count'] }}</span>
                </a>
            @endforeach

            <div class="tap-filters__sep"></div>

            <div class="tap-search">
                <svg class="tap-search__icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="search" name="q" placeholder="Cari rujukan, nama, IC…" value="{{ $search }}"
                       onkeydown="if(event.key==='Enter'){event.preventDefault();const u=new URL(window.location.href);u.searchParams.set('q',this.value);u.searchParams.delete('page');window.location.href=u.toString();}">
            </div>
        </div>

        {{-- ==== Bulk action bar ==== --}}
        <div class="tap-bulk" id="bulkBar" style="display: none;">
            <span class="tap-bulk__count" id="bulkCount">0</span>
            <span>permohonan dipilih</span>
            <div class="tap-bulk__sp"></div>
            <button type="submit" class="tap-bulk__btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><path d="M20 6 9 17l-5-5"/></svg>
                Lulus Pilihan
            </button>
            <button type="button" class="tap-bulk__btn tap-bulk__btn--ghost" onclick="
                document.querySelectorAll('.tap-row__check').forEach(c => c.checked = false);
                updateBulkBar();
            ">Batal</button>
        </div>

        {{-- ==== Table ==== --}}
        <div class="tap-table">
            <div class="tap-table__head">
                <div><input type="checkbox" class="tap-row__check" id="selectAll" onchange="
                    document.querySelectorAll('.tap-row__check.row-check').forEach(c => c.checked = this.checked);
                    updateBulkBar();
                "></div>
                <div class="tap-table__th">Pemohon · Rujukan</div>
                <div class="tap-table__th">Dokumen</div>
                <div class="tap-table__th">No. KP</div>
                <div class="tap-table__th">Skor AI</div>
                <div class="tap-table__th">Status / SLA</div>
                <div class="tap-table__th">Tindakan</div>
            </div>

            @forelse($rows as $app)
                @php
                    $isLate = $app->sla_state === 'breached';
                    $isDone = in_array($app->status, ['approved', 'issued'], true);
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
                @endphp
                <div class="tap-row {{ $isLate ? 'is-late' : '' }} {{ $isDone ? 'is-done' : '' }}">
                    <div>
                        <input type="checkbox" name="ids[]" value="{{ $app->id }}" class="tap-row__check row-check"
                               {{ in_array($app->status, ['issued', 'rejected'], true) ? 'disabled' : '' }}
                               onchange="updateBulkBar()">
                    </div>
                    <div>
                        <div class="tap-row__title">{{ $app->applicant_name }}</div>
                        <div class="tap-row__sub">
                            <span class="mono">{{ $app->reference_number }}</span>
                            <span class="sep">·</span>
                            <span>{{ $app->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="tap-row__tujuan">{{ $docLabel }}</div>
                    <div class="tap-row__no">{{ $app->applicant_ic }}</div>
                    <div>
                        <span class="score">{{ number_format(($app->ai_score ?? 0) * 100, 0) }}%</span>
                    </div>
                    <div class="tap-row__due">
                        <span class="pill {{ $statusClass }}">{{ $statusLabel }}</span>
                        <div class="tap-row__due-sub">
                            @if($isLate)
                                <span style="color: var(--orange); font-weight: 600;">Lewat {{ $app->created_at->diffForHumans(null, true) }}</span>
                            @elseif($app->ai_eta)
                                ETA {{ $app->ai_eta->diffForHumans() }}
                            @else
                                {{ $app->created_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('system.tapisan.show', $app->reference_number) }}" class="tap-row__cta {{ $isLate ? 'tap-row__cta--orange' : 'tap-row__cta--pine' }}">
                            Semak
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="tap-empty">
                    <div class="tap-empty__title">Tiada permohonan padan<span class="dot"></span></div>
                    <div class="tap-empty__sub">Cuba ubah penapis atau kata carian.</div>
                </div>
            @endforelse

            @if($rows->lastPage() > 1)
                <div class="tap-page">
                    <div>
                        Halaman <strong style="color: var(--ink); font-family: var(--mono);">{{ $rows->currentPage() }}</strong>
                        daripada <strong style="color: var(--ink); font-family: var(--mono);">{{ $rows->lastPage() }}</strong>
                        · {{ $rows->total() }} hasil
                    </div>
                    <div class="tap-page__nav">
                        @if($rows->onFirstPage())
                            <button class="tap-page__btn" disabled>‹</button>
                        @else
                            <a class="tap-page__btn" href="{{ $rows->previousPageUrl() }}">‹</a>
                        @endif
                        @foreach(range(max(1, $rows->currentPage() - 2), min($rows->lastPage(), $rows->currentPage() + 2)) as $p)
                            <a class="tap-page__btn {{ $p === $rows->currentPage() ? 'is-active' : '' }}" href="{{ $rows->url($p) }}">{{ $p }}</a>
                        @endforeach
                        @if($rows->hasMorePages())
                            <a class="tap-page__btn" href="{{ $rows->nextPageUrl() }}">›</a>
                        @else
                            <button class="tap-page__btn" disabled>›</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
</form>

<script>
    function updateBulkBar() {
        const checks = document.querySelectorAll('.tap-row__check.row-check:checked');
        const bar = document.getElementById('bulkBar');
        const count = document.getElementById('bulkCount');
        if (checks.length > 0) {
            bar.style.display = 'flex';
            count.textContent = checks.length;
        } else {
            bar.style.display = 'none';
        }
    }
</script>
@endsection
