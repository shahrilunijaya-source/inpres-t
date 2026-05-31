@extends('layouts.app')

@section('title', $title ?? (config('brand.portal_name') . ' — ' . config('brand.agency')))

@push('head')
    @vite(['resources/css/portal.css'])
@endpush

@section('body')
    {{-- ============ Utility bar ============ --}}
    <div class="util-bar">
        <div class="container">
            <div class="util-left">
                <span>Portal Rasmi {{ config('brand.agency') }} · {{ config('brand.ministry_full') }}</span>
            </div>
            <div class="util-right">
                <a href="{{ url('/track') }}">Semak Status</a>
                <a href="{{ route('soalan-lazim') }}">Soalan Lazim</a>
                <a href="{{ route('direktori-cawangan') }}">Hubungi</a>
                <span class="sep">|</span>
                <span class="lang"><b>BM</b></span>
            </div>
        </div>
    </div>

    {{-- ============ Site header (co-brand) ============ --}}
    <header class="site-header">
        <a class="skip" href="#main">Langkau ke kandungan utama</a>
        <div class="container">
            <div class="co-brand">
                <a href="https://www.malaysia.gov.my" class="gov-lockup" aria-label="Jata Negara — Kerajaan Malaysia">
                    <img src="{{ asset(config('brand.crest')) }}" alt="Jata Negara — Lambang Kerajaan Malaysia" width="42" height="42" style="display:block;object-fit:contain;">
                </a>
                <a href="{{ url('/') }}" class="brand-lockup">
                    <img src="{{ asset(config('brand.logo')) }}" class="mark" alt="{{ config('brand.name') }}" width="40" height="40" style="object-fit:contain;">
                    <span class="brand-name">{{ config('brand.agency') }}</span>
                </a>
            </div>
            <nav class="site-nav" aria-label="Utama">
                <a href="{{ url('/') }}" class="{{ ($nav ?? '') === 'home' ? 'active' : '' }}">Utama</a>
                <a href="{{ url('/apply') }}" class="{{ ($nav ?? '') === 'apply' ? 'active' : '' }}">Mohon Dokumen</a>
                <a href="{{ url('/track') }}" class="{{ ($nav ?? '') === 'track' ? 'active' : '' }}">Semak Status</a>
                <a href="{{ url('/perkhidmatan') }}" class="{{ ($nav ?? '') === 'perkhidmatan' ? 'active' : '' }}">Perkhidmatan</a>
            </nav>
            <div class="header-cta">
                <a href="{{ url('/apply') }}" class="btn btn-ghost">Mohon</a>
                <a href="{{ route('system.login') }}" class="btn btn-pine">Log Masuk Pegawai</a>
            </div>
        </div>
    </header>

    {{-- ============ Main ============ --}}
    @yield('main')

    {{-- ============ Footer (dark pine) ============ --}}
    <footer class="site-footer" style="background-color:#001211;">
        <div class="container">
            <div class="grid">
                <div>
                    <div class="footer-brand">
                        <img src="{{ asset(config('brand.logo')) }}" class="mark" alt="{{ config('brand.name') }}" width="40" height="40" style="object-fit:contain;">
                        <span class="stack">
                            <span class="t1">{{ config('brand.portal_name') }}<span class="signature-dot"></span></span>
                            <span class="t2">{{ config('brand.agency') }}</span>
                        </span>
                    </div>
                    <p class="footer-desc">
                        Platform permohonan dokumen JPN dalam talian di bawah Kementerian Dalam Negeri.
                        Mohon, jejak, terima — tiada barisan, tiada borang kertas.
                    </p>
                    <p class="footer-desc" style="margin-top:12px;">
                        {{ config('brand.address') }} · Talian: {{ config('brand.phone') }}
                    </p>
                </div>
                <div>
                    <h5>Perkhidmatan</h5>
                    <ul>
                        <li><a href="{{ url('/apply') }}">Mohon Dokumen</a></li>
                        <li><a href="{{ url('/track') }}">Semak Status</a></li>
                        <li><a href="{{ url('/apply?type=birth') }}">Kelahiran</a></li>
                        <li><a href="{{ url('/apply?type=marriage') }}">Perkahwinan</a></li>
                        <li><a href="{{ url('/apply?type=mykad') }}">Kad Pengenalan</a></li>
                    </ul>
                </div>
                <div>
                    <h5>Maklumat</h5>
                    <ul>
                        <li><a href="{{ route('pengenalan') }}">Pengenalan</a></li>
                        <li><a href="{{ route('soalan-lazim') }}">Soalan Lazim</a></li>
                        <li><a href="{{ route('direktori-cawangan') }}">Direktori Cawangan</a></li>
                        <li><a href="{{ route('system.login') }}">Log Masuk Pegawai</a></li>
                    </ul>
                </div>
                <div>
                    <h5>Pautan Luar</h5>
                    <ul>
                        <li><a href="https://www.malaysia.gov.my" target="_blank" rel="noopener">MyGovernment</a></li>
                        <li><a href="https://www.moha.gov.my" target="_blank" rel="noopener">Kementerian Dalam Negeri</a></li>
                        <li><a href="https://www.jpn.gov.my" target="_blank" rel="noopener">jpn.gov.my</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="meta">
                    <span class="jata-footer" aria-hidden="true">
                        <img src="{{ asset(config('brand.crest')) }}" alt="" width="20" height="20" style="object-fit:contain;">
                    </span>
                    <span>© {{ date('Y') }} Kerajaan Malaysia · {{ config('brand.agency') }} · <b style="color:var(--orange);">Prototaip</b></span>
                </div>
                <div class="links">
                    <a href="#" data-stub="Dasar Privasi">Dasar Privasi</a>
                    <a href="#" data-stub="Dasar Keselamatan">Dasar Keselamatan</a>
                    <span class="t-mono" style="opacity:.6;">{{ config('brand.version') }}</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- Prototype stub modal (for modules not built in this prototype) --}}
    @include('partials.stub-modal')
@endsection
