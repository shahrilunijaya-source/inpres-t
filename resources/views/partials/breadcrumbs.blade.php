{{-- Breadcrumbs (BM). Usage: @include('partials.breadcrumbs', ['current' => 'Mohon Dokumen']) --}}
@php($current = $current ?? '')
<nav aria-label="Breadcrumb" class="text-sm mb-6" style="color:var(--ai-text-mute);">
    <ol class="flex items-center gap-2 flex-wrap">
        <li><a href="{{ url('/') }}" class="hover:underline" style="color:var(--ai-text-dim);">Utama</a></li>
        @if ($current !== '')
            <li aria-hidden="true">/</li>
            <li aria-current="page" style="color:var(--ai-text);">{{ $current }}</li>
        @endif
    </ol>
</nav>
