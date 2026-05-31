@extends('layouts.portal')

@section('title', 'Semak Status — Portal InPreS')

@push('head')
<style>
.form-split{display:grid;grid-template-columns:1.05fr 0.95fr;gap:48px;align-items:start;}
.form-aside{display:flex;flex-direction:column;gap:16px;}
.aside-eyebrow{margin-bottom:6px;}
.step-card{display:flex;gap:16px;align-items:flex-start;padding:20px 22px;background:#fff;border:1px solid var(--gray-200,#e5e7eb);border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,0.05);}
.step-num{flex:none;width:38px;height:38px;border-radius:11px;display:grid;place-items:center;font:700 15px/1 var(--font-mono);background:#e6f7f5;color:var(--teal-700,#007D72);}
.step-card h4{font:700 16px/1.2 var(--font-sans);color:var(--pine,#003d3a);margin:0 0 4px;}
.step-card p{font:400 14px/1.55 var(--font-sans);color:var(--gray-600,#4B5563);margin:0;}
.aside-note{display:flex;gap:13px;padding:20px 22px;border-radius:16px;background:linear-gradient(135deg,#1b3a6b,#0f2240);}
.aside-note .ico{flex:none;color:var(--teal,#00b8a9);margin-top:1px;}
.aside-note .t{font:700 14px/1.3 var(--font-sans);color:#fff;margin:0 0 4px;}
.aside-note .d{font:400 13px/1.55 var(--font-sans);color:rgba(255,255,255,0.72);margin:0;}
@media(max-width:880px){.form-split{grid-template-columns:1fr;gap:32px;}}
</style>
@endpush

@section('main')
{{-- ============ Page header (pine) ============ --}}
<section class="pine-hero" id="main" style="padding:64px 0 56px;">
    <div class="container">
        <span class="eyebrow on-pine">
            Semak Status
            <span class="e-dot orange"></span>
        </span>
        <h1 style="margin:14px 0 0;">Semak Status Permohonan</h1>
        <p class="lede" style="max-width:760px;">
            Masukkan nombor rujukan dan No. Kad Pengenalan permohonan anda untuk melihat
            status terkini dan anggaran ETA AI.
        </p>
    </div>
</section>

{{-- ============ Body ============ --}}
<section class="section">
    <div class="container">
        <div class="form-split">

            {{-- Left: form --}}
            <div class="glass-card-hi p-8">
                @if ($errors->any())
                    <div class="badge badge-rose mb-6 px-4 py-2">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('track.verify') }}" method="POST">
                    @csrf
                    <label class="block text-sm font-medium mb-2" style="color: var(--ai-text-dim);">Nombor Rujukan</label>
                    <input id="ref" type="text" name="reference" placeholder="APP-YYYYMMDD-XXXX"
                           value="{{ old('reference') }}"
                           pattern="APP-[0-9]{8}-[0-9]{4}"
                           required
                           class="w-full px-4 py-3 rounded-lg mono mb-4"
                           style="background: var(--ai-bg-2); border: 1px solid var(--ai-border); color: var(--ai-text);">

                    <label class="block text-sm font-medium mb-2" style="color: var(--ai-text-dim);">No. Kad Pengenalan</label>
                    <div class="flex gap-3">
                        <input id="ic" type="text" name="applicant_ic" placeholder="000000-00-0000"
                               value="{{ old('applicant_ic') }}"
                               pattern="[0-9]{6}-[0-9]{2}-[0-9]{4}"
                               required
                               class="flex-1 px-4 py-3 rounded-lg mono"
                               style="background: var(--ai-bg-2); border: 1px solid var(--ai-border); color: var(--ai-text);">
                        <button type="submit" class="btn-gradient btn-accent">Semak</button>
                    </div>
                </form>

                @php
                    $sampleApps = \App\Models\Application::with('citizen')
                        ->whereIn('status', ['officer_review', 'verified', 'received'])
                        ->limit(3)
                        ->get();
                @endphp

                @if ($sampleApps->isNotEmpty())
                    <div class="mt-8 pt-6 border-t" style="border-color: var(--ai-border);">
                        <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--ai-text-mute);">Contoh untuk demo</div>
                        <div class="text-xs mb-3" style="color: var(--ai-text-mute);">
                            Klik untuk isi rujukan + IC, kemudian tekan Semak.
                        </div>
                        <div class="space-y-2">
                            @foreach ($sampleApps as $app)
                                <button type="button"
                                        data-sample-ref="{{ $app->reference_number }}"
                                        data-sample-ic="{{ $app->applicant_ic }}"
                                        class="w-full text-left glass-card p-3 flex items-center justify-between bounce">
                                    <div>
                                        <div class="mono text-sm font-semibold" style="color: var(--ai-text);">
                                            {{ $app->reference_number }}
                                        </div>
                                        <div class="text-xs" style="color: var(--ai-text-dim);">
                                            {{ \App\Models\Application::DOC_LABELS[$app->doc_type] }} ·
                                            {{ \App\Models\Application::STAGE_LABELS[$app->status] }}
                                        </div>
                                    </div>
                                    <x-sla-badge :state="$app->sla_state" />
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: how it works --}}
            <div class="form-aside">
                <div class="aside-eyebrow">
                    <span class="eyebrow">Bagaimana Ia Berfungsi<span class="e-dot"></span></span>
                </div>

                <div class="step-card">
                    <div class="step-num">1</div>
                    <div>
                        <h4>Masukkan rujukan & IC</h4>
                        <p>Guna nombor rujukan <span class="mono">APP-YYYYMMDD-XXXX</span> dan No. Kad Pengenalan pemohon untuk pengesahan selamat.</p>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-num">2</div>
                    <div>
                        <h4>AI semak barisan & SLA</h4>
                        <p>Enjin AI mengira kedudukan permohonan anda dalam barisan dan beban kerja pegawai semasa.</p>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-num">3</div>
                    <div>
                        <h4>Lihat status & ETA</h4>
                        <p>Pantau lima peringkat tapisan dan anggaran tarikh siap yang dijana AI — dikemas kini secara langsung.</p>
                    </div>
                </div>

                <div class="aside-note">
                    <span class="ico"><i data-lucide="sparkles"></i></span>
                    <div>
                        <div class="t">Anggaran ETA berkuasa AI</div>
                        <div class="d">Setiap anggaran mengambil kira bilangan kes aktif dan corak pemprosesan sebenar JPN.</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@push('scripts')
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>(function(){var t=function(){window.lucide&&lucide.createIcons();};t();setInterval(t,800);})();</script>
<script>
document.querySelectorAll('[data-sample-ref]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('ref').value = btn.dataset.sampleRef;
        document.getElementById('ic').value = btn.dataset.sampleIc;
        document.getElementById('ic').focus();
    });
});
</script>
@endpush
@endsection
