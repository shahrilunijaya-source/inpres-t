{{--
    InPreS standard wordmark — matches officer topbar (system.blade.php).
    Self-contained inline styles so it renders identically regardless of stylesheet.
    Usage: @include('partials.brand', ['size' => '22px'])
--}}
@php($brandSize = $size ?? '20px')
@php($brandName = config('brand.name'))
<span style="font-weight:700; font-size:{{ $brandSize }}; letter-spacing:-0.01em; line-height:1; white-space:nowrap;">
    <span style="color:var(--teal,#00B8A9);">{{ mb_substr($brandName, 0, 1) }}</span>{{ mb_substr($brandName, 1) }}<span style="display:inline-block; width:0.34em; height:0.34em; border-radius:50%; background:var(--teal,#00B8A9); vertical-align:middle; margin-left:0.12em;"></span>
</span>
