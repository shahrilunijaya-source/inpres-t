@extends('layouts.system', ['active' => 'lapor', 'title' => 'Lapor Kehilangan MyKad'])

@section('content')
@php
    $lp = $case['lapor'] ?? [
        'report_no' => '—', 'station' => '—', 'report_date' => '—', 'old_card_no' => '—',
        'old_status' => 'DIBATALKAN', 'reason' => '—', 'fee' => 'RM 110.00', 'fee_status' => 'DIBAYAR',
        'declared_by' => $case['name'] ?? '—',
    ];
    $fmt = function ($d) {
        if (empty($d) || $d === '—') return '—';
        try { return \Carbon\Carbon::parse($d)->format('d/m/Y H:i'); }
        catch (\Throwable $e) { return $d; }
    };
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 20px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Modul 04 · Gantian Hilang · Akta 174 (Pendaftaran Negara)</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Lapor Kehilangan &amp; Permohonan Gantian</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Laporan polis wajib bagi MyKad hilang · kad lama dibatalkan serta-merta · yuran gantian dikenakan</p>
    </header>

    {{-- Old card revoked banner --}}
    <div style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 4px solid #DC2626; border-radius: 10px; padding: 14px 20px; margin-bottom: 16px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
        <span style="background: #DC2626; color: #fff; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">!</span>
        <div style="flex: 1; min-width: 220px;">
            <div style="font-weight: 700; color: #B91C1C; font-size: 14px;">Kad lama {{ $lp['old_card_no'] }} — {{ $lp['old_status'] }}</div>
            <div style="font-size: 12.5px; color: #475569; margin-top: 2px;">Dibatalkan dalam pangkalan data MyKad Master &amp; disekat di semua kaunter agensi. Tidak boleh digunakan walaupun ditemui semula.</div>
        </div>
        <span style="background: #FEE2E2; color: #B91C1C; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 700;">CardRevoked · blockchain</span>
    </div>

    <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 16px;">
        {{-- Police report --}}
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="margin: 0; font-size: 14px;">Laporan Polis</h3>
                <span style="background: #DBEAFE; color: #1E40AF; padding: 3px 10px; border-radius: 999px; font-size: 10.5px; font-weight: 700;">● Sah dari PDRM e-Report</span>
            </div>
            @foreach([
                ['No. Laporan Polis', $lp['report_no']],
                ['Balai Polis', $lp['station']],
                ['Tarikh &amp; Masa Lapor', $fmt($lp['report_date'])],
                ['Pengadu', $lp['declared_by']],
                ['No. MyKad', $case['ic'] ?? '—'],
            ] as $f)
                <div style="display: flex; justify-content: space-between; gap: 14px; padding: 8px 0; border-bottom: 1px dashed #F1F5F9; font-size: 13px;">
                    <span style="color: #6B7280; white-space: nowrap;">{!! $f[0] !!}</span>
                    <span style="font-weight: 600; color: var(--ink-navy); text-align: right;">{{ $f[1] }}</span>
                </div>
            @endforeach
            <div style="margin-top: 12px;">
                <div style="font-size: 10.5px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.4px;">Keterangan Kehilangan</div>
                <div style="font-size: 13px; color: var(--ink-navy); margin-top: 4px; background: #F8FAFC; border-radius: 8px; padding: 10px 12px;">{{ $lp['reason'] }}</div>
            </div>
        </section>

        {{-- Application + fee --}}
        <aside>
            <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 18px; margin-bottom: 14px;">
                <h3 style="margin: 0 0 12px; font-size: 14px;">Permohonan Gantian</h3>
                @foreach([['Jenis Permohonan', 'Gantian Hilang'], ['Modul', 'Modul 04 · Kad Pengenalan'], ['Rujukan', $case['reference'] ?? '—']] as $f)
                    <div style="display: flex; justify-content: space-between; gap: 10px; padding: 7px 0; border-bottom: 1px dashed #F1F5F9; font-size: 12.5px;">
                        <span style="color: #6B7280;">{{ $f[0] }}</span>
                        <span style="font-weight: 600; color: var(--ink-navy); text-align: right; font-family: ui-monospace, monospace; font-size: 11.5px;">{{ $f[1] }}</span>
                    </div>
                @endforeach
            </section>

            <section style="background: #ECFDF5; border: 1px solid #BBF7D0; border-radius: 10px; padding: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 10.5px; color: #15803D; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">Yuran Gantian Hilang</div>
                        <div style="font-size: 24px; font-weight: 800; color: #15803D; margin-top: 2px;">{{ $lp['fee'] }}</div>
                    </div>
                    <span style="background: #16A34A; color: #fff; padding: 5px 14px; border-radius: 999px; font-size: 12px; font-weight: 700;">✓ {{ $lp['fee_status'] }}</span>
                </div>
                <div style="font-size: 11px; color: #475569; margin-top: 8px;">Hilang = RM110 (kali ke-2: RM310). Berbeza dari gantian rosak (RM100).</div>
            </section>
        </aside>
    </div>

    {{-- Next step --}}
    <div style="margin-top: 16px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 240px;">
            <div style="font-weight: 700; color: #B45309; font-size: 14px;">Laporan disahkan — teruskan ke pengesahan biometrik</div>
            <div style="font-size: 12.5px; color: #475569; margin-top: 2px;">Pemohon hadir ke kaunter untuk re-capture biometrik (10 cap jari + muka + iris) bagi sahkan identiti sebelum kad gantian dikeluarkan.</div>
        </div>
        <a href="{{ route('system.biometric', ['ref' => $case['reference']]) }}" style="text-decoration: none; background: var(--ink-navy); color: #fff; padding: 11px 20px; border-radius: 9px; font-size: 13px; font-weight: 700;">Teruskan ke Biometrik →</a>
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi · Gantian Hilang:</strong>
        <span style="color: #475569; font-size: 13px;"> Laporan polis menghalang penyalahgunaan identiti — kad lama dibatalkan serta-merta di blockchain &amp; semua agensi, supaya tiada pihak boleh guna MyKad yang hilang. Biometrik 1:N memastikan pemohon ialah pemilik sah.</span>
    </div>
</div>
@endsection
