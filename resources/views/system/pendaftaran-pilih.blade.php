@extends('layouts.system', ['active' => 'wizard', 'title' => 'Pendaftaran Baru'])

@section('content')
@php
    // Every registrable document under JPN. Each is a submodule of "Pendaftaran Baru".
    // Only Kelahiran has the working intake wizard in this prototype; the rest are v2.
    $docs = [
        ['Sijil Kelahiran',        'Modul 01 · Akta 299',  'Pendaftaran kelahiran baru dari hospital/klinik → biometrik kaunter → sijil JPN.LM05', 'birth',       true,  '#1E40AF'],
        ['Sijil Perkahwinan Sivil','Modul 05 · Akta 164',  'Pendaftaran perkahwinan sivil (bukan Islam) → kaveat 21 hari → upacara → sijil JPN.KC02', 'marriage',  false, '#BE185D'],
        ['Kad Pengenalan (MyKad)', 'Modul 04 · ICAO 9303', 'Permohonan MyKad: kali pertama, gantian hilang/rosak, pertukaran → kad polikarbonat',     'mykad',     false, '#D97706'],
        ['Sijil Kematian',         'Modul 02 · Akta 299',  'Pendaftaran kematian → perakuan kematian → kemas kini rekod warganegara',                 'death',     false, '#475569'],
        ['Pengangkatan',           'Modul 06 · Akta 253',  'Pendaftaran anak angkat → kemas kini salasilah → sijil pengangkatan',                     'adoption',  false, '#15803D'],
        ['Pertukaran Nama',        'Modul 07',             'Permohonan pertukaran nama → pengesahan → kemas kini MyKad & rekod',                      'name',      false, '#6366F1'],
    ];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 8px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">Permohonan · Pendaftaran Baru</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Pendaftaran Baru · Pilih Jenis Dokumen</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">Setiap dokumen JPN ialah submodul tersendiri. Pilih jenis untuk memulakan permohonan baru.</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 20px;">
        @foreach($docs as $d)
            @php $active = $d[4]; @endphp
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $d[5] }}; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; min-height: 188px; {{ $active ? '' : 'opacity: .72;' }}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                    <div style="font-size: 10.5px; letter-spacing: .6px; color: #6B7280; text-transform: uppercase;">{{ $d[1] }}</div>
                    @if($active)
                        <span style="background: #DCFCE7; color: #15803D; padding: 2px 9px; border-radius: 999px; font-size: 10px; font-weight: 700;">● AKTIF</span>
                    @else
                        <span style="background: #F1F5F9; color: #64748B; padding: 2px 9px; border-radius: 999px; font-size: 10px; font-weight: 700;">v2</span>
                    @endif
                </div>
                <h3 style="margin: 8px 0 6px; font-size: 16px; color: var(--ink-navy);">{{ $d[0] }}</h3>
                <p style="font-size: 12.5px; color: #475569; line-height: 1.5; flex: 1;">{{ $d[2] }}</p>
                @if($active)
                    <a href="{{ route('system.wizard', ['type' => $d[3]]) }}" style="margin-top: 12px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; background: var(--pine); color: #fff; padding: 10px 16px; border-radius: 8px; font-size: 13px; font-weight: 700;">Mula Pendaftaran →</a>
                @else
                    <span style="margin-top: 12px; display: inline-flex; align-items: center; justify-content: center; background: #F8FAFC; color: #94A3B8; border: 1px solid #E5E7EB; padding: 10px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: not-allowed;">Akan datang</span>
                @endif
            </div>
        @endforeach
    </div>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Nota:</strong>
        <span style="color: #475569; font-size: 13px;"> Pendaftaran Baru merangkumi semua jenis dokumen JPN. Dalam prototaip ini, modul Kelahiran telah lengkap; modul lain (v2) menyusul mengikut fasa.</span>
    </div>
</div>
@endsection
