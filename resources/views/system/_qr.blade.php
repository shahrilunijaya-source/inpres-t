@php
    // Believable QR matrix — 3 finder patterns + deterministic data modules from the seed.
    // Visual stand-in for a crypto-signed verification code (not scannable).
    $seedStr = $seed ?? 'INSPRES';
    $px = $px ?? 150;
    $n = 25;
    $src = '';
    for ($i = 0; $i < 60; $i++) { $src .= md5($seedStr . $i); }
    $finder = function (int $r, int $c) use ($n) {
        foreach ([[0, 0], [0, $n - 7], [$n - 7, 0]] as $f) {
            if ($r >= $f[0] && $r < $f[0] + 7 && $c >= $f[1] && $c < $f[1] + 7) {
                $ir = $r - $f[0]; $ic = $c - $f[1];
                $ring = ($ir === 0 || $ir === 6 || $ic === 0 || $ic === 6);
                $inner = ($ir >= 2 && $ir <= 4 && $ic >= 2 && $ic <= 4);
                return $ring || $inner;
            }
        }
        return null;
    };
@endphp
<div style="width: {{ $px }}px; height: {{ $px }}px; background: #fff; padding: 8px; border-radius: 8px; border: 1px solid #E5E7EB; box-shadow: 0 1px 3px rgba(0,0,0,.1);">
    <div style="width: 100%; height: 100%; display: grid; grid-template-columns: repeat({{ $n }}, 1fr); grid-template-rows: repeat({{ $n }}, 1fr);">
        @for($r = 0; $r < $n; $r++)
            @for($c = 0; $c < $n; $c++)
                @php($f = $finder($r, $c))
                @php($on = $f !== null ? $f : (hexdec($src[($r * $n + $c) % strlen($src)]) % 2 === 0))
                <div style="background: {{ $on ? 'var(--ink-navy)' : '#fff' }};"></div>
            @endfor
        @endfor
    </div>
</div>
