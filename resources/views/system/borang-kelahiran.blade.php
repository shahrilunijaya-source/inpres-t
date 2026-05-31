@extends('layouts.system', ['active' => 'borang', 'title' => 'Borang Pendaftaran Kelahiran'])

@section('content')
@php
    $b = $case['borang'] ?? [
        'form_no' => 'JPN.LM01', 'channel' => 'Portal MyJPN · dalam talian', 'submitted_at' => '—',
        'status' => 'Menunggu pengesahan biometrik di kaunter', 'baby_name' => $case['name'] ?? '—',
        'sex' => 'Perempuan', 'dob' => $case['dob'] ?? '—', 'born_time' => '—', 'born_place' => 'Hospital', 'weight' => '—',
        'informant' => ($case['mother']['name'] ?? '—') . ' (Ibu)',
    ];
    $mother = $case['mother'] ?? ['name' => '—', 'ic' => '—'];
    $father = $case['father'] ?? ['name' => '—', 'ic' => '—'];
    $hosp   = $case['hospital'] ?? ['name' => 'Hospital'];

    // dd/mm/yyyy formatter — tolerant of '—' / null / already-formatted values
    $fmt = function ($d) {
        if (empty($d) || $d === '—') return '—';
        try { return \Carbon\Carbon::parse($d)->format('d/m/Y'); }
        catch (\Throwable $e) { return $d; }
    };
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 20px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 01 · Borang {{ $b['form_no'] }} · Akta 299</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Borang Pendaftaran Kelahiran</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">{{ $b['channel'] }} · dihantar {{ $b['submitted_at'] }} · data klinikal pra-isi dari hospital</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 18px;">
        @foreach([['Saluran', 'DALAM TALIAN', 'diisi sendiri oleh ibu bapa', '#1E40AF'], ['Data Hospital', 'AUTO-ISI', 'FHIR R4 · KKM', '#15803D'], ['Status', 'KAUNTER', 'menunggu pengesahan biometrik', '#B45309'], ['Tempoh Daftar', '60 hari', 'Semenanjung · Akta 299 S.7', '#6366F1']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 14px 16px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 18px; font-weight: 700; color: {{ $kpi[3] }}; margin: 3px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Maklumat Bayi — auto-prefilled from hospital --}}
    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px; margin-bottom: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 style="margin: 0; font-size: 14px;">Bahagian A · Maklumat Bayi</h3>
            <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px; border-radius: 999px; font-size: 10.5px; font-weight: 700;">● Auto-isi dari {{ $hosp['name'] }}</span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px 24px;">
            @foreach([['Nama Bayi', $b['baby_name']], ['Jantina', $b['sex']], ['Tarikh Lahir', $fmt($b['dob'])], ['Masa Lahir', $b['born_time']], ['Berat Lahir', $b['weight']], ['No. Notifikasi (FHIR)', $hosp['notif'] ?? '—']] as $f)
                <div>
                    <div style="font-size: 10.5px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.4px;">{{ $f[0] }}</div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--ink-navy); margin-top: 2px; border-bottom: 1px solid #E5E7EB; padding-bottom: 6px;">{{ $f[1] }}</div>
                </div>
            @endforeach
        </div>
        {{-- Tempat lahir — hospital penuh + alamat (dari notifikasi hospital) --}}
        <div style="margin-top: 14px; display: grid; grid-template-columns: 1fr 2fr; gap: 14px 24px;">
            <div>
                <div style="font-size: 10.5px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.4px;">Tempat Lahir · Hospital</div>
                <div style="font-size: 14px; font-weight: 600; color: var(--ink-navy); margin-top: 2px; border-bottom: 1px solid #E5E7EB; padding-bottom: 6px;">{{ $hosp['name'] ?? $b['born_place'] }}</div>
            </div>
            <div>
                <div style="font-size: 10.5px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.4px;">Alamat Hospital</div>
                <div style="font-size: 14px; font-weight: 600; color: var(--ink-navy); margin-top: 2px; border-bottom: 1px solid #E5E7EB; padding-bottom: 6px;">{{ $hosp['address'] ?? '—' }}</div>
            </div>
        </div>
    </section>

    {{-- Maklumat Ibu Bapa — auto-isi penuh dari Pendaftaran Negara (rekod MyKad), bukan ditaip semula --}}
    @php
        $motherFields = [
            ['No. Dokumen Pengenalan', $mother['ic'] ?? '—'],
            ['Jenis Dokumen / Negara', ($mother['doc_type'] ?? 'MyKad') . ' · ' . ($mother['negara'] ?? 'Malaysia')],
            ['Nama Penuh', $mother['name'] ?? '—'],
            ['Tarikh Kelahiran', $fmt($mother['dob'] ?? null)],
            ['Alamat', $mother['alamat'] ?? ($case['address'] ?? '—')],
            ['Keturunan', $mother['keturunan'] ?? '—'],
            ['Pekerjaan', $mother['pekerjaan'] ?? '—'],
            ['Taraf Pemastautin', $mother['pemastautin'] ?? ($mother['warganegara'] ?? 'Warganegara')],
            ['Agama', $mother['agama'] ?? '—'],
            ['Taraf Perkahwinan', $mother['kahwin'] ?? '—'],
            ['Tarikh Perkahwinan', $fmt($mother['tarikh_kahwin'] ?? null)],
        ];
        $fatherFields = [
            ['No. Dokumen Pengenalan', $father['ic'] ?? '—'],
            ['Jenis Dokumen / Negara', ($father['doc_type'] ?? 'MyKad') . ' · ' . ($father['negara'] ?? 'Malaysia')],
            ['Nama Penuh', $father['name'] ?? '—'],
            ['Tarikh Kelahiran', $fmt($father['dob'] ?? null)],
            ['Keturunan', $father['keturunan'] ?? '—'],
            ['Pekerjaan', $father['pekerjaan'] ?? '—'],
            ['Taraf Pemastautin', $father['pemastautin'] ?? ($father['warganegara'] ?? 'Warganegara')],
            ['Agama', $father['agama'] ?? '—'],
        ];
    @endphp
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        @foreach([['Bahagian B · Maklumat Ibu', $motherFields, '#BE185D'], ['Bahagian C · Maklumat Bapa', $fatherFields, '#1E3A8A']] as $blk)
            <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h3 style="margin: 0; font-size: 14px; color: {{ $blk[2] }};">{{ $blk[0] }}</h3>
                    <span style="background: #DBEAFE; color: #1E40AF; padding: 3px 10px; border-radius: 999px; font-size: 10px; font-weight: 700;">● Auto-isi · rekod MyKad</span>
                </div>
                @foreach($blk[1] as $f)
                    <div style="display: flex; justify-content: space-between; gap: 14px; padding: 7px 0; border-bottom: 1px dashed #F1F5F9; font-size: 13px;">
                        <span style="color: #6B7280; white-space: nowrap;">{{ $f[0] }}</span>
                        <span style="font-weight: 600; color: var(--ink-navy); text-align: right;">{{ $f[1] }}</span>
                    </div>
                @endforeach
            </section>
        @endforeach
    </div>

    {{-- Informant + status --}}
    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px 20px; margin-bottom: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 10.5px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.4px;">Pemberitahu (Informant) · Akta 299 S.7</div>
                <div style="font-size: 14px; font-weight: 600; color: var(--ink-navy);">{{ $b['informant'] }}</div>
            </div>
            <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">✓ Borang lengkap</span>
        </div>
    </section>

    {{-- Next step: counter biometric --}}
    <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 240px;">
            <div style="font-weight: 700; color: #B45309; font-size: 14px;">Langkah seterusnya — Pengesahan Biometrik di Kaunter</div>
            <div style="font-size: 12.5px; color: #475569; margin-top: 2px;">Borang dihantar dalam talian. Ibu bapa hadir ke kaunter JPN untuk pengesahan biometrik (10 cap jari + muka) sebelum pendaftaran disahkan.</div>
        </div>
        <a href="{{ route('system.biometric', ['ref' => $case['reference']]) }}" style="text-decoration: none; background: var(--ink-navy); color: #fff; padding: 11px 20px; border-radius: 9px; font-size: 13px; font-weight: 700;">Teruskan ke Pengesahan Biometrik →</a>
    </div>
</div>
@endsection
