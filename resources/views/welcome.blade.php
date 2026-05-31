@extends('layouts.portal')

@section('title', config('brand.portal_name') . ' — Mohon Dokumen Dalam Talian')

@php $nav = 'home'; @endphp

@push('head')
<style>
.hero-dash .row .info{display:flex;align-items:center;gap:11px;}
.ev-dot{flex:none;width:8px;height:8px;border-radius:999px;background:var(--teal,#00b8a9);box-shadow:0 0 0 4px rgba(var(--brand-rgb),0.16);}
.hero-dash .glass .row .t-meta{font-variant-numeric:tabular-nums;white-space:nowrap;}
.hero-dash .stats-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
</style>
@endpush

@section('main')
{{-- ============ Pine hero ============ --}}
<section class="pine-hero" id="main">
    <div class="container">
        <div class="grid">
            <div>
                <span class="eyebrow on-pine">
                    {{ config('brand.agency') }} · {{ config('brand.portal_name') }}
                    <span class="e-dot orange"></span>
                </span>
                <h1>
                    Mohon dokumen JPN.<br />
                    <span class="accent">Tiada barisan. Tiada borang kertas.<span class="signature-dot"></span></span>
                </h1>
                <p class="lede">
                    Daftar kelahiran, perkahwinan, atau MyKad secara dalam talian.
                    Imbas IC dengan AI untuk auto-isi medan utama, jejak status secara langsung.
                </p>
                <div class="pine-hero-actions">
                    <a href="{{ url('/apply') }}" class="btn btn-teal btn-lg">
                        Mulakan Permohonan <i data-lucide="arrow-right"></i>
                    </a>
                    <a href="{{ url('/track') }}" class="btn btn-ghost-light btn-lg">
                        Semak Status Permohonan
                    </a>
                </div>
                <div class="pine-hero-trust">
                    <span class="t-item"><i data-lucide="shield-check"></i> Disahkan KDN</span>
                    <span class="t-item"><i data-lucide="zap"></i> Auto-isi AI</span>
                    <span class="t-item"><i data-lucide="lock"></i> Dilindungi PDPA 2010</span>
                </div>
            </div>

            {{-- Live activity panel (anonymized — no refs, no doc types, no PII) --}}
            <div class="hero-dash">
                <div class="glass">
                    <div class="glass-hd">
                        <span class="ttl">Aktiviti Sistem</span>
                        <span class="live">Live</span>
                    </div>
                    @forelse ($events as $ev)
                        <div class="row">
                            <div class="info">
                                <span class="ev-dot"></span>
                                <div class="t-title">{{ $ev['label'] }}</div>
                            </div>
                            <span class="t-meta">{{ $ev['ago'] }}</span>
                        </div>
                    @empty
                        <div class="row"><div class="info"><div>Sistem sedia menerima permohonan.</div></div></div>
                    @endforelse
                </div>
                <div class="stats-row">
                    <div class="stat-mini">
                        <div class="lbl">Sedang diproses</div>
                        <div class="num">{{ number_format($statInProgress ?? 0) }}</div>
                        <div class="sub">oleh pegawai JPN</div>
                    </div>
                    <div class="stat-mini">
                        <div class="lbl">Telah selesai</div>
                        <div class="num orange">{{ number_format($statProcessed ?? 0) }}</div>
                        <div class="sub">diluluskan / dikeluarkan</div>
                    </div>
                    <div class="stat-mini">
                        <div class="lbl">Diproses hari ini</div>
                        <div class="num">{{ number_format($statToday ?? 0) }}</div>
                        <div class="sub">permohonan baharu</div>
                    </div>
                    <div class="stat-mini">
                        <div class="lbl">SLA dipenuhi</div>
                        <div class="num orange">{{ $statSlaMet ?? 0 }}%</div>
                        <div class="sub">dalam tempoh sasaran</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ Mohon dalam talian (doc types) ============ --}}
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Mohon Dalam Talian<span class="e-dot"></span></span>
            <h2 style="margin:8px 0 0;font:700 44px/1.05 var(--font-sans);letter-spacing:-0.03em;">
                Tiga dokumen. Satu portal.
            </h2>
        </div>
        <div class="svc-grid">
            @foreach ([
                ['type'=>'birth','i'=>'baby','t'=>'Sijil Kelahiran','p'=>'Untuk bayi baharu lahir atau salinan sijil yang hilang.','sla'=>'5 hari bekerja'],
                ['type'=>'marriage','i'=>'heart','t'=>'Sijil Perkahwinan','p'=>'Pendaftaran perkahwinan atau pengeluaran semula sijil.','sla'=>'7 hari bekerja'],
                ['type'=>'mykad','i'=>'credit-card','t'=>'MyKad','p'=>'Pembaharuan, penggantian, atau permohonan kali pertama.','sla'=>'14 hari bekerja'],
            ] as $doc)
                <a href="{{ url('/apply?type='.$doc['type']) }}" class="svc">
                    <span class="ico"><i data-lucide="{{ $doc['i'] }}"></i></span>
                    <h4>{{ $doc['t'] }}</h4>
                    <p>{{ $doc['p'] }}</p>
                    <span class="arrow">Mohon · {{ $doc['sla'] }} <i data-lucide="arrow-right"></i></span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ Live stats ============ --}}
