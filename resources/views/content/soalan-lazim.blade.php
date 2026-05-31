@extends('layouts.portal')

@section('title', 'Soalan Lazim — Portal InPreS')

@php $nav = ''; @endphp

@section('main')
{{-- ============ Page header (pine) ============ --}}
<section class="pine-hero" id="main" style="padding:64px 0 56px;">
    <div class="container">
        <span class="eyebrow on-pine">
            Bantuan
            <span class="e-dot orange"></span>
        </span>
        <h1 style="margin:14px 0 0;">
            Soalan <span class="accent">Lazim<span class="signature-dot"></span></span>
        </h1>
        <p class="lede" style="max-width:760px;">
            Jawapan ringkas kepada soalan yang kerap ditanya mengenai perkhidmatan
            pendaftaran JPN dan penggunaan portal ini.
        </p>
    </div>
</section>

{{-- ============ FAQ groups ============ --}}
<section class="section">
    <div class="container">
        @foreach ($faqs as $group => $items)
            <div style="margin-bottom:40px;">
                <span class="eyebrow">{{ $group }}<span class="e-dot"></span></span>
                <div class="faq-grid" style="margin-top:16px;">
                    @foreach ($items as $i => $item)
                        <details style="background:#fff;border:1px solid var(--ai-border,#e5e7eb);border-radius:14px;padding:0;overflow:hidden;" {{ $loop->parent->first && $i === 0 ? 'open' : '' }}>
                            <summary style="cursor:pointer;list-style:none;padding:18px 22px;font:600 17px/1.4 var(--font-sans);color:var(--ai-text,#003D3A);display:flex;justify-content:space-between;gap:16px;align-items:center;">
                                <span>{{ $item['q'] }}</span>
                                <i data-lucide="chevron-down" style="flex-shrink:0;width:20px;height:20px;color:var(--ai-indigo,#00B8A9);"></i>
                            </summary>
                            <div style="padding:0 22px 20px;color:var(--ai-text-dim,#4B5563);font-size:16px;line-height:1.7;">
                                {{ $item['a'] }}
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- CTA --}}
        <div style="background:var(--ai-indigo-soft,#E6F8F6);border:1px solid var(--ai-border,#e5e7eb);border-radius:16px;padding:28px 32px;display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:20px;">
            <div>
                <h3 style="margin:0 0 4px;font:700 22px/1.2 var(--font-sans);color:var(--ai-text,#003D3A);">Masih ada soalan?</h3>
                <p style="margin:0;color:var(--ai-text-dim,#4B5563);">Mulakan permohonan dalam talian atau semak status anda.</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ url('/apply') }}" class="btn btn-teal">Mulakan Permohonan</a>
                <a href="{{ route('direktori-cawangan') }}" class="btn btn-ghost">Hubungi JPN</a>
            </div>
        </div>
    </div>
</section>

<style>
summary::-webkit-details-marker{display:none;}
details[open] summary i[data-lucide="chevron-down"]{transform:rotate(180deg);}
summary i{transition:transform .2s;}
.faq-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:start;}
@media(max-width:880px){.faq-grid{grid-template-columns:1fr;}}
</style>

@push('head')
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
@endpush
@push('scripts')
<script>(function(){var t=function(){window.lucide&&lucide.createIcons();};t();setInterval(t,800);})();</script>
@endpush
@endsection
