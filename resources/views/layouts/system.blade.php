<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Ruang Pegawai' }} · {{ config('brand.system_name') }}</title>
    @vite(['resources/css/system.css'])
</head>
<body class="system">
@php
    $user = auth()->user();
    $active = $active ?? 'utama';
    $roleContext = match ($user?->role) {
        'admin'      => 'PENTADBIR · PUSAT SISTEM',
        'supervisor' => 'PENYELIA · PASUKAN PUTRAJAYA',
        default      => 'PEGAWAI · SALURAN UTAMA',
    };
    $context = $context ?? $roleContext;
    $expandedPermohonan = in_array($active, ['tapisan', 'birth', 'marriage', 'mykad', 'wizard', 'hospital', 'borang', 'kaveat', 'upacara', 'sijil', 'clms', 'biometric', 'lapor', 'kad'], true);
    $expandedRekod = in_array($active, ['familytree'], true);
    $expandedValidasi = in_array($active, ['abis', 'blockchain', 'katalog'], true);
    $expandedPentadbiran = in_array($active, ['kafka', 'agensi', 'mydigital', 'perkakasan'], true);

    // Role access matrix · LAMPIRAN A modules
    $role = $user?->role ?? 'officer';
    $isAdmin = $role === 'admin';
    $canOperational = in_array($role, ['officer', 'supervisor'], true); // SoD: admin tak proses permohonan
    $canAccess = [
        'biometric'  => in_array($role, ['officer','supervisor','admin'], true),
        'abis'       => in_array($role, ['officer','supervisor','admin'], true),
        'kaveat'     => in_array($role, ['officer','supervisor','admin'], true),
        'upacara'    => in_array($role, ['officer','supervisor'], true),
        'sijil'      => in_array($role, ['officer','supervisor'], true),
        'borang'     => in_array($role, ['officer','supervisor','admin'], true),
        'hospital'   => in_array($role, ['officer','supervisor','admin'], true),
        'familytree' => in_array($role, ['officer','supervisor'], true),
        'katalog'    => in_array($role, ['officer','supervisor','admin'], true),
        'clms'       => in_array($role, ['officer','supervisor','admin'], true),
        'lapor'      => in_array($role, ['officer','supervisor','admin'], true),
        'kad'        => in_array($role, ['officer','supervisor'], true),
        'blockchain' => in_array($role, ['supervisor','admin'], true),
        'agensi'     => in_array($role, ['supervisor','admin'], true),
        'perkakasan' => in_array($role, ['supervisor','admin'], true),
        'kafka'      => in_array($role, ['admin'], true),
        'mydigital'  => in_array($role, ['admin'], true),
    ];

    $initials = '';
    if ($user) {
        foreach (explode(' ', trim($user->name)) as $part) {
            if ($part !== '' && strlen($initials) < 2) $initials .= strtoupper(mb_substr($part, 0, 1));
        }
    }

    $pendingCount = \App\Models\Application::whereIn('status', ['received', 'verified', 'officer_review'])->count();
@endphp

