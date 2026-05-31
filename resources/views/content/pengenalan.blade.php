@extends('layouts.portal')

@section('title', 'Pengenalan — Portal InPreS')

@php $nav = ''; @endphp

@section('main')
{{-- ============ Page header (pine) ============ --}}
<section class="pine-hero" id="main" style="padding:64px 0 56px;">
    <div class="container">
        <span class="eyebrow on-pine">
            Maklumat Korporat
            <span class="e-dot orange"></span>
        </span>
        <h1 style="margin:14px 0 0;">
            Pengenalan <span class="accent">JPN<span class="signature-dot"></span></span>
        </h1>
        <p class="lede" style="max-width:760px;">
            Jabatan Pendaftaran Negara (JPN) ialah agensi di bawah Kementerian Dalam
            Negeri yang bertanggungjawab terhadap pendaftaran sivil dan pengenalan diri
            rakyat Malaysia.
        </p>
    </div>
</section>

{{-- ============ Body ============ --}}
<section class="section">
    <div class="container">
        <div style="display:grid;gap:40px;">

            <div class="intro-split">
                <div>
                    <span class="eyebrow">Latar Belakang<span class="e-dot"></span></span>
                    <h2 style="margin:8px 0 0;font:700 30px/1.1 var(--font-sans);letter-spacing:-0.02em;">
                        Tunjang rekod sivil negara
                    </h2>
                </div>
                <div>
                    <p style="color:var(--ai-text-dim,#4B5563);font-size:17px;line-height:1.7;">
                        JPN menyimpan dan menguruskan rekod kelahiran, kematian, perkahwinan
                        dan perceraian, serta mengeluarkan dokumen pengenalan diri seperti
                        MyKad, MyKid dan MyTentera. Setiap rekod ini menjadi asas kepada
                        identiti rasmi setiap individu dari lahir hingga ke akhir hayat.
                    </p>
                    <p style="color:var(--ai-text-dim,#4B5563);font-size:17px;line-height:1.7;margin-top:14px;">
                        Portal InPreS membawa sebahagian perkhidmatan ini ke dalam talian —
                        membolehkan rakyat memohon, mengimbas dokumen dengan bantuan AI, dan
                        menjejak status permohonan tanpa perlu beratur di kaunter.
                    </p>
                </div>
            </div>

            {{-- Visi / Misi --}}
            <div class="svc-grid" style="grid-template-columns:1fr 1fr;">
                <div class="svc" style="cursor:default;">
                    <span class="ico"><i data-lucide="eye"></i></span>
                    <h4>Visi</h4>
                    <p>Menjadi peneraju perkhidmatan pendaftaran identiti dan sivil yang
                       dipercayai serta mesra rakyat.</p>
                </div>
                <div class="svc" style="cursor:default;">
                    <span class="ico"><i data-lucide="target"></i></span>
                    <h4>Misi</h4>
                    <p>Menyampaikan perkhidmatan pendaftaran yang tepat, selamat dan
                       pantas melalui inovasi digital dan integriti data.</p>
                </div>
            </div>

            {{-- Fungsi utama --}}
            <div>
                <span class="eyebrow">Fungsi Utama<span class="e-dot orange"></span></span>
                <h2 style="margin:8px 0 16px;font:700 30px/1.1 var(--font-sans);letter-spacing:-0.02em;">
                    Apa yang JPN uruskan
                </h2>
                <div class="svc-grid">
                    @foreach ([
                        ['i'=>'baby','t'=>'Pendaftaran Kelahiran & Kematian','p'=>'Rekod dan sijil kelahiran serta kematian seluruh negara.'],
                        ['i'=>'credit-card','t'=>'Pengenalan Diri','p'=>'Pengeluaran MyKad, MyKid, MyTentera dan penggantian.'],
                        ['i'=>'heart','t'=>'Perkahwinan & Perceraian','p'=>'Pendaftaran perkahwinan dan perceraian bukan Islam.'],
                        ['i'=>'flag','t'=>'Kewarganegaraan','p'=>'Permohonan taraf warganegara dan permastautin tetap.'],
                        ['i'=>'users','t'=>'Pengangkatan','p'=>'Pendaftaran anak angkat di bawah peruntukan undang-undang.'],
                        ['i'=>'shield-check','t'=>'Integriti Data','p'=>'Pengurusan dan perlindungan rekod pendaftaran negara.'],
                    ] as $f)
                        <div class="svc" style="cursor:default;">
                            <span class="ico"><i data-lucide="{{ $f['i'] }}"></i></span>
                            <h4>{{ $f['t'] }}</h4>
                            <p>{{ $f['p'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Rujukan rasmi --}}
            <div style="background:var(--ai-indigo-soft,#E6F8F6);border:1px solid var(--ai-border,#e5e7eb);border-radius:16px;padding:24px 28px;">
                <p style="margin:0;color:var(--ai-text,#003D3A);font-size:15px;">
                    <b>Prototaip.</b> Maklumat di halaman ini bersifat ringkasan. Untuk
                    maklumat dan urusan rasmi, sila layari
                    <a href="https://www.jpn.gov.my/my/maklumat-korporat/pengenalan" target="_blank" rel="noopener" style="color:var(--ai-indigo,#00B8A9);font-weight:600;">jpn.gov.my</a>
                    atau hubungi talian 03-8000 8000.
                </p>
            </div>

        </div>
    </div>
</section>

@push('head')
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<style>
.intro-split{display:grid;grid-template-columns:0.85fr 1.15fr;gap:48px;align-items:start;}
@media(max-width:880px){.intro-split{grid-template-columns:1fr;gap:16px;}}
</style>
@endpush
@push('scripts')
<script>(function(){var t=function(){window.lucide&&lucide.createIcons();};t();setInterval(t,800);})();</script>
@endpush
@endsection
