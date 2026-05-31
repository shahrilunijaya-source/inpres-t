@extends('layouts.system', ['active' => 'kad', 'title' => 'Kad MyKad Dikeluarkan'])

@section('content')
@php
    $card = $case['card'] ?? ['card_no' => $case['card_no'] ?? '—', 'issue_date' => '—', 'expiry' => 'SEUMUR HIDUP', 'citizenship' => 'WARGANEGARA', 'birth_place' => 'MALAYSIA', 'type_label' => 'Gantian Hilang'];
    $gender = ($case['gender'] ?? 'F') === 'M' ? 'LELAKI' : 'PEREMPUAN';
    $religion = $case['religion'] ?? null;          // only Muslims have agama printed on the card front
    $showAgama = is_string($religion) && strtoupper($religion) === 'ISLAM';
    $addr = strtoupper($case['address'] ?? '—');
    $fmt = function ($d) {
        if (empty($d) || $d === '—') return '—';
        try { return \Carbon\Carbon::parse($d)->format('d/m/Y'); }
        catch (\Throwable $e) { return $d; }
    };
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 18px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 04 ms.62 · ICAO Doc 9303 · Polikarbonat</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Kad Pengenalan (MyKad) · Dikeluarkan</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Personalisation + key injection PKI selesai · kad gantian sedia untuk serahan</p>
    </header>

    {{-- Issued banner --}}
    <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 14px 20px; margin-bottom: 18px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
        <span style="background: #16A34A; color: #fff; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">✓</span>
        <div style="flex: 1; min-width: 220px;">
            <div style="font-weight: 700; color: #15803D; font-size: 14px;">MyKad {{ $card['card_no'] }} berjaya dikeluarkan</div>
            <div style="font-size: 12.5px; color: #475569;">{{ $card['type_label'] }} · MyKadIssued dicatat ke blockchain · MyDigital ID auto-provisioned</div>
        </div>
        <span style="background: #DCFCE7; color: #15803D; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 700;">STATUS · SEDIA SERAHAN</span>
    </div>

    <div style="display: grid; grid-template-columns: auto 1fr; gap: 24px; align-items: start;">
        {{-- The physical card mockup --}}
        <div>
            <div style="width: 520px; max-width: 88vw; aspect-ratio: 1.586; border-radius: 16px; padding: 18px 20px; position: relative; overflow: hidden; color: var(--ink-navy); box-shadow: 0 14px 40px rgba(var(--ink-navy-rgb),.32); background: linear-gradient(135deg, #BFD3EC 0%, #9FC0E3 38%, #C9DBF0 70%, #AEC9E8 100%);">
                {{-- guilloché security texture --}}
                <div style="position:absolute; inset:0; opacity:.35; background:
                    repeating-linear-gradient(45deg, rgba(255,255,255,.35) 0 2px, transparent 2px 7px),
                    repeating-linear-gradient(-45deg, rgba(30,64,175,.10) 0 2px, transparent 2px 9px);"></div>

                {{-- header --}}
                <div style="position: relative; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="display: flex; align-items: center; gap: 9px;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: radial-gradient(circle at 35% 30%, #FFE08A, #C9962B); border: 1.5px solid #9A741F; display: flex; align-items: center; justify-content: center;">@include('system._icon', ['name' => 'shield', 'color' => '#6B4E0F', 'size' => 18])</div>
                        <div style="line-height: 1.05;">
                            <div style="font-size: 14px; font-weight: 800; letter-spacing: 2px;">MALAYSIA</div>
                            <div style="font-size: 9px; letter-spacing: 1.4px; color: #1E3A6B;">KAD PENGENALAN</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 8px; letter-spacing: 1px; color: #1E3A6B;">JABATAN PENDAFTARAN NEGARA</div>
                        <div style="font-size: 8px; color: #2E4D7B;">KEMENTERIAN DALAM NEGERI</div>
                    </div>
                </div>

                {{-- IC number --}}
                <div style="position: relative; margin-top: 10px; font-family: ui-monospace, 'Courier New', monospace; font-size: 25px; font-weight: 800; letter-spacing: 2px; color: var(--ink-navy); text-shadow: 0 1px 0 rgba(255,255,255,.5);">{{ $case['ic'] ?? '—' }}</div>

                <div style="position: relative; display: flex; gap: 16px; margin-top: 8px;">
                    {{-- photo --}}
                    <div style="flex-shrink: 0;">
                        @include('system._mykad-photo', ['ic' => $case['ic'] ?? '', 'name' => $case['name'] ?? '', 'shape' => 'rect', 'size' => 132, 'gender' => $case['gender'] ?? 'F'])
                    </div>
                    {{-- particulars --}}
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 14.5px; font-weight: 800; line-height: 1.2; text-transform: uppercase;">{{ $case['name'] ?? '—' }}</div>
                        <div style="font-size: 10px; line-height: 1.45; margin-top: 6px; color: #11264a;">{{ $addr }}</div>
                        <div style="display: flex; gap: 14px; margin-top: 10px; align-items: flex-end;">
                            @if($showAgama)
                                <div><div style="font-size: 7.5px; letter-spacing: .5px; color: #2E4D7B;">AGAMA</div><div style="font-size: 11px; font-weight: 700;">{{ strtoupper($religion) }}</div></div>
                            @endif
                            <div><div style="font-size: 7.5px; letter-spacing: .5px; color: #2E4D7B;">{{ $card['citizenship'] }}</div><div style="font-size: 11px; font-weight: 700;">{{ $gender }}</div></div>
                        </div>
                    </div>
                </div>

                {{-- chip + hologram --}}
                <div style="position: absolute; bottom: 16px; left: 20px; width: 42px; height: 32px; border-radius: 6px; background: linear-gradient(135deg, #E7C766, #B8902F); border: 1px solid #9A741F;">
                    <div style="position:absolute; inset:5px; border:1px solid rgba(154,116,31,.7); border-radius:3px;
                        background: repeating-linear-gradient(0deg, transparent 0 4px, rgba(154,116,31,.5) 4px 5px), repeating-linear-gradient(90deg, transparent 0 6px, rgba(154,116,31,.5) 6px 7px);"></div>
                </div>
                <div style="position: absolute; bottom: 14px; right: 18px; width: 54px; height: 54px; border-radius: 50%; background: radial-gradient(circle at 40% 35%, rgba(255,255,255,.7), rgba(160,192,227,.25) 60%, transparent); border: 1px dashed rgba(30,64,175,.35); display:flex; align-items:center; justify-content:center; font-size:8px; color:#1E3A6B; text-align:center; line-height:1.1;">SIRIM<br>HOLO</div>
            </div>
            <div style="margin-top: 8px; font-size: 11px; color: #6B7280; text-align: center; width: 520px; max-width: 88vw;">Polikarbonat · laser engrave · cip PKI dwi-antara muka · imej hantu (ghost) keselamatan</div>
        </div>

        {{-- issue details + collection --}}
        <aside>
            <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px; margin-bottom: 14px;">
                <h3 style="margin: 0 0 12px; font-size: 14px;">Butiran Pengeluaran</h3>
                @foreach([
                    ['No. Siri Kad', $card['card_no']],
                    ['Jenis', $card['type_label']],
                    ['Tarikh Keluar', $fmt($card['issue_date'])],
                    ['Tempoh Sah', $card['expiry']],
                    ['Tempat Lahir', $card['birth_place']],
                    ['Tarikh Lahir', $fmt($case['dob'] ?? null)],
                ] as $f)
                    <div style="display: flex; justify-content: space-between; gap: 12px; padding: 7px 0; border-bottom: 1px dashed #F1F5F9; font-size: 12.5px;">
                        <span style="color: #6B7280;">{{ $f[0] }}</span>
                        <span style="font-weight: 600; color: var(--ink-navy); text-align: right;">{{ $f[1] }}</span>
                    </div>
                @endforeach
            </section>

            <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px; margin-bottom: 14px;">
                <h3 style="margin: 0 0 10px; font-size: 14px;">Rekod Blockchain</h3>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 12px; padding: 6px 0;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #DC2626;"></span>
                    <span style="flex: 1;">Kad lama <strong>{{ $case['lapor']['old_card_no'] ?? '—' }}</strong> — CardRevoked</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 12px; padding: 6px 0;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A;"></span>
                    <span style="flex: 1;">Kad baru <strong>{{ $card['card_no'] }}</strong> — MyKadIssued</span>
                </div>
                <div style="margin-top: 8px; font-family: ui-monospace, monospace; font-size: 10.5px; color: #7C3AED; background: #F5F3FF; border-radius: 6px; padding: 7px 10px;">Blok #{{ $case['blockchain']['events'][0]['block'] ?? '—' }} · {{ $case['blockchain']['events'][0]['hash'] ?? '—' }}</div>
            </section>

            <div style="display: flex; gap: 10px;">
                @include('system._qr', ['seed' => ($card['card_no'] ?? '') . ($case['ic'] ?? ''), 'px' => 92])
                <div style="background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px; padding: 12px 14px; flex: 1;">
                    <div style="font-weight: 700; color: #1E40AF; font-size: 12.5px;">Serahan Kad</div>
                    <div style="font-size: 11.5px; color: #475569; margin-top: 3px;">SMS dihantar ke pemohon. Kad sedia diambil di kaunter dalam 1 hari bekerja, atau pos berdaftar.</div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