<div class="ws-app" id="wsApp">

    {{-- ============ TOP BAR ============ --}}
    <div class="ws-topbar">
        <button type="button" class="ws-topbar__burger" onclick="document.getElementById('wsApp').classList.toggle('is-collapsed')" aria-label="Toggle sidebar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        <a class="ws-topbar__brand" href="{{ route('system.utama') }}">
            <span class="i">{{ mb_substr(config('brand.name'), 0, 1) }}</span>{{ mb_substr(config('brand.name'), 1) }}<span class="dot"></span>
        </a>

        <div class="ws-topbar__sep"></div>
        <div class="ws-topbar__context">{{ $context }}</div>

        <div class="ws-topbar__spacer"></div>

        <div class="ws-topbar__cluster" id="topbarCluster">
            <button type="button" class="ws-topbar__btn js-stub" data-module="Carian Pantas (Cmd+K)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <span style="font-family: var(--mono); font-size: 11.5px; opacity: 0.7;">⌘K</span>
            </button>

            <button type="button" class="ws-topbar__btn js-stub" data-module="Mesej & Notifikasi">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span class="badge badge--teal">3</span>
            </button>

            @if($canOperational ?? true)
            <a class="ws-topbar__btn" href="{{ route('system.tapisan') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                </svg>
                <span>Permohonan</span>
                @if($pendingCount > 0)
                    <span class="badge">{{ $pendingCount }}</span>
                @endif
            </a>
            @endif

            <button type="button" class="ws-topbar__user" onclick="document.getElementById('userDropdown').style.display = (document.getElementById('userDropdown').style.display === 'block' ? 'none' : 'block')">
                <span class="ws-topbar__avatar">{{ $initials ?: 'JP' }}</span>
                <span class="ws-topbar__name">
                    <span>{{ explode(' ', $user?->name ?? 'Pegawai JPN')[0] }}</span>
                    <span class="sub">{{ strtoupper($user?->role ?? 'OFFICER') }}</span>
                </span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            <div class="ws-dropdown" id="userDropdown" style="display:none;">
                <div class="ws-dropdown__head">
                    <span class="ws-topbar__avatar" style="width:36px;height:36px;font-size:13px;">{{ $initials ?: 'JP' }}</span>
                    <div class="ws-dropdown__head-text">
                        <span class="nm">{{ $user?->name }}</span>
                        <span class="mt">{{ $user?->email }}</span>
                    </div>
                </div>
                <div class="ws-dropdown__list">
                    <a class="ws-dropdown__user-item js-stub" data-module="Profil Pegawai" href="#">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Profil saya
                    </a>
                    <a class="ws-dropdown__user-item js-stub" data-module="Tukar Kata Laluan" href="#">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="15" r="4"/><path d="m10.8 12.2 8.2-8.2"/><path d="m17 5 3 3"/></svg>
                        Tukar kata laluan
                    </a>
                    <a class="ws-dropdown__user-item js-stub" data-module="Tetapan Akaun" href="#">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Tetapan akaun
                    </a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logoutFormDD').submit();" class="ws-dropdown__user-item ws-dropdown__user-item--danger" style="border-top: 1px solid var(--line);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Log keluar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ SIDEBAR ============ --}}
    <nav class="ws-sidebar" aria-label="Navigasi utama">

        <div class="ws-side-section">Ruang Kerja</div>

        {{-- ==== UTAMA ==== --}}
        <a href="{{ route('system.utama') }}" class="ws-side-top {{ $active === 'utama' ? 'is-active' : '' }}">
            <span class="ws-side-top__icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </span>
            <span class="ws-side-label">Utama</span>
        </a>

        @if($canOperational)
        {{-- ==== TUGASAN SAYA (Kanban) ==== --}}
        <a href="{{ route('system.kanban') }}" class="ws-side-top {{ $active === 'kanban' ? 'is-active' : '' }}">
            <span class="ws-side-top__icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="18" rx="1"/><rect x="14" y="3" width="7" height="12" rx="1"/></svg>
            </span>
            <span class="ws-side-label">Tugasan Saya</span>
        </a>

        {{-- ==== PERMOHONAN (working) ==== --}}
        <div class="ws-side-group">
            <button type="button" class="ws-side-group__head {{ $expandedPermohonan ? 'is-open' : '' }}" onclick="this.classList.toggle('is-open'); this.nextElementSibling.style.display = (this.classList.contains('is-open') ? 'flex' : 'none')">
                <span class="ws-side-top__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11Z"/></svg>
                </span>
                <span class="ws-side-label" style="flex:1;">Permohonan</span>
                <span class="ws-side-group__caret">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </button>
            <div class="ws-side-group__children" style="display: {{ $expandedPermohonan ? 'flex' : 'none' }};">
                <a href="{{ route('system.wizard') }}" class="ws-side-item {{ $active === 'wizard' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Pendaftaran Baru</span>
                </a>
                <a href="{{ route('system.tapisan') }}" class="ws-side-item {{ $active === 'tapisan' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Semua Permohonan</span>
                </a>
                <a href="{{ route('system.tapisan', ['doc' => 'birth']) }}" class="ws-side-item {{ $active === 'birth' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Sijil Kelahiran</span>
                </a>
                @if($canAccess['hospital'])
                <a href="{{ route('system.hospital') }}" class="ws-side-item {{ $active === 'hospital' ? 'is-active' : '' }}" style="padding-left: 36px;">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">↳ Hospital KKM Pra-Daftar</span>
                </a>
                @endif
                <a href="{{ route('system.tapisan', ['doc' => 'marriage']) }}" class="ws-side-item {{ $active === 'marriage' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Sijil Perkahwinan</span>
                </a>
                @if($canAccess['kaveat'])
                <a href="{{ route('system.kaveat') }}" class="ws-side-item {{ $active === 'kaveat' ? 'is-active' : '' }}" style="padding-left: 36px;">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">↳ Kaveat 21 Hari (S.22)</span>
                </a>
                @endif
                <a href="{{ route('system.tapisan', ['doc' => 'mykad']) }}" class="ws-side-item {{ $active === 'mykad' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">MyKAD</span>
                </a>
                @if($canAccess['clms'])
                <a href="{{ route('system.clms') }}" class="ws-side-item {{ $active === 'clms' ? 'is-active' : '' }}" style="padding-left: 36px;">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">↳ CLMS Baris Gilir Cetak</span>
                </a>
                @endif
                @if($canAccess['biometric'])
                <a href="{{ route('system.biometric') }}" class="ws-side-item {{ $active === 'biometric' ? 'is-active' : '' }}" style="padding-left: 36px;">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">↳ Penangkapan Biometrik</span>
                </a>
                @endif
                <a href="#" class="ws-side-item js-stub" data-module="Sijil Kematian">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Sijil Kematian</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Pengangkatan">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Pengangkatan</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Pertukaran Nama">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Pertukaran Nama</span>
                </a>
            </div>
        </div>
        @endif

        @if($canOperational)
        <div class="ws-side-section">Rekod & Sijil</div>

        {{-- ==== REKOD WARGANEGARA (stub group) ==== --}}
        <div class="ws-side-group">
            <button type="button" class="ws-side-group__head" onclick="this.classList.toggle('is-open'); this.nextElementSibling.style.display = (this.classList.contains('is-open') ? 'flex' : 'none')">
                <span class="ws-side-top__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <span class="ws-side-label" style="flex:1;">Rekod Warganegara</span>
                <span class="ws-side-group__caret">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </button>
            <div class="ws-side-group__children" style="display: {{ $expandedRekod ? 'flex' : 'none' }};">
                @if($canOperational)
                <a href="#" class="ws-side-item js-stub" data-module="Carian Warganegara">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Carian Warganegara</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Daftar Warganegara Baru">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Daftar Baru</span>
                </a>
                @endif
                @if($canAccess['familytree'])
                <a href="{{ route('system.familytree') }}" class="ws-side-item {{ $active === 'familytree' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Salasilah Family Tree</span>
                </a>
                @endif
                @if($canOperational)
                <a href="#" class="ws-side-item js-stub" data-module="Kemaskini Maklumat">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Kemaskini Maklumat</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Penyahdaftaran">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Penyahdaftaran</span>
                </a>
                @endif
            </div>
        </div>

        {{-- ==== PERAKUAN & SIJIL (stub group) ==== --}}
        <div class="ws-side-group">
            <button type="button" class="ws-side-group__head" onclick="this.classList.toggle('is-open'); this.nextElementSibling.style.display = (this.classList.contains('is-open') ? 'flex' : 'none')">
                <span class="ws-side-top__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </span>
                <span class="ws-side-label" style="flex:1;">Perakuan & Sijil</span>
                <span class="ws-side-group__caret">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </button>
            <div class="ws-side-group__children" style="display: none;">
                <a href="#" class="ws-side-item js-stub" data-module="Perakuan Kelahiran Baru">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Perakuan Baru</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Salinan Pendua">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Salinan Pendua</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Pengesahan Sijil">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Pengesahan Sijil</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Cetakan Pukal">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Cetakan Pukal</span>
                </a>
            </div>
        </div>

        {{-- ==== KEWARGANEGARAAN (stub group) ==== --}}
        <div class="ws-side-group">
            <button type="button" class="ws-side-group__head" onclick="this.classList.toggle('is-open'); this.nextElementSibling.style.display = (this.classList.contains('is-open') ? 'flex' : 'none')">
                <span class="ws-side-top__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </span>
                <span class="ws-side-label" style="flex:1;">Kewarganegaraan</span>
                <span class="ws-side-group__caret">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </button>
            <div class="ws-side-group__children" style="display: none;">
                <a href="#" class="ws-side-item js-stub" data-module="Permohonan Status Kewarganegaraan">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Permohonan Status</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Pengesahan Kewarganegaraan">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Pengesahan</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Pelepasan Kewarganegaraan">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Pelepasan</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Penerimaan Kewarganegaraan">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Penerimaan</span>
                </a>
            </div>
        </div>
        @endif

        <div class="ws-side-section">Pemantauan</div>

        {{-- ==== LAPORAN (mixed: Statistik working, rest stub) ==== --}}
        <div class="ws-side-group">
            <button type="button" class="ws-side-group__head {{ $active === 'statistik' ? 'is-open' : '' }}" onclick="this.classList.toggle('is-open'); this.nextElementSibling.style.display = (this.classList.contains('is-open') ? 'flex' : 'none')">
                <span class="ws-side-top__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
                </span>
                <span class="ws-side-label" style="flex:1;">Laporan</span>
                <span class="ws-side-group__caret">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </button>
            <div class="ws-side-group__children" style="display: {{ $active === 'statistik' ? 'flex' : 'none' }};">
                <a href="{{ route('system.statistik') }}" class="ws-side-item {{ $active === 'statistik' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Statistik & Analitik</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Statistik Bulanan PDF">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Statistik Bulanan</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Prestasi Pegawai">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Prestasi Pegawai</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Beban Kerja Pejabat">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Beban Kerja</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="SLA & KPI">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">SLA & KPI</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Eksport Data CSV">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Eksport CSV</span>
                </a>
            </div>
        </div>

        {{-- ==== PENGESAHAN & VALIDASI (Sistem Backbone LAMPIRAN A) ==== --}}
        @if($canAccess['abis'] || $canAccess['blockchain'] || $canAccess['katalog'])
        <div class="ws-side-group">
            <button type="button" class="ws-side-group__head {{ $expandedValidasi ? 'is-open' : '' }}" onclick="this.classList.toggle('is-open'); this.nextElementSibling.style.display = (this.classList.contains('is-open') ? 'flex' : 'none')">
                <span class="ws-side-top__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12 11 14 15 10"/><circle cx="12" cy="12" r="10"/></svg>
                </span>
                <span class="ws-side-label" style="flex:1;">Pengesahan & Validasi</span>
                <span class="ws-side-group__caret">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </button>
            <div class="ws-side-group__children" style="display: {{ $expandedValidasi ? 'flex' : 'none' }};">
                @if($canAccess['abis'])
                <a href="{{ route('system.abis') }}" class="ws-side-item {{ $active === 'abis' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span><span class="ws-side-label">ABIS 1:N Biometrik</span>
                </a>
                @endif
                @if($canAccess['blockchain'])
                <a href="{{ route('system.blockchain') }}" class="ws-side-item {{ $active === 'blockchain' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span><span class="ws-side-label">Hyperledger Fabric Lejer</span>
                </a>
                @endif
                @if($canAccess['katalog'])
                <a href="{{ route('system.katalog') }}" class="ws-side-item {{ $active === 'katalog' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span><span class="ws-side-label">Katalog Sub-Fungsi (63)</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        @if(in_array($role, ['supervisor', 'admin'], true))
        <div class="ws-side-section">Sistem</div>

        {{-- ==== PENTADBIRAN (Sistem ops + LAMPIRAN A infra) ==== --}}
        <div class="ws-side-group">
            <button type="button" class="ws-side-group__head {{ $expandedPentadbiran ? 'is-open' : '' }}" onclick="this.classList.toggle('is-open'); this.nextElementSibling.style.display = (this.classList.contains('is-open') ? 'flex' : 'none')">
                <span class="ws-side-top__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </span>
                <span class="ws-side-label" style="flex:1;">Pentadbiran</span>
                <span class="ws-side-group__caret">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </button>
            <div class="ws-side-group__children" style="display: {{ $expandedPentadbiran ? 'flex' : 'none' }};">
                <a href="#" class="ws-side-item js-stub" data-module="Pengurusan Pengguna">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Pengurusan Pengguna</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Peranan & Kebenaran">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Peranan & Kebenaran</span>
                </a>
                @if($canAccess['agensi'])
                <a href="{{ route('system.agensi') }}" class="ws-side-item {{ $active === 'agensi' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Integrasi Agensi (13)</span>
                </a>
                @endif
                @if($canAccess['kafka'])
                <a href="{{ route('system.kafka') }}" class="ws-side-item {{ $active === 'kafka' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Kafka Event Bus</span>
                </a>
                @endif
                @if($canAccess['mydigital'])
                <a href="{{ route('system.mydigital') }}" class="ws-side-item {{ $active === 'mydigital' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">MyDigital ID</span>
                </a>
                @endif
                @if($canAccess['perkakasan'])
                <a href="{{ route('system.perkakasan') }}" class="ws-side-item {{ $active === 'perkakasan' ? 'is-active' : '' }}">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Perkakasan Kaunter (9)</span>
                </a>
                @endif
                <a href="#" class="ws-side-item js-stub" data-module="Tetapan Sistem">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Tetapan Sistem</span>
                </a>
                <a href="#" class="ws-side-item js-stub" data-module="Sandaran & Pemulihan">
                    <span class="ws-side-item__bullet"></span>
                    <span class="ws-side-label">Sandaran & Pemulihan</span>
                </a>
            </div>
        </div>
        @endif

        <div class="ws-side-section">Sokongan</div>

        {{-- ==== MANUAL (stub top-level) ==== --}}
        <a href="#" class="ws-side-top js-stub" data-module="Manual Pengguna">
            <span class="ws-side-top__icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </span>
            <span class="ws-side-label">Manual Pengguna</span>
        </a>

        <a href="#" class="ws-side-top js-stub" data-module="Bantuan Helpdesk">
            <span class="ws-side-top__icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </span>
            <span class="ws-side-label">Bantuan</span>
        </a>

        {{-- ==== LOG AUDIT (working, anchored at bottom) ==== --}}
        <div class="ws-side-section">Jejak Audit</div>
        <a href="{{ route('system.audit') }}" class="ws-side-top {{ $active === 'audit' ? 'is-active' : '' }}">
            <span class="ws-side-top__icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <span class="ws-side-label">Log Audit</span>
        </a>

    </nav>

    {{-- ============ CONTENT ============ --}}
    <main class="ws-content">
        @if(isset($case) && !empty($case['threaded']))
            @include('system._case-rail')
        @endif
        {{ $slot ?? '' }}
        @yield('content')
    </main>
