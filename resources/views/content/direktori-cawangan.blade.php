@extends('layouts.portal')

@section('title', 'Direktori Cawangan — Portal InPreS')

@php $nav = ''; @endphp

@section('main')
{{-- ============ Page header (pine) ============ --}}
<section class="pine-hero" id="main" style="padding:64px 0 56px;">
    <div class="container">
        <span class="eyebrow on-pine">
            Hubungi Kami
            <span class="e-dot orange"></span>
        </span>
        <h1 style="margin:14px 0 0;">
            Direktori <span class="accent">Cawangan<span class="signature-dot"></span></span>
        </h1>
        <p class="lede" style="max-width:760px;">
            Pejabat JPN di setiap negeri. Talian careline kebangsaan
            <b style="color:#fff;">03-8000 8000</b> beroperasi untuk semua urusan.
        </p>
    </div>
</section>

{{-- ============ Directory grid ============ --}}
<section class="section">
    <div class="container">
        <div class="svc-grid">
            @foreach ($cawangan as $c)
                <div class="svc" style="cursor:default;">
                    <span class="ico"><i data-lucide="map-pin"></i></span>
                    <h4>{{ $c['negeri'] }}</h4>
                    <p>{{ $c['alamat'] }}</p>
                    <span class="arrow" style="cursor:default;">
                        <i data-lucide="phone"></i> {{ $c['tel'] }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Rujukan rasmi --}}
        <div style="background:var(--ai-indigo-soft,#E6F8F6);border:1px solid var(--ai-border,#e5e7eb);border-radius:16px;padding:24px 28px;margin-top:32px;max-width:880px;">
            <p style="margin:0;color:var(--ai-text,#003D3A);font-size:15px;">
                <b>Prototaip.</b> Alamat penuh, waktu operasi dan direktori pejabat
                daerah tersedia di
                <a href="https://www.jpn.gov.my/my/hubungi-kami/direktori-cawangan" target="_blank" rel="noopener" style="color:var(--ai-indigo,#00B8A9);font-weight:600;">direktori rasmi JPN</a>.
            </p>
        </div>
    </div>
</section>

@push('head')
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
@endpush
@push('scripts')
<script>(function(){var t=function(){window.lucide&&lucide.createIcons();};t();setInterval(t,800);})();</script>
@endpush
@endsection
