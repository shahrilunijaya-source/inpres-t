@php
    // MyKad-style portrait. Ethnicity inferred from the name (Malay / Chinese / Indian)
    // so the face matches the person; gender + IC seed make it deterministic. Uses DiceBear
    // "personas" portrait avatars with a matched skin tone (privacy-safe, ethnic-sensitive).
    // Falls back to a silhouette if offline (onerror hides the img).
    $seed   = abs(crc32(($ic ?? '') . ($name ?? 'x')));
    $shape  = $shape ?? 'rect';
    $size   = $size ?? 76;
    $gender = ($gender ?? 'M');
    $radius = $shape === 'circle' ? '50%' : '10px';
    $w = $shape === 'circle' ? $size : round($size * 0.80);

    $nn = ' ' . mb_strtolower($name ?? '') . ' ';
    if (str_contains($nn, ' bin ') || str_contains($nn, ' binti ') || preg_match('/\b(muhammad|mohd|nur|siti|ahmad|abdul|nor|wan|amir|hamzah|farah|nadia|salleh|roslan|karim|hisham|faridah|aisyah|hakim|khairul|faizal|najwa|hajar)\b/u', $nn)) {
        $eth = 'malay';
    } elseif (preg_match('#a/[lp]#u', $nn) || preg_match('/\b(kumar|raj|nair|pillai|singh|kaur|devi|subramaniam|krishnan|maniam|gopal|ramasamy|govindasamy|anand|munusamy|rajan|suresh|priya|anitha|deepa|shanti|ramesh|vijay|dinesh|arvind|harpreet|simran|pillay|samy|thanabalan)\b/u', $nn)) {
        $eth = 'indian';
    } elseif (preg_match('/\b(tan|lim|wong|lee|ong|goh|teoh|chan|lau|yeoh|loh|chua|ng|chong|chen|khoo|yap|sim|toh|liew|leong|cheah|gan|low|chin|foo|chew|wei|hui|mei|chee|boon|kok|hwa|peng|seng|hao|jie|shan|ying|yen|qi|han|kiat|xin|pei|siew|lai|fen|hoon)\b/u', $nn)) {
        $eth = 'chinese';
    } else {
        $eth = 'default';
    }
    $skin = ['malay' => 'cb9e6e', 'indian' => '8d5524', 'chinese' => 'f2d3b1', 'default' => 'd8a47f'][$eth];
    $src = 'https://api.dicebear.com/9.x/personas/svg?seed=' . urlencode(($ic ?? $name) . $gender . $eth) . '&skinColor=' . $skin . '&backgroundColor=cdddf0';
    $silhouette = 'hsl(' . (($seed % 30) + 18) . ', 30%, 58%)';
@endphp
<div style="width: {{ $w }}px; height: {{ $size }}px; border-radius: {{ $radius }}; overflow: hidden; position: relative; flex-shrink: 0; border: 2px solid rgba(255,255,255,.9); box-shadow: 0 1px 4px rgba(0,0,0,.18); background: #cdddf0;">
    <svg viewBox="0 0 100 100" width="100%" height="100%" preserveAspectRatio="xMidYMax meet" style="display:block; position:absolute; inset:0;">
        <circle cx="50" cy="40" r="19" fill="{{ $silhouette }}" />
        <path d="M18 100 C18 73 35 62 50 62 C65 62 82 73 82 100 Z" fill="{{ $silhouette }}" />
    </svg>
    <img src="{{ $src }}" alt="" loading="lazy" referrerpolicy="no-referrer" onerror="this.style.display='none'"
         style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;" />
    @if($shape !== 'circle')
        <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(var(--ink-navy-rgb),.80); color:#fff; font-size:7px; letter-spacing:.6px; text-align:center; padding:2px 0; z-index:2;">FOTO · MyKad</div>
    @endif
</div>
