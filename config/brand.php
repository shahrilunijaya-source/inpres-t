<?php

/*
|--------------------------------------------------------------------------
| Brand / White-label configuration
|--------------------------------------------------------------------------
|
| Single source of truth for every company-specific brand string + asset.
| This is the ONE config a clone (inpres-x / inpres-t) edits to re-brand —
| paired with resources/css/theme.css (which controls the look). Override
| any value via the matching BRAND_* key in .env.
|
| After editing this file or .env: run `php artisan config:clear`.
|
*/

$name     = env('BRAND_NAME', 'InPreS');                          // product short name
$agency   = env('BRAND_AGENCY', 'Jabatan Pendaftaran Negara');    // owning agency
$ministry = env('BRAND_MINISTRY', 'Kementerian Dalam Negeri');

return [

    // ---- Product names ----
    'name'        => $name,                 // "InPreS"
    'portal_name' => 'Portal ' . $name,     // "Portal InPreS"  (public)
    'system_name' => 'Sistem ' . $name,     // "Sistem InPreS"  (officer)

    // ---- Owning agency / ministry ----
    'agency'        => $agency,                              // "Jabatan Pendaftaran Negara"
    'agency_full'   => env('BRAND_AGENCY_FULL', $agency . ' Malaysia'),
    'ministry'      => $ministry,                            // "Kementerian Dalam Negeri"
    'ministry_full' => env('BRAND_MINISTRY_FULL', $ministry . ' Malaysia'),

    // ---- Assets (relative to public/) ----
    'logo'  => env('BRAND_LOGO', 'img/jpn-logo.png'),       // agency logo
    'crest' => env('BRAND_CREST', 'img/jata-negara.svg'),   // national crest / lockup mark

    // ---- Contact (footer) ----
    'address' => env('BRAND_ADDRESS', 'Kompleks Kementerian Dalam Negeri, No. 20 Persiaran Perdana, Presint 2, 62551 Putrajaya, Malaysia.'),
    'phone'   => env('BRAND_PHONE', '03-8000 8000'),

    // ---- Footer / version ----
    'agency_logo_alt' => 'Logo ' . $agency,
    'version'         => env('BRAND_VERSION', 'v0.1 · inpres-a'),  // instance label
];
