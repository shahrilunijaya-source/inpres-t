@extends('layouts.system', ['active' => 'katalog', 'title' => 'Katalog Sub-Fungsi (63)'])

@section('content')
@php
$modules = [
    'Modul 01 · Kelahiran' => [
        'akta' => 'Akta 299 · Ordinan SM/SSL',
        'sections' => [
            'Pendaftaran (7)' => [
                ['Pra-Pendaftaran Hospital', 'active', 'Integrasi KKM real-time'],
                ['Pendaftaran Biasa', 'active', '60 hari SM / 14 hari SSL'],
                ['Pendaftaran Lambat', 'partial', 'Hari 15-42 SSL sahaja'],
                ['Pendaftaran Lewat', 'partial', '>60 hari SM / >42 hari SSL'],
                ['Kelahiran Mati (Stillbirth)', 'partial', 'Minggu ke-28 ke atas'],
                ['Kelahiran di Hedjaz', 'scope', 'Akta 152 S.4(1) · TBH'],
                ['Atas Kapal · Semula · Pembetulan', 'scope', 'Akta 299 S.27(3)'],
            ],
            'Sijil + MyKid (8)' => [
                ['Cabutan Daftar Kelahiran', 'active', 'Salinan sah'],
                ['Carian Daftar Kelahiran', 'active', 'Bayaran semakan RM5'],
                ['Cetakan Semula Sijil', 'active', 'JPN.LM05 / Borang XII'],
                ['Paparan Sijil Digital', 'active', 'QR verification awam'],
                ['MyKid Kali Pertama', 'active', 'Selepas daftar kelahiran'],
                ['Gantian MyKid', 'partial', 'Rosak / hilang'],
                ['Pertanyaan Cip · Pembatalan', 'scope', 'Lifecycle CLMS'],
                ['Salasilah Keluarga (Family Tree)', 'active', 'Auto-update hubungan'],
            ],
            'Pengurusan Rekod · KRITIKAL (7)' => [
                ['Pewujudan Rekod (Blockchain)', 'active', 'HL Fabric immutable'],
                ['Pengesahan Rekod', 'active', 'Crypto signature'],
                ['Pembatalan Rekod', 'active', 'Audit trail penuh'],
                ['Pelupusan Rekod', 'partial', 'Akta Arkib 2003'],
                ['Pengesahan Digital', 'active', 'QR code verification'],
                ['Carian Sejarah', 'active', 'Full-text + biometric'],
                ['Log Transaksi Immutable', 'active', 'Akta Keterangan S.90A'],
            ],
        ],
    ],
    'Modul 04 · Kad Pengenalan' => [
        'akta' => 'Akta 78 (Pendaftaran Negara 1959)',
        'sections' => [
            'Permohonan Kali Pertama (8)' => [
                ['MyKad (12 Tahun)', 'active', 'Biometrik wajib · ABIS 1:N'],
                ['MyPR', 'active', 'Penduduk Tetap'],
                ['MyKAS', 'partial', 'Warganegara Sementara'],
                ['MyPOCA', 'partial', 'Orang Asli · JAKOA'],
                ['Daftar Lewat', 'partial', '> 12 tahun · soal siasat'],
                ['Orang Baru Tiba', 'scope', 'Baru naturalisasi'],
                ['Kaunter Bergerak', 'scope', 'Offline mode · pelosok'],
                ['Kad Khas', 'scope', 'VVIP · diplomat'],
            ],
            'Operasi Kad (9)' => [
                ['Penukaran Kad', 'active', 'Tukar status / tukar nama'],
                ['Gantian Kad', 'active', 'Hilang / rosak'],
                ['Pengeluaran Kad', 'active', 'CLMS personalization'],
                ['Serahan Kad', 'active', 'Verifikasi biometrik'],
                ['Cetakan Resit', 'active', 'Untuk pemohon'],
                ['Pembatalan Permohonan', 'partial', 'Sebelum kad keluar'],
                ['Pelupusan Kad', 'scope', 'Selepas kematian'],
                ['Pemulangan ke JPN', 'scope', 'Bila diminta semula'],
                ['Penjadualan Cetakan', 'partial', 'CLMS queue'],
            ],
            'VV + ABIS · KRITIKAL (5)' => [
                ['Verifikasi + Validasi (VV)', 'active', 'Semakan permohonan'],
                ['ABIS 1:N Matching', 'active', '30M+ rekod · GPU H200'],
                ['Pewujudan Rekod', 'active', 'HL Fabric'],
                ['Pengesahan Rekod', 'active', 'Crypto signature'],
                ['Pembetulan + Panel Khas', 'partial', 'Jawatankuasa Pertimbangan'],
            ],
        ],
    ],
    'Modul 05 · Perkahwinan & Perceraian Sivil' => [
        'akta' => 'Akta 164 (1976)',
        'sections' => [
            'Pendaftaran Utama (7)' => [
                ['Pendaftaran Perkahwinan', 'active', 'Sivil · bukan Islam'],
                ['Pengurusan Kaveat', 'active', 'Tempoh 21 hari S.22'],
                ['Pendaftaran Perceraian', 'partial', 'Selepas Perintah Mahkamah'],
                ['Daftar Semula S.46B', 'scope', 'Sebelum Akta 164 1982'],
                ['MALAWAKIL Luar Negara', 'scope', 'Kedutaan / Konsulat MY'],
                ['Pengesahan Taraf', 'active', 'Dalam & luar negara'],
                ['Tribunal + Badan Pendamai', 'scope', 'S.106 kaunseling'],
            ],
            'Sijil + Cabutan (7)' => [
                ['Carian Daftar', 'active', 'Bayaran semakan'],
                ['Cabutan Daftar Perkahwinan', 'active', 'Salinan sah'],
                ['Sijil Perkahwinan', 'active', 'JPN.KC02'],
                ['Sijil Perceraian', 'partial', 'Decree absolute'],
                ['Paparan Digital', 'active', 'QR verification'],
                ['Cetakan Semula', 'active', 'Hilang / rosak'],
                ['Pelantikan Penolong Pendaftar', 'scope', 'Rumah ibadat'],
            ],
            'Pengurusan Rekod · KRITIKAL (5)' => [
                ['Pewujudan Rekod', 'active', 'HL Fabric'],
                ['Pengesahan Rekod', 'active', 'Crypto signature'],
                ['Pembatalan Permohonan', 'partial', 'Sebelum upacara'],
                ['Pelupusan Rekod', 'scope', 'Akta Arkib 2003'],
                ['Pembetulan + Panel Khas', 'partial', 'Fakta + Perkeranian'],
            ],
        ],
    ],
];
$all = collect($modules)->flatMap(fn($m) => collect($m['sections'])->flatMap(fn($s) => $s));
$active = $all->where(1, 'active')->count();
$partial = $all->where(1, 'partial')->count();
$scope = $all->where(1, 'scope')->count();
$total = $all->count();
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Para 2.2 · 3 Modul Pertama</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Katalog Sub-Fungsi · 63 Fungsi LAMPIRAN A</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Modul 01 Kelahiran (22) + Modul 04 Kad Pengenalan (22) + Modul 05 Perkahwinan (19)</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Jumlah Sub-Fungsi',$total,'3 modul','#1E40AF'],['Aktif',$active,round($active*100/$total).'% siap','#15803D'],['Separa',$partial,round($partial*100/$total).'% pembangunan','#B45309'],['Dalam Skop',$scope,round($scope*100/$total).'% fasa seterusnya','#475569']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 28px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    @foreach($modules as $modName => $modData)
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; margin-top: 18px; overflow: hidden;">
            <div style="padding: 16px 22px; background: linear-gradient(135deg, var(--ink-navy), #1E3A8A); color: #fff; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 15px;">{{ $modName }}</h3>
                <span style="font-size: 11px; opacity: 0.85; font-family: ui-monospace, monospace;">{{ $modData['akta'] }}</span>
            </div>
            <div style="padding: 18px 22px;">
                @foreach($modData['sections'] as $secName => $funcs)
                    <div style="margin-bottom: 18px;">
                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 10px;">{{ $secName }}</div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 10px;">
                            @foreach($funcs as $f)
                                @php($border = match($f[1]) { 'active' => '#15803D', 'partial' => '#B45309', default => '#CBD5E1' })
                                @php($bg = match($f[1]) { 'active' => '#DCFCE7', 'partial' => '#FEF3C7', default => '#F1F5F9' })
                                @php($fg = match($f[1]) { 'active' => '#15803D', 'partial' => '#B45309', default => '#475569' })
                                @php($label = match($f[1]) { 'active' => 'AKTIF', 'partial' => 'SEPARA', default => 'AKAN DATANG' })
                                <div style="border: 1px solid #E5E7EB; border-left: 3px solid {{ $border }}; border-radius: 8px; padding: 10px 12px; background: #fff;">
                                    <div style="display: flex; justify-content: space-between; gap: 8px; align-items: flex-start;">
                                        <div style="font-weight: 600; font-size: 13px; color: var(--ink-navy); line-height: 1.3;">{{ $f[0] }}</div>
                                        <span style="background: {{ $bg }}; color: {{ $fg }}; padding: 2px 7px; border-radius: 999px; font-size: 9.5px; font-weight: 700; white-space: nowrap;">{{ $label }}</span>
                                    </div>
                                    <div style="color: #6B7280; font-size: 11px; margin-top: 4px; line-height: 1.4;">{{ $f[2] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi LAMPIRAN A Para 2.2:</strong>
        <span style="color: #475569; font-size: 13px;"> Tender menetapkan 63 sub-fungsi merentasi 3 modul ini (22 + 22 + 19). Setiap sub-fungsi ada justifikasi akta, antara muka pengguna, integrasi modul/agensi, dan log transaksi blockchain. Pelan berfasa: 31 AKTIF · 18 SEPARA · 14 AKAN DATANG.</span>
    </div>
</div>
@endsection
