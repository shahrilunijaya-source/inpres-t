@extends('layouts.portal')

@section('title', 'Semak Status — InPreS')

@section('main')
    <div class="max-w-2xl mx-auto">
        <div class="badge badge-indigo mono mb-4">Phase 3 — HERO</div>
        <h1 class="text-3xl font-bold mb-4" style="color: var(--ai-text);">Semak Status Permohonan</h1>
        <p class="mb-8" style="color: var(--ai-text-dim);">
            Placeholder. Phase 3 akan membina Smart Tracker — timeline langsung, AI ETA, SLA badges.
        </p>

        @if (!empty($reference))
            <div class="glass-card-hi p-8">
                <div class="text-xs uppercase tracking-wider mb-2" style="color: var(--ai-text-mute);">Rujukan</div>
                <div class="mono text-2xl font-bold mb-6" style="color: var(--ai-text);">{{ $reference }}</div>

                <div class="space-y-3">
                    @foreach (['Diterima', 'Disahkan', 'Semakan Pegawai', 'Diluluskan', 'Dikeluarkan'] as $i => $stage)
                        <div class="glass-card p-4 flex items-center gap-4 {{ $i === 1 ? 'glow-ring pulse-glow' : '' }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mono text-sm"
                                 style="background: {{ $i <= 1 ? 'var(--ai-indigo-soft)' : 'var(--ai-bg-2)' }};
                                        color: {{ $i <= 1 ? 'var(--ai-indigo)' : 'var(--ai-text-mute)' }};
                                        border: 1px solid {{ $i <= 1 ? 'var(--ai-indigo)' : 'var(--ai-border)' }};">
                                {{ $i + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold" style="color: var(--ai-text);">{{ $stage }}</div>
                                <div class="text-xs" style="color: var(--ai-text-mute);">
                                    {{ $i <= 1 ? 'Selesai' : ($i === 2 ? 'Dalam proses' : 'Menunggu') }}
                                </div>
                            </div>
                            @if ($i <= 1)
                                <span class="badge badge-emerald">Sedia</span>
                            @elseif ($i === 2)
                                <span class="badge badge-amber">Aktif</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="glass-card-hi p-8">
                <label class="block text-sm font-medium mb-2" style="color: var(--ai-text-dim);">Nombor Rujukan</label>
                <div class="flex gap-3">
                    <input type="text" placeholder="APP-YYYYMMDD-XXXX"
                           class="flex-1 px-4 py-3 rounded-lg mono"
                           style="background: var(--ai-bg-2); border: 1px solid var(--ai-border); color: var(--ai-text);">
                    <button class="btn-gradient">Semak</button>
                </div>
                <div class="mt-6 text-sm" style="color: var(--ai-text-mute);">
                    Contoh: <a href="{{ url('/track/APP-20260528-0042') }}" class="mono" style="color: var(--ai-indigo);">APP-20260528-0042</a>
                </div>
            </div>
        @endif
    </div>
@endsection