</div>

{{-- Hidden logout form (triggered from dropdown link) --}}
<form method="POST" action="{{ route('system.logout') }}" id="logoutFormDD" style="display:none;">@csrf</form>

{{-- ============ STUB ITEM DIMMING (prototaip: belum dibina) ============ --}}
<style>
    .ws-sidebar .ws-side-item.js-stub,
    .ws-sidebar .ws-side-top.js-stub {
        opacity: 0.38;
        transition: opacity 150ms ease;
    }
    .ws-sidebar .ws-side-item.js-stub:hover,
    .ws-sidebar .ws-side-top.js-stub:hover {
        opacity: 0.72;
    }
    .ws-sidebar .ws-side-item.js-stub .ws-side-label::after,
    .ws-sidebar .ws-side-top.js-stub .ws-side-label::after {
        content: '· v2';
        font-size: 9px;
        opacity: 0.7;
        margin-left: 6px;
        letter-spacing: 0.4px;
        font-weight: 500;
        font-family: ui-monospace, monospace;
    }
    /* Whole stub group header (group with no working children) — dim too */
    .ws-sidebar .ws-side-group:has(.ws-side-group__children > a:only-child.js-stub) .ws-side-group__head,
    .ws-sidebar .ws-side-group:has(.ws-side-group__children > a.js-stub:nth-last-child(1):nth-child(n+1)):not(:has(.ws-side-item:not(.js-stub))) .ws-side-group__head {
        opacity: 0.55;
    }
