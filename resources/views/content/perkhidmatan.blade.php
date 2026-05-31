@extends('layouts.portal')

@section('title', 'Perkhidmatan — Portal InPreS')

@php $nav = 'perkhidmatan'; @endphp

@push('head')
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<style>
.svc.is-soon{cursor:pointer;opacity:0.5;text-align:left;width:100%;font-family:inherit;}
.svc.is-soon:hover{opacity:0.72;border-color:var(--gray-300,#d1d5db);transform:none;box-shadow:none;}
.svc.is-soon .ico{background:var(--gray-400,#9ca3af);}
.svc .soon-tag{display:inline-flex;align-items:center;gap:6px;font:600 11px/1 var(--font-sans);letter-spacing:0.06em;text-transform:uppercase;color:var(--gray-500,#6b7280);}
.svc .soon-tag svg{width:13px;height:13px;}
.svc-group{margin-bottom:48px;}
.svc-group:last-child{margin-bottom:0;}
</style>
@endpush

@section('main')
{{-- ============ Page header (pine) ============ --}}
<section class="pine-hero" id="main" style="padding:64px 0 56px;">
    <div class="container">
        <span class="eyebrow on-pine">
            Direktori Perkhidmatan
            <span class="e-dot orange"></span>
        </span>
        <h1 style="margin:14px 0 0;">Perkhidmatan InPreS</h1>
        <p class="lede" style="max-width:760px;">
            Senarai penuh modul Portal InPreS. Modul yang telah dibina boleh diakses
            terus; selebihnya berada dalam peta jalan pembangunan dan akan ditambah
            secara berperingkat.
        </p>
    </div>
</section>

{{-- ============ Body ============ --}}
<section class="section">
    <div class="container">
        @foreach ($groups as $group => $items)
            <div class="svc-group">
                <div class="section-head" style="margin-bottom:20px;">
                    <span class="eyebrow">{{ $group }}<span class="e-dot"></span></span>
                </div>
                <div class="svc-grid">
                    @foreach ($items as $svc)
                        @if ($svc['built'])
                            <a href="{{ url($svc['url']) }}" class="svc">
                                <span class="ico"><i data-lucide="{{ $svc['i'] }}"></i></span>
                                <h4>{{ $svc['t'] }}</h4>
                                <p>{{ $svc['d'] }}</p>
                                <span class="arrow">Buka <i data-lucide="arrow-right"></i></span>
                            </a>
                        @else
                            <button type="button" data-stub="{{ $svc['t'] }}" class="svc is-soon">
                                <span class="ico"><i data-lucide="{{ $svc['i'] }}"></i></span>
                                <h4>{{ $svc['t'] }}</h4>
                                <p>{{ $svc['d'] }}</p>
                                <span class="soon-tag"><i data-lucide="lock"></i> Belum tersedia</span>
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Prototype note --}}
        <div style="background:var(--ai-indigo-soft,#E6F8F6);border:1px solid var(--ai-border,#e5e7eb);border-radius:16px;padding:24px 28px;margin-top:8px;">
            <p style="margin:0;color:var(--ai-text,#003D3A);font-size:15px;">
                <b>Prototaip.</b> Modul bertanda <i>Belum tersedia</i> menunjukkan skop
                penuh sistem InPreS yang dirancang. Untuk urusan rasmi semasa, sila layari
                <a href="https://www.jpn.gov.my" target="_blank" rel="noopener" style="color:var(--ai-indigo,#00B8A9);font-weight:600;">jpn.gov.my</a>
                atau hubungi talian 03-8000 8000.
            </p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>(function(){var t=function(){window.lucide&&lucide.createIcons();};t();setInterval(t,800);})();</script>
@endpush