<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="section-head-row" style="margin-bottom:32px;">
            <div>
                <span class="eyebrow">Statistik Perkhidmatan<span class="e-dot orange"></span></span>
                <h2 style="margin:8px 0 0;font:700 44px/1.05 var(--font-sans);letter-spacing:-0.03em;">
                    Sistem yang sentiasa hidup.
                </h2>
            </div>
            <a href="{{ url('/track') }}" class="btn btn-ghost">
                Semak permohonan anda <i data-lucide="arrow-right"></i>
            </a>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="lbl">Sedang diproses</div>
                <div class="num">{{ number_format($statInProgress ?? 0) }}</div>
                <div class="sub">dalam tapisan pegawai</div>
            </div>
            <div class="stat-card">
                <div class="lbl">Telah selesai</div>
                <div class="num">{{ number_format($statProcessed ?? 0) }}</div>
                <div class="sub">diluluskan / dikeluarkan</div>
            </div>
            <div class="stat-card">
                <div class="lbl">Peringkat tapisan</div>
                <div class="num">{{ $statStages ?? 5 }}</div>
                <div class="sub">bagi setiap permohonan</div>
            </div>
            <div class="stat-card">
                <div class="lbl">Akses</div>
                <div class="num">24/7</div>
                <div class="sub">semak status bila-bila masa</div>
                <div class="delta">Tidak terikat waktu pejabat</div>
            </div>
        </div>
    </div>
</section>

{{-- ============ Perkhidmatan JPN (real services) ============ --}}
<section class="section" style="padding-top:0;background:var(--paper);">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Perkhidmatan JPN<span class="e-dot"></span></span>
            <h2 style="margin:8px 0 0;font:700 44px/1.05 var(--font-sans);letter-spacing:-0.03em;">
                Apa yang anda perlukan?
            </h2>
        </div>
        <div class="svc-grid">
            @foreach ([
                ['i'=>'baby','t'=>'Kelahiran','p'=>'Pendaftaran kelahiran dan sijil kelahiran.','h'=>url('/apply?type=birth')],
                ['i'=>'credit-card','t'=>'Kad Pengenalan','p'=>'MyKad, MyKid, MyTentera dan penggantian.','h'=>url('/apply?type=mykad')],
                ['i'=>'heart','t'=>'Perkahwinan','p'=>'Pendaftaran perkahwinan bukan Islam.','h'=>url('/apply?type=marriage')],
                ['i'=>'scroll-text','t'=>'Perceraian','p'=>'Pendaftaran perceraian dan perakuan.','stub'=>'Perceraian'],
                ['i'=>'file-text','t'=>'Kematian','p'=>'Pendaftaran kematian dan sijil kematian.','stub'=>'Kematian'],
                ['i'=>'users','t'=>'Pengangkatan','p'=>'Pendaftaran anak angkat.','stub'=>'Pengangkatan'],
                ['i'=>'flag','t'=>'Warganegara','p'=>'Kewarganegaraan dan taraf permastautin.','stub'=>'Warganegara'],
                ['i'=>'search','t'=>'Semak Status','p'=>'Jejak permohonan dalam talian anda.','h'=>url('/track')],
            ] as $s)
                @if (isset($s['stub']))
                    <a href="#" data-stub="{{ $s['stub'] }}" class="svc">
                        <span class="ico"><i data-lucide="{{ $s['i'] }}"></i></span>
                        <h4>{{ $s['t'] }}</h4>
                        <p>{{ $s['p'] }}</p>
                        <span class="arrow">Belum tersedia <i data-lucide="lock"></i></span>
                    </a>
                @else
                    <a href="{{ $s['h'] }}" class="svc">
                        <span class="ico"><i data-lucide="{{ $s['i'] }}"></i></span>
                        <h4>{{ $s['t'] }}</h4>
                        <p>{{ $s['p'] }}</p>
                        <span class="arrow">Buka <i data-lucide="arrow-right"></i></span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</section>

@push('head')
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
@endpush

@push('scripts')
<script>
  (function(){ var t=function(){window.lucide&&lucide.createIcons();};t();setInterval(t,800);})();
</script>
@endpush
@endsection
