{{-- Government footer — JPN/KDN identity, contact, links (BM). Portal only. --}}
<footer class="mt-auto border-t" style="border-color: var(--ai-border); background: var(--ai-bg-2);">
    <div class="max-w-6xl mx-auto px-6 py-10 grid md:grid-cols-3 gap-8 text-sm" style="color: var(--ai-text-dim);">
        {{-- Identity + contact --}}
        <div>
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ asset(config('brand.crest')) }}" alt="Jata Negara" class="h-10 w-auto" style="object-fit:contain;">
                <img src="{{ asset(config('brand.logo')) }}" alt="{{ config('brand.agency_logo_alt') }}" class="h-10 w-auto" style="object-fit:contain;">
                <div class="leading-tight">
                    <div class="font-bold" style="color:var(--ai-text);">{{ config('brand.agency') }}</div>
                    <div class="text-xs" style="color:var(--ai-text-mute);">{{ config('brand.ministry') }}</div>
                </div>
            </div>
            <p class="text-xs leading-relaxed" style="color:var(--ai-text-mute);">
                {{ config('brand.address') }}
            </p>
            <p class="text-xs mt-3" style="color:var(--ai-text-mute);">
                Talian Khidmat Pelanggan: <span class="mono" style="color:var(--ai-text-dim);">{{ config('brand.phone') }}</span><br>
                Waktu operasi: 8:30 — 17:00
            </p>
        </div>

        {{-- Portal links --}}
        <div>
            <div class="font-semibold mb-3" style="color: var(--ai-text);">Perkhidmatan</div>
            <ul class="space-y-2">
                <li><a href="{{ url('/apply') }}" class="hover:underline">Mohon Dokumen</a></li>
                <li><a href="{{ url('/track') }}" class="hover:underline">Semak Status Permohonan</a></li>
                <li><a href="{{ route('system.login') }}" class="hover:underline">Log Masuk Pegawai</a></li>
            </ul>
        </div>

        {{-- Government links --}}
        <div>
            <div class="font-semibold mb-3" style="color: var(--ai-text);">Pautan Kerajaan</div>
            <ul class="space-y-2">
                <li><a href="https://www.malaysia.gov.my" target="_blank" rel="noopener" class="hover:underline">MyGov — Portal Rasmi Kerajaan</a></li>
                <li><a href="https://www.moha.gov.my" target="_blank" rel="noopener" class="hover:underline">Kementerian Dalam Negeri</a></li>
                <li><a href="https://www.jpn.gov.my" target="_blank" rel="noopener" class="hover:underline">jpn.gov.my</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t" style="border-color: var(--ai-border);">
        <div class="max-w-6xl mx-auto px-6 py-4 text-xs flex flex-wrap gap-2 justify-between" style="color: var(--ai-text-mute);">
            <span>© {{ date('Y') }} {{ config('brand.agency_full') }}.
                <span class="badge badge-amber" style="text-transform:none;">Prototaip</span>
            </span>
            <span class="mono">{{ config('brand.version') }}</span>
        </div>
    </div>
</footer>
