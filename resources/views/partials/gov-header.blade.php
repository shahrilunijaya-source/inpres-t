{{-- Government header — JPN identity lockup + nav (BM). Portal only. --}}
<header class="sticky top-0 z-40 backdrop-blur-xl"
        style="background: var(--ai-glass); border-bottom: 1px solid var(--ai-border);">
    <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between gap-4">
        <a href="{{ url('/') }}" class="flex items-center gap-3 min-w-0" style="color: var(--ai-text);">
            {{-- Mark 1: National crest --}}
            <img src="{{ asset(config('brand.crest')) }}" alt="Jata Negara"
                 class="h-11 w-auto shrink-0" style="object-fit:contain;">
            {{-- Mark 2: Agency logo --}}
            <img src="{{ asset(config('brand.logo')) }}" alt="{{ config('brand.agency_logo_alt') }}"
                 class="h-11 w-auto shrink-0" style="object-fit:contain;">
            {{-- divider --}}
            <span class="hidden sm:block h-9 w-px shrink-0" style="background:var(--ai-border);"></span>
            <span class="leading-tight min-w-0">
                <span class="block font-bold tracking-tight truncate" style="font-size:1.02rem; color:var(--ai-text);">
                    {{ config('brand.agency') }}
                </span>
                <span class="block text-xs truncate" style="color:var(--ai-text-mute);">
                    {{ config('brand.ministry') }}
                </span>
            </span>
            <span class="hidden md:inline-flex items-center px-2 py-0.5 ml-1 rounded-md text-xs font-semibold shrink-0"
                  style="background:var(--ai-indigo-soft); color:var(--ai-indigo);">
                {{ config('brand.portal_name') }}
            </span>
        </a>

        <nav class="flex items-center gap-5 text-sm shrink-0">
            <a href="{{ url('/') }}" class="hidden md:inline hover:underline" style="color: var(--ai-text-dim);">Utama</a>
            <a href="{{ url('/apply') }}" class="hidden md:inline hover:underline" style="color: var(--ai-text-dim);">Mohon</a>
            <a href="{{ url('/track') }}" class="hidden md:inline hover:underline" style="color: var(--ai-text-dim);">Semak Status</a>
            <a href="{{ route('system.login') }}" class="btn-gradient text-sm">Log Masuk Pegawai</a>
        </nav>
    </div>
</header>
