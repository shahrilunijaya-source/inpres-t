@props([
    'application',
    'prediction',
])

@php
    use App\Models\Application;
    $stages = Application::STAGES;
    $stageLabels = Application::STAGE_LABELS;
    $currentIndex = $application->stageIndex();
    $isRejected = $application->status === 'rejected';

    $stageDescriptions = [
        'received' => 'Permohonan diterima dan disahkan.',
        'verified' => 'Dokumen sokongan disahkan secara automatik.',
        'officer_review' => 'Sedang disemak oleh pegawai JPN.',
        'approved' => 'Diluluskan. Sijil sedang dijana.',
        'issued' => 'Sijil tersedia untuk dimuat turun.',
    ];

    $confidencePct = (int) round($prediction['confidence'] * 100);
@endphp

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Timeline (2/3 width) --}}
    <div class="lg:col-span-2 glass-card-hi p-8 fade-up">
        <div class="flex items-start justify-between mb-8">
            <div>
                <div class="text-xs uppercase tracking-wider mb-1" style="color: var(--ai-text-mute);">Rujukan</div>
                <div class="mono text-2xl font-bold" style="color: var(--ai-text);">{{ $application->reference_number }}</div>
                <div class="text-sm mt-2" style="color: var(--ai-text-dim);">
                    {{ \App\Models\Application::DOC_LABELS[$application->doc_type] ?? $application->doc_type }} ·
                    <span class="mono">{{ $application->applicant_ic }}</span>
                </div>
            </div>
            <x-sla-badge :state="$application->sla_state" />
        </div>

        {{-- Vertical timeline --}}
        <ol class="relative" data-tracker>
            {{-- Connecting rail --}}
            <div class="absolute left-[19px] top-2 bottom-2 w-px" style="background: var(--ai-border);"></div>

            @foreach ($stages as $i => $stage)
                @php
                    $isDone = $i < $currentIndex || ($i === $currentIndex && in_array($application->status, ['issued', 'approved'], true) && ! $isRejected);
                    $isActive = $i === $currentIndex && ! $isRejected && ! in_array($application->status, ['issued'], true);
                    $isPending = $i > $currentIndex;
                @endphp

                <li class="relative pl-14 pb-6 last:pb-0 fade-up" style="animation-delay: {{ $i * 80 }}ms;">
                    {{-- Node --}}
                    <div class="absolute left-0 w-10 h-10 rounded-full flex items-center justify-center mono text-sm font-semibold {{ $isActive ? 'pulse-glow' : '' }}"
                         style="background: {{ $isDone || $isActive ? 'var(--ai-indigo-soft)' : 'var(--ai-bg-2)' }};
                                color: {{ $isDone ? 'var(--ai-emerald)' : ($isActive ? 'var(--ai-indigo)' : 'var(--ai-text-mute)') }};
                                border: 1.5px solid {{ $isDone ? 'var(--ai-emerald)' : ($isActive ? 'var(--ai-indigo)' : 'var(--ai-border)') }};
                                {{ $isActive ? 'box-shadow: 0 0 0 3px rgba(129,140,248,0.20), 0 0 10px rgba(129,140,248,0.70);' : '' }}">
                        @if ($isDone)
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>

                    {{-- Card --}}
                    <div class="glass-card p-4 {{ $isPending ? 'opacity-60' : '' }} bounce">
                        <div class="flex items-center justify-between mb-1">
                            <div class="font-semibold" style="color: var(--ai-text);">
                                {{ $stageLabels[$stage] ?? $stage }}
                            </div>
                            @if ($isActive)
                                <span class="badge badge-indigo badge-mono">Sedang Diproses</span>
                            @elseif ($isDone)
                                <span class="badge badge-emerald">Selesai</span>
                            @elseif ($isPending)
                                <span class="text-xs mono" style="color: var(--ai-text-mute);">Menunggu</span>
                            @endif
                        </div>
                        <div class="text-sm" style="color: var(--ai-text-dim);">
                            {{ $stageDescriptions[$stage] ?? '' }}
                        </div>
                        @if ($isActive)
                            <div class="mt-3 ai-sweep h-1 rounded-full"></div>
                        @endif
                    </div>
                </li>
            @endforeach

            @if ($isRejected)
                <li class="relative pl-14 fade-up">
                    <div class="absolute left-0 w-10 h-10 rounded-full flex items-center justify-center"
                         style="background: rgba(251,113,133,0.16);
                                color: var(--ai-rose);
                                border: 1.5px solid var(--ai-rose);
                                box-shadow: 0 0 0 3px rgba(251,113,133,0.20), 0 0 10px rgba(251,113,133,0.70);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="glass-card p-4">
                        <div class="flex items-center justify-between mb-1">
                            <div class="font-semibold" style="color: var(--ai-rose);">Permohonan Ditolak</div>
                            <x-sla-badge state="breached" />
                        </div>
                        <div class="text-sm" style="color: var(--ai-text-dim);">Sila hubungi helpdesk untuk maklum balas.</div>
                    </div>
                </li>
            @endif
        </ol>
    </div>

    {{-- AI Panel sidebar (1/3 width) --}}
    <div class="space-y-6 fade-up" style="animation-delay: 200ms;">
        {{-- ETA panel --}}
        <div class="glass-card-hi p-6">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" style="color: var(--ai-indigo);">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                </svg>
                <div class="text-xs uppercase tracking-wider font-semibold" style="color: var(--ai-text-dim);">AI Prediction</div>
            </div>

            <div class="mb-4">
                <div class="text-xs mb-1" style="color: var(--ai-text-mute);">Anggaran Siap</div>
                <div class="mono text-2xl font-bold" style="color: var(--ai-text);" data-eta>
                    {{ $prediction['eta']->format('d M Y, h:i A') }}
                </div>
                <div class="text-xs mt-1 mono" style="color: var(--ai-text-mute);" data-eta-relative>
                    {{ $prediction['eta']->diffForHumans() }}
                </div>
            </div>

            {{-- Confidence bar --}}
            <div class="mb-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs" style="color: var(--ai-text-dim);">Keyakinan</span>
                    <span class="mono text-sm font-bold" style="color: var(--ai-indigo);" data-confidence-text>
                        {{ $confidencePct }}%
                    </span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background: var(--ai-bg-2);">
                    <div class="h-full rounded-full bounce" data-confidence-bar
                         style="width: {{ $confidencePct }}%;
                                background: linear-gradient(90deg, var(--ai-indigo) 0%, var(--ai-violet) 100%);
                                box-shadow: 0 0 12px rgba(129,140,248,0.5);"></div>
                </div>
            </div>

            <p class="text-xs leading-relaxed" style="color: var(--ai-text-dim);" data-reason>
                {{ $prediction['reason'] }}
            </p>
        </div>

        {{-- Queue depth --}}
        <div class="glass-card p-6">
            <div class="text-xs uppercase tracking-wider mb-2" style="color: var(--ai-text-mute);">Beban Kerja Pegawai</div>
            <div class="flex items-baseline gap-2">
                <span class="mono text-3xl font-bold" style="color: var(--ai-cyan);" data-queue>{{ $prediction['queue_depth'] }}</span>
                <span class="text-sm" style="color: var(--ai-text-dim);">kes aktif</span>
            </div>
            @if ($application->assignedOfficer)
                <div class="mt-3 pt-3 border-t" style="border-color: var(--ai-border);">
                    <div class="text-xs" style="color: var(--ai-text-mute);">Pegawai bertugas</div>
                    <div class="text-sm font-medium mt-1" style="color: var(--ai-text);">
                        {{ $application->assignedOfficer->name }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Audit log --}}
        @if ($application->auditLogs->isNotEmpty())
            <div class="glass-card p-6">
                <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--ai-text-mute);">Aktiviti Terkini</div>
                <ol class="space-y-3 text-sm">
                    @foreach ($application->auditLogs->take(5) as $log)
                        <li class="flex items-start gap-3">
                            <span class="w-1.5 h-1.5 rounded-full mt-2 shrink-0" style="background: var(--ai-indigo);"></span>
                            <div class="flex-1 min-w-0">
                                <div style="color: var(--ai-text);">
                                    {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    @if ($log->payload['to'] ?? false)
                                        →
                                        <span style="color: var(--ai-indigo);">
                                            {{ \App\Models\Application::STAGE_LABELS[$log->payload['to']] ?? $log->payload['to'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs mono mt-0.5" style="color: var(--ai-text-mute);">
                                    {{ $log->created_at->diffForHumans() }}
                                    @if ($log->officer)
                                        · {{ $log->officer->name }}
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
</div>

@pushOnce('scripts')
<script>
(function() {
    const ref = @json($application->reference_number);
    const url = `/api/track/${ref}/status`;

    async function poll() {
        try {
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();

            const confEl = document.querySelector('[data-confidence-text]');
            const barEl = document.querySelector('[data-confidence-bar]');
            const queueEl = document.querySelector('[data-queue]');

            if (confEl) confEl.textContent = `${Math.round(data.confidence * 100)}%`;
            if (barEl) barEl.style.width = `${Math.round(data.confidence * 100)}%`;
            if (queueEl) queueEl.textContent = data.queue_depth;
        } catch (e) {
            /* swallow */
        }
    }

    // Reveal animation on load: confidence bar fills from 0, counters count up.
    function animateIn() {
        var bar = document.querySelector('[data-confidence-bar]');
        if (bar) {
            var target = bar.style.width;
            bar.style.transition = 'width 1.1s cubic-bezier(.22,1,.36,1)';
            bar.style.width = '0%';
            requestAnimationFrame(function () { requestAnimationFrame(function () { bar.style.width = target; }); });
        }
        var ct = document.querySelector('[data-confidence-text]');
        if (ct) {
            var ctt = parseInt(ct.textContent, 10) || 0, c = 0;
            var ci = setInterval(function () { c += 2; if (c >= ctt) { c = ctt; clearInterval(ci); } ct.textContent = c + '%'; }, 22);
        }
        var q = document.querySelector('[data-queue]');
        if (q) {
            var qt = parseInt(q.textContent, 10) || 0, n = 0, st = Math.max(1, Math.ceil(qt / 28));
            q.textContent = '0';
            var qi = setInterval(function () { n += st; if (n >= qt) { n = qt; clearInterval(qi); } q.textContent = n; }, 30);
        }
    }
    animateIn();

    setInterval(poll, 5000);
})();
</script>
@endPushOnce