</style>

@if(session('toast'))
    <div class="toast" id="toast">
        <span class="dot"></span>
        <span>{{ session('toast') }}</span>
    </div>
    <script>setTimeout(() => document.getElementById('toast')?.remove(), 3600);</script>
@endif

{{-- ============ STUB MODAL ============ --}}
<div class="stub-modal" id="stubModal" role="dialog" aria-modal="true" aria-labelledby="stubModalTitle">
    <div class="stub-modal__card">
        <button type="button" class="stub-modal__close" id="stubModalClose" aria-label="Tutup">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <div class="stub-modal__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        </div>

        <div class="stub-modal__eyebrow">PROTOTAIP</div>
        <h2 class="stub-modal__title" id="stubModalTitle">Modul belum tersedia.<span class="dot"></span></h2>
        <p class="stub-modal__body">
            Modul ini wujud dalam reka bentuk penuh tetapi <strong>belum dilaksanakan</strong> dalam prototaip ini. Versi pengeluaran akan disertakan dengan modul lengkap.
        </p>

        <div class="stub-modal__module">
            <span><strong id="stubModalModule">—</strong></span>
            <span>NOT_IMPLEMENTED</span>
        </div>

        <div class="stub-modal__actions">
            <button type="button" class="stub-modal__btn stub-modal__btn--ghost" id="stubModalCancel">
                Kembali
            </button>
            <a href="{{ route('system.tapisan') }}" class="stub-modal__btn stub-modal__btn--primary">
                Buka Tapisan
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
            </a>
        </div>
    </div>
