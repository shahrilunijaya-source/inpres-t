@extends('layouts.system', ['active' => 'sijil', 'title' => 'Sijil Kelahiran'])

@section('content')
@php
    $s = $case['sijil'] ?? ['cert_no' => 'JPN.LM05-' . ($case['reference'] ?? ''), 'reg_no' => 'KLH-' . ($case['reference'] ?? ''), 'mykid_no' => 'MYKID-' . ($case['reference'] ?? ''), 'issued_date' => '—', 'block' => 1925014, 'tx_hash' => '0x000…', 'ledger_wait' => '612 ms'];
    $mother = $case['mother'] ?? ['name' => '—', 'ic' => '—'];
    $father = $case['father'] ?? ['name' => '—', 'ic' => '—'];
    $hosp   = $case['hospital'] ?? ['name' => 'Hospital', 'born_at' => '—'];
    $b      = $case['borang'] ?? [];
    $babyName = $case['name'] ?? ($b['baby_name'] ?? '—');
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 18px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 01 · Borang JPN.LM05 · Akta 299</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Sijil Kelahiran</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Dikeluarkan selepas catatan kekal blockchain · mencetus kelayakan MyKid · QR crypto-signed</p>
    </header>

    {{-- Blockchain confirmation (passthrough completed) --}}
    <div style="background: #F5F3FF; border: 1px solid #DDD6FE; border-radius: 10px; padding: 14px 18px; margin-bottom: 18px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #7C3AED; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700;">✓</div>
        <div style="flex: 1; min-width: 220px;">
            <div style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #7C3AED; font-weight: 700;">Catatan Blockchain Disahkan</div>
            <div style="font-size: 13px; color: #475569;">Blok <strong style="font-family: ui-monospace, monospace; color:#581C87;">#{{ $s['block'] }}</strong> · <span style="font-family: ui-monospace, monospace;">{{ $s['tx_hash'] }}</span> · {{ $s['ledger_wait'] }}</div>
        </div>
        <span style="background: #DCFCE7; color: #15803D; padding: 5px 14px; border-radius: 999px; font-size: 12px; font-weight: 700;">✓ REKOD KEKAL · BUKTI MAHKAMAH (S.90A)</span>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
        {{-- Certificate JPN.LM05 --}}
        <section style="background: #fff; border: 2px solid #15803D; border-radius: 14px; padding: 28px 32px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -30px; right: -30px; width: 140px; height: 140px; background: radial-gradient(circle, #16A34A11, transparent); border-radius: 50%;"></div>
            <div style="text-align: center; border-bottom: 2px solid #E5E7EB; padding-bottom: 14px; margin-bottom: 18px;">
                <div style="font-size: 11px; letter-spacing: 2px; color: #15803D; font-weight: 700;">JABATAN PENDAFTARAN NEGARA MALAYSIA</div>
                <div style="font-size: 20px; font-weight: 800; color: var(--ink-navy); margin-top: 4px;">SIJIL KELAHIRAN</div>
                <div style="font-size: 11px; color: #6B7280;">Akta Pendaftaran Kelahiran dan Kematian 1957 (Akta 299) · Borang JPN.LM05</div>
            </div>

            <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 16px;">
                @include('system._mykad-photo', ['ic' => $case['reference'] ?? $babyName, 'name' => $babyName, 'shape' => 'rect', 'size' => 96, 'gender' => 'F'])
                <div>
                    <div style="font-size: 10.5px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.4px;">Nama</div>
                    <div style="font-size: 18px; font-weight: 800; color: var(--ink-navy);">{{ $babyName }}</div>
                    <div style="font-size: 12px; color: #6B7280; margin-top: 2px;">No. Daftar <strong style="font-family: ui-monospace, monospace; color:#15803D;">{{ $s['reg_no'] }}</strong></div>
                </div>
            </div>

            <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
                <tr><td style="padding: 7px 0; color: #6B7280; width: 42%;">No. Sijil</td><td style="font-family: ui-monospace, monospace; font-weight: 700;">{{ $s['cert_no'] }}</td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280;">Tarikh / Tempat Lahir</td><td style="font-weight: 600;">{{ $b['dob'] ?? $case['dob'] ?? '—' }} · {{ $b['born_place'] ?? $hosp['name'] }}</td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280;">Jantina</td><td style="font-weight: 600;">{{ $b['sex'] ?? 'Perempuan' }}</td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280;">Nama Ibu</td><td style="font-weight: 600;">{{ $mother['name'] }} <span style="color:#6B7280; font-family: ui-monospace, monospace;">({{ $mother['ic'] }})</span></td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280;">Nama Bapa</td><td style="font-weight: 600;">{{ $father['name'] }} <span style="color:#6B7280; font-family: ui-monospace, monospace;">({{ $father['ic'] }})</span></td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280;">Tarikh Daftar</td><td style="font-weight: 600;">{{ $s['issued_date'] }}</td></tr>
            </table>
        </section>

        {{-- QR + MyKid eligibility --}}
        <aside>
            <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px; text-align: center; margin-bottom: 14px;">
                <div style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #6B7280; font-weight: 700; margin-bottom: 10px;">QR Pengesahan</div>
                <div style="display: flex; justify-content: center;">@include('system._qr', ['seed' => $s['cert_no'] . $s['reg_no'], 'px' => 150])</div>
                <div style="font-size: 11px; color: #6B7280; margin-top: 10px;">Imbas untuk sahkan terhadap ledger Hyperledger Fabric · Akta Keterangan 1950 S.90A.</div>
            </div>
            <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 16px 18px; margin-bottom: 14px;">
                <div style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #1E40AF; font-weight: 700;">MyKid · Dokumen Identiti Pertama</div>
                <div style="font-family: ui-monospace, monospace; font-size: 16px; font-weight: 800; color: #1E3A8A; margin: 4px 0;">{{ $s['mykid_no'] }}</div>
                <div style="font-size: 12px; color: #475569;">Layak automatik selepas pendaftaran kelahiran. Notis kutipan dihantar (SMS + e-mel) — sedia dalam 7–14 hari.</div>
            </div>
            <div style="background: #ECFDF5; border: 1px solid #22C55E; border-radius: 10px; padding: 16px 18px;">
                <div style="font-weight: 700; color: #15803D; margin-bottom: 6px;">✓ Sijil Sedia Dikeluarkan</div>
                <div style="font-size: 12px; color: #475569;">Salinan rasmi · cetakan kaunter berkualiti keselamatan.</div>
                <button type="button" style="margin-top: 12px; width: 100%; background: var(--ink-navy); color: #fff; border: 0; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer;">Cetak Sijil</button>
            </div>
        </aside>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi LAMPIRAN A Modul 01 (Sijil = entry point identiti):</strong>
        <span style="color: #475569; font-size: 13px;"> Sijil JPN.LM05 dikeluarkan hanya selepas rekod dicatat kekal di blockchain. Ia mencetus kelayakan MyKid (dokumen identiti pertama) dan kaskad ke 7 modul — warganegara, pendidikan, LHDN, Family Tree. QR crypto-signed membolehkan pengesahan tanpa hubungi JPN.</span>
    </div>
</div>
@endsection
