@extends('layouts.system', ['active' => 'sijil', 'title' => 'Sijil Perkahwinan'])

@section('content')
@php
    $s = $case['sijil'] ?? ['cert_no' => 'JPN.KC02-' . $case['reference'], 'reg_no' => $case['reference'], 'issued_date' => '—', 'block' => 1925015, 'tx_hash' => '0x000…', 'ledger_wait' => '612 ms', 'copies' => 2];
    $up = $case['upacara'] ?? ['venue' => 'JPN Putrajaya', 'registrar' => 'Pendaftar Perkahwinan', 'witnesses' => []];
    $groom = $case['groom'] ?? ['name' => $case['name'], 'ic' => $case['ic']];
    $bride = $case['bride'] ?? ['name' => 'Pasangan', 'ic' => '—'];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 05 · Borang JPN.KC02 · Akta 164</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Sijil Perkahwinan Sivil</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Dikeluarkan selepas catatan kekal blockchain · QR crypto-signed · {{ $s['copies'] }} salinan</p>
    </header>

    {{-- Blockchain confirmation banner (the passthrough completed) --}}
    <div style="background: #F5F3FF; border: 1px solid #DDD6FE; border-radius: 10px; padding: 14px 18px; margin-bottom: 18px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #7C3AED; color: #fff; display: flex; align-items: center; justify-content: center;">@include('system._icon', ['name' => 'link', 'color' => '#fff', 'size' => 18])</div>
        <div style="flex: 1; min-width: 220px;">
            <div style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #7C3AED; font-weight: 700;">Catatan Blockchain Disahkan</div>
            <div style="font-size: 13px; color: #475569;">Blok <strong style="font-family: ui-monospace, monospace; color:#581C87;">#{{ $s['block'] }}</strong> · <span style="font-family: ui-monospace, monospace;">{{ $s['tx_hash'] }}</span> · masa pengesahan {{ $s['ledger_wait'] }}</div>
        </div>
        <span style="background: #DCFCE7; color: #15803D; padding: 5px 14px; border-radius: 999px; font-size: 12px; font-weight: 700;">✓ STATUS UNDANG-UNDANG: SUAMI ISTERI SAH</span>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
        {{-- The certificate --}}
        <section style="background: #fff; border: 2px solid #1E3A8A; border-radius: 14px; padding: 28px 32px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -30px; right: -30px; width: 140px; height: 140px; background: radial-gradient(circle, #1E40AF11, transparent); border-radius: 50%;"></div>
            <div style="text-align: center; border-bottom: 2px solid #E5E7EB; padding-bottom: 14px; margin-bottom: 18px;">
                <div style="font-size: 11px; letter-spacing: 2px; color: #1E3A8A; font-weight: 700;">JABATAN PENDAFTARAN NEGARA MALAYSIA</div>
                <div style="font-size: 20px; font-weight: 800; color: var(--ink-navy); margin-top: 4px;">SIJIL PERKAHWINAN</div>
                <div style="font-size: 11px; color: #6B7280;">Akta Membaharui Undang-Undang (Perkahwinan & Perceraian) 1976 · Borang JPN.KC02</div>
            </div>

            <div style="display: flex; align-items: center; justify-content: center; gap: 24px; margin: 18px 0;">
                <div style="text-align: center;">
                    <div style="display: flex; justify-content: center;">@include('system._mykad-photo', ['ic' => $groom['ic'], 'name' => $groom['name'], 'shape' => 'circle', 'size' => 88, 'gender' => 'M'])</div>
                    <div style="font-weight: 700; font-size: 14px; margin-top: 6px;">{{ $groom['name'] }}</div>
                    <div style="font-family: ui-monospace, monospace; font-size: 11px; color: #6B7280;">{{ $groom['ic'] }}</div>
                </div>
                <div style="color: #BE185D; display: flex; align-items: center;">@include('system._icon', ['name' => 'heart', 'color' => '#BE185D', 'size' => 26])</div>
                <div style="text-align: center;">
                    <div style="display: flex; justify-content: center;">@include('system._mykad-photo', ['ic' => $bride['ic'], 'name' => $bride['name'], 'shape' => 'circle', 'size' => 88, 'gender' => 'F'])</div>
                    <div style="font-weight: 700; font-size: 14px; margin-top: 6px;">{{ $bride['name'] }}</div>
                    <div style="font-family: ui-monospace, monospace; font-size: 11px; color: #6B7280;">{{ $bride['ic'] }}</div>
                </div>
            </div>

            <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
                <tr><td style="padding: 7px 0; color: #6B7280; width: 42%;">No. Sijil</td><td style="font-family: ui-monospace, monospace; font-weight: 700;">{{ $s['cert_no'] }}</td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280;">No. Daftar Perkahwinan</td><td style="font-family: ui-monospace, monospace; font-weight: 700;">{{ $s['reg_no'] }}</td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280;">Tarikh Perkahwinan</td><td style="font-weight: 600;">{{ $s['issued_date'] }}</td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280;">Tempat Upacara</td><td style="font-weight: 600;">{{ $up['venue'] }}</td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280;">Pendaftar</td><td style="font-weight: 600;">{{ $up['registrar'] }}</td></tr>
                <tr><td style="padding: 7px 0; color: #6B7280; vertical-align: top;">Saksi</td><td style="font-weight: 600;">@foreach(($up['witnesses'] ?? []) as $w){{ $w['name'] }}@if(!$loop->last)<br>@endif @endforeach</td></tr>
            </table>
        </section>

        {{-- QR + issuance --}}
        <aside>
            <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px; text-align: center; margin-bottom: 14px;">
                <div style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #6B7280; font-weight: 700; margin-bottom: 10px;">QR Pengesahan</div>
                <div style="display: flex; justify-content: center;">@include('system._qr', ['seed' => $s['cert_no'] . $s['reg_no'], 'px' => 150])</div>
                <div style="font-size: 11px; color: #6B7280; margin-top: 10px;">Imbas untuk sahkan terhadap ledger Hyperledger Fabric. Crypto signature · Akta Keterangan 1950 S.90A.</div>
            </div>
            <div style="background: #ECFDF5; border: 1px solid #22C55E; border-radius: 10px; padding: 16px 18px;">
                <div style="font-weight: 700; color: #15803D; margin-bottom: 6px;">✓ Sijil Sedia Dikeluarkan</div>
                <div style="font-size: 12px; color: #475569;">{{ $s['copies'] }} salinan rasmi untuk pasangan · cetakan kaunter berkualiti keselamatan.</div>
                <button type="button" style="margin-top: 12px; width: 100%; background: var(--ink-navy); color: #fff; border: 0; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer;">Cetak {{ $s['copies'] }} Salinan</button>
            </div>
        </aside>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi LAMPIRAN A Modul 05 (Sijil = entry point warisan + family tree):</strong>
        <span style="color: #475569; font-size: 13px;"> Sijil JPN.KC02 dikeluarkan hanya selepas rekod dicatat kekal di blockchain. QR crypto-signed membolehkan pengesahan tanpa hubungi JPN. Sijil mencetuskan kaskad ke Family Tree, KWSP (penama), LHDN (status cukai), dan Pencen.</span>
    </div>
</div>
@endsection