</div>

<script>
    // ===== User dropdown outside-click =====
    document.addEventListener('click', function (e) {
        const dd = document.getElementById('userDropdown');
        const cluster = document.getElementById('topbarCluster');
        if (dd && cluster && !cluster.contains(e.target)) dd.style.display = 'none';
    });

    // ===== Stub modal =====
    const stubModal = document.getElementById('stubModal');
    const stubModuleLabel = document.getElementById('stubModalModule');
    const stubModalClose = document.getElementById('stubModalClose');
    const stubModalCancel = document.getElementById('stubModalCancel');

    function openStubModal(moduleName) {
        stubModuleLabel.textContent = moduleName || 'Modul tidak diketahui';
        stubModal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeStubModal() {
        stubModal.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    // Intercept all .js-stub clicks
    document.addEventListener('click', function (e) {
        const target = e.target.closest('.js-stub');
        if (!target) return;
        e.preventDefault();
        e.stopPropagation();
        const moduleName = target.getAttribute('data-module') || target.textContent.trim();
        openStubModal(moduleName);
    });

    stubModalClose?.addEventListener('click', closeStubModal);
    stubModalCancel?.addEventListener('click', closeStubModal);
    stubModal?.addEventListener('click', function (e) {
        if (e.target === stubModal) closeStubModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && stubModal.classList.contains('is-open')) closeStubModal();
    });
</script>
</body>
</html>
