@extends('layouts.portal')

@section('title', 'Mohon Dokumen — Portal InPreS')

@push('head')
<style>
.form-split{display:grid;grid-template-columns:1.05fr 0.95fr;gap:48px;align-items:start;}
.form-aside{display:flex;flex-direction:column;gap:16px;position:sticky;top:24px;}
.aside-eyebrow{margin-bottom:6px;}
.step-card{display:flex;gap:16px;align-items:flex-start;padding:20px 22px;background:#fff;border:1px solid var(--gray-200,#e5e7eb);border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,0.05);transition:border-color .2s,box-shadow .2s;}
.step-card.is-active{border-color:var(--teal,#00b8a9);box-shadow:0 4px 16px rgba(var(--brand-rgb),0.18);}
.step-num{flex:none;width:38px;height:38px;border-radius:11px;display:grid;place-items:center;color:var(--ink-navy);background:#e6f7f5;font:700 15px/1 var(--font-sans);}
.step-card.is-done .step-num{background:var(--teal-700,#007D72);color:#fff;}
.step-card h4{font:700 16px/1.2 var(--font-sans);color:var(--pine,#003d3a);margin:0 0 4px;}
.step-card p{font:400 14px/1.55 var(--font-sans);color:var(--gray-600,#4B5563);margin:0;}
.aside-note{display:flex;gap:13px;padding:20px 22px;border-radius:16px;background:linear-gradient(135deg,#1b3a6b,#0f2240);}
.aside-note .ico{flex:none;color:var(--teal,#00b8a9);margin-top:1px;}
.aside-note .t{font:700 14px/1.3 var(--font-sans);color:#fff;margin:0 0 4px;}
.aside-note .d{font:400 13px/1.55 var(--font-sans);color:rgba(255,255,255,0.72);margin:0;}

/* ---- Wizard ---- */
.wiz-progress{display:flex;gap:6px;margin-bottom:26px;}
.wiz-progress .seg{flex:1;height:5px;border-radius:999px;background:var(--ai-border,#26303a);transition:background .3s;}
.wiz-progress .seg.on{background:linear-gradient(90deg,var(--teal,#00b8a9),var(--teal-700,#007D72));}
.wiz-step{display:none;}
.wiz-step.active{display:block;animation:wizfade .35s ease;}
@keyframes wizfade{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
.wiz-sec-title{font:700 13px/1 var(--font-sans);letter-spacing:.6px;text-transform:uppercase;color:var(--teal,#00b8a9);margin:0 0 4px;}
.wiz-sec-sub{font:400 13px/1.5 var(--font-sans);color:var(--ai-text-mute,#7c8794);margin:0 0 20px;}
.fld{margin-bottom:16px;}
.fld label{display:block;font:500 13px/1 var(--font-sans);color:var(--ai-text-dim,#9aa6b2);margin-bottom:7px;}
.fld input,.fld select,.fld textarea{width:100%;padding:11px 13px;border-radius:10px;background:var(--ai-bg-2,#10181f);border:1px solid var(--ai-border,#26303a);color:var(--ai-text,#e8edf2);font:400 14px/1.4 var(--font-sans);}
.fld input:focus,.fld select:focus,.fld textarea:focus{outline:none;border-color:var(--teal,#00b8a9);}
.fld .hint{font:400 11.5px/1.4 var(--font-sans);color:var(--ai-text-mute,#7c8794);margin-top:5px;}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:0 16px;}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:0 16px;}
.pull-row{display:flex;gap:10px;align-items:flex-end;}
.pull-row .fld{flex:1;margin-bottom:0;}
.btn-pull{flex:none;padding:11px 16px;border-radius:10px;border:1px solid var(--teal-700,#007D72);background:rgba(var(--brand-rgb),0.12);color:var(--ink-navy);font:600 13px/1 var(--font-sans);cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:7px;}
.btn-pull:hover{background:rgba(var(--brand-rgb),0.22);}
.btn-pull:disabled{opacity:.5;cursor:default;}
.pull-card{margin-top:14px;padding:14px 16px;border-radius:12px;border:1px solid var(--teal-700,#007D72);background:rgba(var(--brand-rgb),0.07);display:none;}
.pull-card.show{display:block;animation:wizfade .3s ease;}
.pull-card .pc-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
.pull-card .pc-badge{font:700 10px/1 var(--font-sans);letter-spacing:.5px;text-transform:uppercase;color:var(--ink-navy);background:rgba(var(--brand-rgb),0.15);padding:4px 9px;border-radius:999px;}
.pc-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px 18px;}
.pc-grid .k{font:400 11px/1.3 var(--font-sans);color:var(--ai-text-mute,#7c8794);text-transform:uppercase;letter-spacing:.4px;}
.pc-grid .v{font:600 13.5px/1.3 var(--font-sans);color:var(--ai-text,#e8edf2);}
.relation-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;}
.relation-grid label{display:block;padding:13px;text-align:center;border-radius:11px;border:1px solid var(--ai-border,#26303a);background:var(--ai-bg-2,#10181f);cursor:pointer;font:600 13px/1 var(--font-sans);color:var(--ai-text-dim,#9aa6b2);}
.relation-grid input{position:absolute;opacity:0;}
.relation-grid input:checked + label,.relation-grid label.on{border-color:var(--teal,#00b8a9);color:var(--ink-navy);background:rgba(var(--brand-rgb),0.15);}
.declare{display:flex;gap:11px;align-items:flex-start;padding:15px;border-radius:12px;background:var(--ai-bg-2,#10181f);border:1px solid var(--ai-border,#26303a);}
.declare input{margin-top:3px;flex:none;}
.declare p{font:400 13px/1.55 var(--font-sans);color:var(--ai-text-dim,#9aa6b2);margin:0;}
.wiz-nav{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:26px;padding-top:20px;border-top:1px solid var(--ai-border,#26303a);}
.btn-back{padding:11px 18px;border-radius:10px;border:1px solid var(--ai-border,#26303a);background:transparent;color:var(--ai-text-dim,#9aa6b2);font:600 14px/1 var(--font-sans);cursor:pointer;}
.btn-back:hover{color:var(--ink-navy);border-color:var(--ai-text-mute,#7c8794);background:rgba(0,0,0,0.04);}
.btn-back[hidden]{visibility:hidden;}
/* pretty inline validation popup (replaces native browser bubble) */
.pf-pop{position:fixed;z-index:9999;max-width:268px;display:flex;gap:9px;align-items:flex-start;padding:11px 13px;border-radius:12px;background:#fff;color:var(--ink-navy);border:1px solid rgba(var(--ink-navy-rgb),0.07);box-shadow:0 12px 32px rgba(var(--ink-navy-rgb),0.18),0 2px 6px rgba(var(--ink-navy-rgb),0.08);font:500 13px/1.45 var(--font-sans);opacity:0;transform:translateY(4px) scale(.97);transition:opacity .16s ease,transform .16s ease;pointer-events:none;}
.pf-pop.show{opacity:1;transform:translateY(0) scale(1);}
.pf-pop .pf-ic{flex:none;width:20px;height:20px;border-radius:7px;display:grid;place-items:center;background:#FFF1E6;color:#E8662A;font:800 13px/1 var(--font-sans);margin-top:1px;}
.pf-pop .pf-msg{margin:0;}
.pf-pop::after{content:"";position:absolute;width:11px;height:11px;background:#fff;left:var(--ax,22px);transform:rotate(45deg);}
.pf-pop[data-pos="top"]::after{bottom:-6px;border-right:1px solid rgba(var(--ink-navy-rgb),0.07);border-bottom:1px solid rgba(var(--ink-navy-rgb),0.07);}
.pf-pop[data-pos="bottom"]::after{top:-6px;border-left:1px solid rgba(var(--ink-navy-rgb),0.07);border-top:1px solid rgba(var(--ink-navy-rgb),0.07);}
.pf-bad{outline:2px solid rgba(232,102,42,0.55)!important;outline-offset:2px;border-radius:11px;animation:pfShake .32s;}
@keyframes pfShake{0%,100%{transform:translateX(0);}20%{transform:translateX(-5px);}40%{transform:translateX(5px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);}}
/* Review */
.rev-block{margin-bottom:16px;padding:15px 17px;border-radius:12px;background:var(--ai-bg-2,#10181f);border:1px solid var(--ai-border,#26303a);}
.rev-block h5{font:700 12px/1 var(--font-sans);letter-spacing:.5px;text-transform:uppercase;color:var(--teal,#00b8a9);margin:0 0 11px;}
.rev-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px 18px;}
.rev-grid .k{font:400 11.5px/1.3 var(--font-sans);color:var(--ai-text-mute,#7c8794);}
.rev-grid .v{font:600 13px/1.35 var(--font-sans);color:var(--ai-text,#e8edf2);}
@media(max-width:880px){.form-split{grid-template-columns:1fr;gap:32px;}.form-aside{position:static;}.grid-2,.grid-3,.relation-grid{grid-template-columns:1fr;}}
</style>
@endpush

@section('main')
{{-- ============ Page header (pine) ============ --}}
<section class="pine-hero" id="main" style="padding:64px 0 56px;">
    <div class="container">
        <span class="eyebrow on-pine">
            Permohonan
            <span class="e-dot orange"></span>
        </span>
        <h1 style="margin:14px 0 0;">Mohon Dokumen JPN</h1>
        <p class="lede" style="max-width:760px;">
            Imbas IC dengan AI — sistem mendapatkan rekod anda terus dari Pendaftaran Negara.
        </p>
    </div>
</section>

{{-- ============ Body ============ --}}
<section class="section">
    <div class="container">
        <div class="form-split">
        <div>
        @if ($errors->any())
            <div class="badge badge-rose mb-6 px-4 py-2">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Doc type selector (shared, above both forms) --}}
        <div class="glass-card-hi p-6 mb-6 fade-up" style="animation-delay:60ms;">
            <label class="block text-sm font-medium mb-3" style="color: var(--ai-text-dim);">Jenis Dokumen</label>
            <div class="grid grid-cols-3 gap-3">
                @foreach (['birth' => 'Sijil Kelahiran', 'marriage' => 'Sijil Perkahwinan', 'mykad' => 'MyKAD'] as $value => $label)
                    <label class="glass-card bounce cursor-pointer p-4 text-center has-[:checked]:glow-ring">
                        <input type="radio" name="doc_type_picker" value="{{ $value }}"
                               {{ ($docType ?? 'birth') === $value ? 'checked' : '' }}
                               class="sr-only peer doc-type-radio">
                        <div class="text-sm font-semibold peer-checked:text-white" style="color: var(--ai-text);">{{ $label }}</div>
                    </label>
                @endforeach
            </div>
            <div class="grid grid-cols-4 gap-3 mt-3">
                @foreach (['Perceraian' => 'Sijil Perceraian', 'Kematian' => 'Sijil Kematian', 'Pengangkatan' => 'Pengangkatan', 'Warganegara' => 'Warganegara'] as $stub => $label)
                    <button type="button" data-stub="{{ $stub }}" class="glass-card p-3 text-center cursor-pointer relative" style="opacity:0.45;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 absolute top-2 right-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--ai-text-mute);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <div class="text-xs font-semibold" style="color: var(--ai-text);">{{ $label }}</div>
                        <div class="text-[10px] mt-1" style="color: var(--ai-text-mute);">Belum tersedia</div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- BIRTH WIZARD (doc_type = birth) — deep LM01 (JPN.LM01)         --}}
        {{-- ============================================================= --}}
        <form action="{{ route('apply.store') }}" method="POST" id="birth-form" novalidate
              class="glass-card-hi p-8 fade-up" style="animation-delay:120ms;">
            @csrf
            <input type="hidden" name="doc_type" value="birth">

            <div class="wiz-progress">
                <div class="seg on" data-seg="0"></div>
                <div class="seg" data-seg="1"></div>
                <div class="seg" data-seg="2"></div>
                <div class="seg" data-seg="3"></div>
                <div class="seg" data-seg="4"></div>
            </div>

            {{-- STEP 1 — Ibu & Bapa (Bahagian C + D) --}}
            <div class="wiz-step active" data-step="0">
                <p class="wiz-sec-title">Bahagian C &amp; D · Ibu &amp; Bapa</p>
                <p class="wiz-sec-sub">Masukkan No. Kad Pengenalan. AI mendapatkan rekod terus dari Pendaftaran Negara — nama, tarikh lahir dan alamat diisi automatik.</p>

                {{-- Mother --}}
                <div class="pull-row">
                    <div class="fld">
                        <label>No. KP Ibu</label>
                        <input type="text" id="mother-ic" name="mother[ic]" class="mono" placeholder="000000-00-0000"
                               pattern="[0-9]{6}-[0-9]{2}-[0-9]{4}" required>
                    </div>
                    <button type="button" class="btn-pull" data-pull="mother">
                        <i data-lucide="scan-line" style="width:15px;height:15px;"></i> Dapatkan rekod
                    </button>
                </div>
                <p class="hint" style="margin-top:6px;">Cuba IC contoh: <button type="button" data-sample="mother" class="underline" style="color:var(--ai-indigo);background:none;border:none;cursor:pointer;padding:0;font:inherit;">920418-10-5566</button></p>

                <div class="pull-card" id="mother-card">
                    <div class="pc-head"><strong style="color:var(--ink-navy);font:700 14px/1 var(--font-sans);">Rekod Ibu</strong><span class="pc-badge" id="mother-conf">● Auto-isi</span></div>
                    <div class="pc-grid">
                        <div><div class="k">Nama Penuh</div><div class="v" id="mother-name-d">—</div></div>
                        <div><div class="k">Tarikh Lahir</div><div class="v" id="mother-dob-d">—</div></div>
                        <div style="grid-column:1/-1;"><div class="k">Alamat</div><div class="v" id="mother-addr-d">—</div></div>
                    </div>
                    <input type="hidden" name="mother[full_name]" id="mother-name">
                    <input type="hidden" name="mother[dob]" id="mother-dob">
                    <input type="hidden" name="mother[address]" id="mother-addr">
                    {{-- Fields not in registry record — keyed by applicant --}}
                    <div class="grid-3" style="margin-top:14px;">
                        <div class="fld"><label>Keturunan</label>@include('partials.opt-race', ['name' => 'mother[race]'])</div>
                        <div class="fld"><label>Agama</label>@include('partials.opt-religion', ['name' => 'mother[religion]'])</div>
                        <div class="fld"><label>Taraf Pemastautin</label>@include('partials.opt-resident', ['name' => 'mother[resident]'])</div>
                    </div>
                    <div class="grid-3">
                        <div class="fld"><label>Pekerjaan</label><input type="text" name="mother[occupation]" placeholder="cth. Guru"></div>
                        <div class="fld"><label>Pendidikan</label>@include('partials.opt-education', ['name' => 'mother[education]'])</div>
                        <div class="fld"><label>Taraf Perkahwinan</label>@include('partials.opt-marital', ['name' => 'mother[marital]'])</div>
                    </div>
                    <div class="fld" style="max-width:50%;"><label>Tarikh Perkahwinan</label><input type="date" name="mother[marriage_date]"></div>
                </div>

                {{-- Father --}}
                <div class="pull-row" style="margin-top:22px;">
                    <div class="fld">
                        <label>No. KP Bapa <span style="color:var(--ai-text-mute);font-weight:400;">(jika berkenaan)</span></label>
                        <input type="text" id="father-ic" name="father[ic]" class="mono" placeholder="000000-00-0000"
                               pattern="[0-9]{6}-[0-9]{2}-[0-9]{4}">
                    </div>
                    <button type="button" class="btn-pull" data-pull="father">
                        <i data-lucide="scan-line" style="width:15px;height:15px;"></i> Dapatkan rekod
                    </button>
                </div>
                <p class="hint" style="margin-top:6px;">Cuba IC contoh: <button type="button" data-sample="father" class="underline" style="color:var(--ai-indigo);background:none;border:none;cursor:pointer;padding:0;font:inherit;">761112-10-3285</button></p>

                <div class="pull-card" id="father-card">
                    <div class="pc-head"><strong style="color:var(--ink-navy);font:700 14px/1 var(--font-sans);">Rekod Bapa</strong><span class="pc-badge" id="father-conf">● Auto-isi</span></div>
                    <div class="pc-grid">
                        <div><div class="k">Nama Penuh</div><div class="v" id="father-name-d">—</div></div>
                        <div><div class="k">Tarikh Lahir</div><div class="v" id="father-dob-d">—</div></div>
                    </div>
                    <input type="hidden" name="father[full_name]" id="father-name">
                    <input type="hidden" name="father[dob]" id="father-dob">
                    <div class="grid-3" style="margin-top:14px;">
                        <div class="fld"><label>Keturunan</label>@include('partials.opt-race', ['name' => 'father[race]'])</div>
                        <div class="fld"><label>Agama</label>@include('partials.opt-religion', ['name' => 'father[religion]'])</div>
                        <div class="fld"><label>Taraf Pemastautin</label>@include('partials.opt-resident', ['name' => 'father[resident]'])</div>
                    </div>
                    <div class="grid-2">
                        <div class="fld"><label>Pekerjaan</label><input type="text" name="father[occupation]" placeholder="cth. Jurutera"></div>
                        <div class="fld"><label>Pendidikan</label>@include('partials.opt-education', ['name' => 'father[education]'])</div>
                    </div>
                    <div class="declare" style="margin-top:4px;">
                        <input type="checkbox" name="father[section13]" value="1" id="s13">
                        <p>Permohonan di bawah <strong>Seksyen 13</strong> — masukkan maklumat bapa walaupun ibu bapa tidak berkahwin (perlu pengesahan di hadapan Pendaftar).</p>
                    </div>
                </div>
            </div>

            {{-- STEP 2 — Maklumat Bayi (Bahagian A) --}}
            <div class="wiz-step" data-step="1">
                <p class="wiz-sec-title">Bahagian A · Maklumat Kanak-Kanak</p>
                <p class="wiz-sec-sub">Maklumat kelahiran bayi. Jika kelahiran di hospital, sebahagian medan boleh diisi dari notifikasi hospital (FHIR).</p>

                <button type="button" class="btn-pull" id="child-prefill" style="margin-bottom:18px;">
                    <i data-lucide="hospital" style="width:15px;height:15px;"></i> Isi data klinikal dari hospital (FHIR)
                </button>

                <div class="fld"><label>Nama Penuh Bayi</label><input type="text" name="child[full_name]" required placeholder="Nama penuh seperti akan didaftarkan"></div>

                <div class="grid-3">
                    <div class="fld"><label>Jantina</label>
                        <select name="child[sex]" required>
                            <option value="Lelaki">Lelaki</option>
                            <option value="Perempuan">Perempuan</option>
                            <option value="Ragu">Ragu</option>
                        </select>
                    </div>
                    <div class="fld"><label>Tarikh Kelahiran</label><input type="date" name="child[dob]" required></div>
                    <div class="fld"><label>Waktu Kelahiran</label><input type="time" name="child[born_time]"></div>
                </div>

                <div class="grid-3">
                    <div class="fld"><label>Sesi</label>
                        <select name="child[born_period]">
                            <option value="">—</option>
                            <option>Pagi</option><option>Tengah Hari</option><option>Petang</option><option>Malam</option><option>Tengah Malam</option>
                        </select>
                        <div class="hint">Tengah Hari/Malam = jam 12.00</div>
                    </div>
                    <div class="fld"><label>Berat Bayi (kg)</label><input type="text" name="child[weight_kg]" placeholder="cth. 3.20"></div>
                    <div class="fld"><label>Ukuran Bayi (cm)</label><input type="text" name="child[measure_cm]" placeholder="cth. 49"></div>
                </div>

                <div class="grid-2">
                    <div class="fld"><label>Tempat Kelahiran</label><input type="text" name="child[born_place]" placeholder="cth. Hospital Kuala Lumpur"></div>
                    <div class="fld"><label>Negeri Kelahiran</label>@include('partials.opt-state', ['name' => 'child[born_state]'])</div>
                </div>

                <div class="grid-2">
                    <div class="fld"><label>Keturunan</label>@include('partials.opt-race', ['name' => 'child[race]'])</div>
                    <div class="fld"><label>Agama</label>@include('partials.opt-religion', ['name' => 'child[religion]'])</div>
                </div>
            </div>

            {{-- STEP 3 — Penyambut Kelahiran (Bahagian B) --}}
            <div class="wiz-step" data-step="2">
                <p class="wiz-sec-title">Bahagian B · Penyambut Kelahiran</p>
                <p class="wiz-sec-sub">Orang yang menyambut kelahiran — doktor, jururawat, bidan, atau pihak hospital.</p>

                <button type="button" class="btn-pull" id="hosp-prefill" style="margin-bottom:18px;">
                    <i data-lucide="hospital" style="width:15px;height:15px;"></i> Isi dari notifikasi hospital (FHIR)
                </button>

                <div class="grid-2">
                    <div class="fld"><label>No. Dokumen Pengenalan</label><input type="text" name="deliverer[doc_no]" id="dlv-doc"></div>
                    <div class="fld"><label>Jenis Dokumen / Negara</label>
                        <select name="deliverer[doc_type]" id="dlv-type">
                            <option value="">—</option>
                            <option>MyKad · Malaysia</option><option>Pasport</option><option>Lain-lain</option>
                        </select>
                    </div>
                </div>
                <div class="fld"><label>Nama Penuh Penyambut / Pihak Hospital</label><input type="text" name="deliverer[full_name]" id="dlv-name" placeholder="cth. Dr. / Bidan / Hospital"></div>
            </div>

            {{-- STEP 4 — Pemberitahu (E) + Pengesahan (F/G) --}}
            <div class="wiz-step" data-step="3">
                <p class="wiz-sec-title">Bahagian E &amp; F · Pemberitahu &amp; Pengesahan</p>
                <p class="wiz-sec-sub">Siapa membuat permohonan ini, dan pengesahan maklumat.</p>

                <div class="fld">
                    <label>Hubungan Pemberitahu dengan Bayi</label>
                    <div class="relation-grid">
                        <div style="position:relative;"><input type="radio" name="informant[relation]" value="Ibu" id="rel-ibu" checked><label for="rel-ibu">Ibu</label></div>
                        <div style="position:relative;"><input type="radio" name="informant[relation]" value="Bapa" id="rel-bapa"><label for="rel-bapa">Bapa</label></div>
                        <div style="position:relative;"><input type="radio" name="informant[relation]" value="Lain-lain" id="rel-lain"><label for="rel-lain">Lain-lain</label></div>
                    </div>
                </div>

                <div id="informant-other" style="display:none;">
                    <div class="grid-2">
                        <div class="fld"><label>No. Dokumen Pengenalan Pemberitahu</label><input type="text" name="informant[ic]"></div>
                        <div class="fld"><label>Nama Penuh Pemberitahu</label><input type="text" name="informant[full_name]"></div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="fld"><label>No. Telefon</label><input type="text" name="confirm[phone]" placeholder="cth. 012-3456789"></div>
                    <div class="fld"><label>E-mel</label><input type="email" name="confirm[email]" placeholder="nama@contoh.com"></div>
                </div>

                <div class="declare">
                    <input type="checkbox" name="confirm[declared]" value="1" id="declared" required>
                    <p>Bahawasanya saya (Ibu/Bapa/Pemberitahu) dengan ini mengaku bersetuju dan bertanggungjawab terhadap segala maklumat yang diberikan dalam borang ini adalah betul dan benar.</p>
                </div>
            </div>

            {{-- STEP 5 — Semakan (Review) --}}
            <div class="wiz-step" data-step="4">
                <p class="wiz-sec-title">Semakan Akhir</p>
                <p class="wiz-sec-sub">Semak maklumat sebelum hantar. Status kewarganegaraan bayi akan ditentukan oleh Pendaftar.</p>
                <div id="review-out"></div>
                <div class="rev-block" style="border-color:var(--teal-700,#007D72);background:rgba(var(--brand-rgb),0.07);">
                    <div class="rev-grid"><div class="k">Bahagian I · Taraf Kewarganegaraan</div><div class="v">Ditentukan oleh Pendaftar</div></div>
                </div>
            </div>

            <div class="wiz-nav">
                <button type="button" class="btn-back" id="wiz-back" hidden>← Kembali</button>
                <div style="display:flex;align-items:center;gap:14px;margin-left:auto;">
                    <span class="text-xs" style="color:var(--ai-text-mute);">Langkah <span id="wiz-cur">1</span> / 5</span>
                    <button type="button" class="btn-gradient bounce text-base px-7 py-3" id="wiz-next">
                        Seterusnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <button type="submit" class="btn-gradient bounce text-base px-7 py-3" id="wiz-submit" style="display:none;">
                        Hantar Permohonan
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        </form>

        {{-- ============================================================= --}}
        {{-- MARRIAGE WIZARD (doc_type = marriage) — Akta 164 (non-Muslim)  --}}
        {{-- ============================================================= --}}
        <form action="{{ route('apply.store') }}" method="POST" id="marriage-form" novalidate
              class="glass-card-hi p-8 fade-up" style="animation-delay:120ms;display:none;">
            @csrf
            <input type="hidden" name="doc_type" value="marriage">

            <div class="wiz-progress">
                <div class="seg on" data-seg="0"></div>
                <div class="seg" data-seg="1"></div>
                <div class="seg" data-seg="2"></div>
            </div>

            {{-- STEP 1 — Pejabat + Pihak Lelaki (Bahagian A) --}}
            <div class="wiz-step active" data-step="0">
                <p class="wiz-sec-title">Pejabat Permohonan</p>
                <p class="wiz-sec-sub">Kepada Pendaftar / Penolong Pendaftar Perkahwinan di:</p>
                <div class="fld">
                    <label>Pejabat JPN Daerah</label>
                    <select name="office" required>
                        <option value="">— Pilih pejabat —</option>
                        @foreach (['JPN Daerah SPU · Seberang Perai Utara', 'JPN Daerah SPT · Seberang Perai Tengah', 'JPN Daerah Timur Laut · Pulau Pinang', 'JPN Negeri Pulau Pinang', 'JPN WP Kuala Lumpur', 'JPN Negeri Selangor'] as $o)
                            <option value="{{ $o }}">{{ $o }}</option>
                        @endforeach
                    </select>
                    <div class="hint">Perkahwinan sivil (Akta 164) — untuk bukan beragama Islam.</div>
                </div>

                <p class="wiz-sec-title" style="margin-top:22px;">Bahagian A · Maklumat Peribadi Lelaki</p>
                <p class="wiz-sec-sub">Masukkan No. KP — rekod ditarik automatik dari Pendaftaran Negara.</p>
                @include('partials.marriage-party', ['p' => 'male', 'title' => 'Lelaki', 'sample' => '900312-14-5021'])
            </div>

            {{-- STEP 2 — Pihak Perempuan (Bahagian B) --}}
            <div class="wiz-step" data-step="1">
                <p class="wiz-sec-title">Bahagian B · Maklumat Peribadi Perempuan</p>
                <p class="wiz-sec-sub">Masukkan No. KP — rekod ditarik automatik dari Pendaftaran Negara.</p>
                @include('partials.marriage-party', ['p' => 'female', 'title' => 'Perempuan', 'sample' => '920708-10-5566'])
            </div>

            {{-- STEP 3 — Semakan --}}
            <div class="wiz-step" data-step="2">
                <p class="wiz-sec-title">Semakan Akhir</p>
                <p class="wiz-sec-sub">Semak maklumat kedua-dua pihak sebelum hantar.</p>
                <div id="m-review-out"></div>
                <div class="declare" style="margin-top:8px;">
                    <input type="checkbox" name="confirm[declared]" value="1" id="m-declared" required>
                    <p>Kami memberitahu mengenai perkahwinan yang dicadangkan di antara kami, dan mengaku bahawa segala maklumat yang diperihal dalam borang ini adalah betul dan benar.</p>
                </div>
            </div>

            <div class="wiz-nav">
                <button type="button" class="btn-back" data-back hidden>← Kembali</button>
                <div style="display:flex;align-items:center;gap:14px;margin-left:auto;">
                    <span class="text-xs" style="color:var(--ai-text-mute);">Langkah <span data-cur>1</span> / 3</span>
                    <button type="button" class="btn-gradient bounce text-base px-7 py-3" data-next>
                        Seterusnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <button type="submit" class="btn-gradient bounce text-base px-7 py-3" data-submit style="display:none;">
                        Hantar Permohonan
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        </form>

        {{-- ============================================================= --}}
        {{-- MYKAD WIZARD (doc_type = mykad) — JPN.KP01                     --}}
        {{-- ============================================================= --}}
        <form action="{{ route('apply.store') }}" method="POST" id="mykad-form" novalidate
              class="glass-card-hi p-8 fade-up" style="animation-delay:120ms;display:none;">
            @csrf
            <input type="hidden" name="doc_type" value="mykad">

            <div class="wiz-progress">
                <div class="seg on" data-seg="0"></div>
                <div class="seg" data-seg="1"></div>
                <div class="seg" data-seg="2"></div>
            </div>

            {{-- STEP 1 — Maklumat Pemohon (Bahagian A + B) --}}
            <div class="wiz-step active" data-step="0">
                <p class="wiz-sec-title">Bahagian A &amp; B · Maklumat Pemohon</p>
                <p class="wiz-sec-sub">Masukkan No. KP pemohon — rekod ditarik automatik dari Pendaftaran Negara.</p>

                <div class="pull-row">
                    <div class="fld">
                        <label>No. KP Pemohon</label>
                        <input type="text" id="k-applicant-ic" name="applicant[ic]" class="mono" placeholder="000000-00-0000"
                               pattern="[0-9]{6}-[0-9]{2}-[0-9]{4}" required>
                    </div>
                    <button type="button" class="btn-pull" data-pullk="applicant">
                        <i data-lucide="scan-line" style="width:15px;height:15px;"></i> Dapatkan rekod
                    </button>
                </div>
                <p class="hint" style="margin-top:6px;">Cuba IC contoh: <button type="button" data-samplek="applicant" class="underline" style="color:var(--ai-indigo);background:none;border:none;cursor:pointer;padding:0;font:inherit;">900214-14-5588</button></p>

                <div class="pull-card" id="k-applicant-card">
                    <div class="pc-head"><strong style="color:var(--ink-navy);font:700 14px/1 var(--font-sans);">Rekod Pemohon</strong><span class="pc-badge" id="k-applicant-conf">● Auto-isi</span></div>
                    <div class="pc-grid">
                        <div><div class="k">Nama Penuh</div><div class="v" id="k-applicant-name-d">—</div></div>
                        <div><div class="k">Tarikh Lahir</div><div class="v" id="k-applicant-dob-d">—</div></div>
                        <div style="grid-column:1/-1;"><div class="k">Alamat</div><div class="v" id="k-applicant-addr-d">—</div></div>
                    </div>
                    <input type="hidden" name="applicant[full_name]" id="k-applicant-name">
                    <input type="hidden" name="applicant[dob]"       id="k-applicant-dob">
                    <input type="hidden" name="applicant[address]"   id="k-applicant-addr">

                    <div class="grid-3" style="margin-top:14px;">
                        <div class="fld"><label>Jantina</label>
                            <select name="applicant[sex]"><option value="">—</option><option>Lelaki</option><option>Perempuan</option></select>
                        </div>
                        <div class="fld"><label>No. Telefon</label><input type="text" name="applicant[phone]" placeholder="cth. 012-3456789"></div>
                        <div class="fld"><label>KP Sabah / Sarawak</label>
                            <select name="applicant[kp_borneo]"><option value="">— Tiada —</option><option>Sabah</option><option>Sarawak</option></select>
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="fld"><label>Poskod</label><input type="text" name="applicant[postcode]" id="k-applicant-postcode" class="mono" maxlength="5"></div>
                        <div class="fld"><label>Bandar</label><input type="text" name="applicant[city]" placeholder="cth. Kepala Batas"></div>
                        <div class="fld"><label>Negeri</label>@include('partials.opt-state', ['name' => 'applicant[state]'])</div>
                    </div>
                    <div class="grid-3">
                        <div class="fld"><label>Keturunan</label>@include('partials.opt-race', ['name' => 'applicant[race]'])</div>
                        <div class="fld"><label>Agama</label>@include('partials.opt-religion', ['name' => 'applicant[religion]'])</div>
                        <div class="fld"><label>Status Perkahwinan</label>@include('partials.opt-marital-civil', ['name' => 'applicant[marital]'])</div>
                    </div>
                    <div class="grid-2">
                        <div class="fld"><label>Negeri / Negara Kelahiran</label>@include('partials.opt-state', ['name' => 'applicant[birth_state]'])</div>
                        <div class="fld"><label>Jenis Dokumen Lain (jika bukan MyKid)</label><input type="text" name="applicant[other_doc]" placeholder="cth. Sijil Lahir"></div>
                    </div>
                </div>
            </div>

            {{-- STEP 2 — Penganjur + Pejabat + (opsyenal) Polis/Tentera & Imigresen --}}
            <div class="wiz-step" data-step="1">
                <p class="wiz-sec-title">Bahagian E · Penganjur / Ibu Bapa</p>
                <p class="wiz-sec-sub">Diisi jika pemohon bawah umur 12 tahun. Masukkan No. KP penganjur untuk auto-isi.</p>

                <div class="pull-row">
                    <div class="fld">
                        <label>No. KP Ibu Bapa / Penganjur <span style="color:var(--ai-text-mute);font-weight:400;">(jika berkenaan)</span></label>
                        <input type="text" id="k-guardian-ic" name="guardian[ic]" class="mono" placeholder="000000-00-0000" pattern="[0-9]{6}-[0-9]{2}-[0-9]{4}">
                    </div>
                    <button type="button" class="btn-pull" data-pullk="guardian">
                        <i data-lucide="scan-line" style="width:15px;height:15px;"></i> Dapatkan rekod
                    </button>
                </div>
                <p class="hint" style="margin-top:6px;">Cuba IC contoh: <button type="button" data-samplek="guardian" class="underline" style="color:var(--ai-indigo);background:none;border:none;cursor:pointer;padding:0;font:inherit;">761112-10-3285</button></p>

                <div class="pull-card" id="k-guardian-card">
                    <div class="pc-head"><strong style="color:var(--ink-navy);font:700 14px/1 var(--font-sans);">Rekod Penganjur</strong><span class="pc-badge" id="k-guardian-conf">● Auto-isi</span></div>
                    <input type="hidden" name="guardian[full_name]" id="k-guardian-name">
                    <div class="fld" style="margin-bottom:0;"><label>Nama Penuh Penganjur</label><div class="v" id="k-guardian-name-d" style="font:600 14px/1.3 var(--font-sans);color:var(--ai-text);">—</div></div>
                    <div class="fld" style="margin-top:12px;">
                        <label>Pertalian dengan Pemohon</label>
                        <div class="relation-grid">
                            <div style="position:relative;"><input type="radio" name="guardian[relation]" value="Ibu" id="k-rel-ibu"><label for="k-rel-ibu">Ibu</label></div>
                            <div style="position:relative;"><input type="radio" name="guardian[relation]" value="Bapa" id="k-rel-bapa"><label for="k-rel-bapa">Bapa</label></div>
                            <div style="position:relative;"><input type="radio" name="guardian[relation]" value="Penjaga" id="k-rel-jaga"><label for="k-rel-jaga">Penjaga</label></div>
                        </div>
                    </div>
                </div>

                <div class="fld" style="margin-top:22px;">
                    <label>Pejabat Kutipan MyKad</label>
                    <select name="office" required>
                        <option value="">— Pilih pejabat —</option>
                        @foreach (['JPN Daerah SPU · Seberang Perai Utara', 'JPN Daerah SPT · Seberang Perai Tengah', 'JPN Daerah Timur Laut · Pulau Pinang', 'JPN Negeri Pulau Pinang', 'JPN WP Kuala Lumpur', 'JPN Negeri Selangor'] as $o)
                            <option value="{{ $o }}">{{ $o }}</option>
                        @endforeach
                    </select>
                </div>

                <details style="margin-top:8px;">
                    <summary style="cursor:pointer;font:500 12.5px/1 var(--font-sans);color:var(--ai-text-mute,#7c8794);">Bahagian C · Maklumat Polis / Tentera (jika berkenaan)</summary>
                    <div class="grid-3" style="margin-top:12px;">
                        <div class="fld"><label>Perkhidmatan</label><select name="service[type]"><option value="">— Tiada —</option><option>Polis</option><option>Tentera</option></select></div>
                        <div class="fld"><label>No. Polis / Tentera</label><input type="text" name="service[no]"></div>
                        <div class="fld"><label>Tarikh Berhenti / Bersara</label><input type="date" name="service[retire_date]"></div>
                    </div>
                </details>

                <details style="margin-top:4px;">
                    <summary style="cursor:pointer;font:500 12.5px/1 var(--font-sans);color:var(--ai-text-mute,#7c8794);">Bahagian D · Maklumat Imigresen (bukan warganegara)</summary>
                    <div class="grid-2" style="margin-top:12px;">
                        <div class="fld"><label>Taraf Penduduk</label>@include('partials.opt-resident', ['name' => 'immigration[resident]'])</div>
                        <div class="fld"><label>No. Permit Masuk</label><input type="text" name="immigration[permit_no]"></div>
                    </div>
                    <div class="grid-2">
                        <div class="fld"><label>No. Pasport</label><input type="text" name="immigration[passport_no]"></div>
                        <div class="fld"><label>Negara Pengeluar Pasport</label><input type="text" name="immigration[passport_country]"></div>
                    </div>
                </details>
            </div>

            {{-- STEP 3 — Semakan --}}
            <div class="wiz-step" data-step="2">
                <p class="wiz-sec-title">Semakan Akhir</p>
                <p class="wiz-sec-sub">Semak maklumat sebelum hantar.</p>
                <div id="k-review-out"></div>
                <div class="declare" style="margin-top:8px;">
                    <input type="checkbox" name="confirm[declared]" value="1" id="k-declared" required>
                    <p>Dengan ini saya / penganjur mengaku bahawa segala maklumat yang diberikan dalam borang ini adalah benar dan betul.</p>
                </div>
            </div>

            <div class="wiz-nav">
                <button type="button" class="btn-back" data-back hidden>← Kembali</button>
                <div style="display:flex;align-items:center;gap:14px;margin-left:auto;">
                    <span class="text-xs" style="color:var(--ai-text-mute);">Langkah <span data-cur>1</span> / 3</span>
                    <button type="button" class="btn-gradient bounce text-base px-7 py-3" data-next>
                        Seterusnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <button type="submit" class="btn-gradient bounce text-base px-7 py-3" data-submit style="display:none;">
                        Hantar Permohonan
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        </form>
        </div>{{-- /left --}}

        {{-- Right: step guide (reflects wizard for birth) --}}
        <div class="form-aside">
            <div class="aside-eyebrow"><span class="eyebrow">Permohonan Pintar<span class="e-dot"></span></span></div>

            <div id="birth-guide" style="display:flex;flex-direction:column;gap:14px;">
                <div class="step-card is-active" data-guide="0"><div class="step-num">1</div><div><h4>Ibu &amp; Bapa</h4><p>Masukkan No. KP — rekod ditarik automatik dari Pendaftaran Negara.</p></div></div>
                <div class="step-card" data-guide="1"><div class="step-num">2</div><div><h4>Maklumat Bayi</h4><p>Nama, jantina, tarikh &amp; waktu lahir, berat, tempat kelahiran.</p></div></div>
                <div class="step-card" data-guide="2"><div class="step-num">3</div><div><h4>Penyambut Kelahiran</h4><p>Doktor / bidan / hospital — boleh auto-isi dari notifikasi hospital.</p></div></div>
                <div class="step-card" data-guide="3"><div class="step-num">4</div><div><h4>Pemberitahu &amp; Pengesahan</h4><p>Hubungan dengan bayi, hubungi, dan akuan kebenaran maklumat.</p></div></div>
                <div class="step-card" data-guide="4"><div class="step-num">5</div><div><h4>Semakan &amp; Hantar</h4><p>Semak semua maklumat, kemudian hantar permohonan.</p></div></div>
            </div>

            <div id="marriage-guide" style="display:none;flex-direction:column;gap:14px;">
                <div class="step-card is-active" data-guide="0"><div class="step-num">1</div><div><h4>Pihak Lelaki</h4><p>Pejabat JPN + No. KP lelaki — rekod ditarik dari Pendaftaran Negara.</p></div></div>
                <div class="step-card" data-guide="1"><div class="step-num">2</div><div><h4>Pihak Perempuan</h4><p>No. KP perempuan — nama, alamat, agama auto-isi dari rekod.</p></div></div>
                <div class="step-card" data-guide="2"><div class="step-num">3</div><div><h4>Semakan &amp; Hantar</h4><p>Semak maklumat kedua-dua pihak, akui kebenaran, hantar.</p></div></div>
            </div>

            <div id="mykad-guide" style="display:none;flex-direction:column;gap:14px;">
                <div class="step-card is-active" data-guide="0"><div class="step-num">1</div><div><h4>Maklumat Pemohon</h4><p>No. KP pemohon — nama, alamat, keturunan, agama auto-isi dari rekod.</p></div></div>
                <div class="step-card" data-guide="1"><div class="step-num">2</div><div><h4>Penganjur &amp; Pejabat</h4><p>Ibu bapa/penganjur (bawah umur), pejabat kutipan, polis/tentera &amp; imigresen jika berkenaan.</p></div></div>
                <div class="step-card" data-guide="2"><div class="step-num">3</div><div><h4>Semakan &amp; Hantar</h4><p>Semak maklumat, akui kebenaran, hantar permohonan.</p></div></div>
            </div>

            <div class="aside-note">
                <span class="ico"><i data-lucide="shield-check"></i></span>
                <div>
                    <div class="t">Data anda dilindungi</div>
                    <div class="d">Semua maklumat dikendalikan mengikut Akta Perlindungan Data Peribadi (PDPA) 2010.</div>
                </div>
            </div>
        </div>

        </div>{{-- /form-split --}}
    </div>
</section>

    @push('scripts')
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>(function(){var t=function(){window.lucide&&lucide.createIcons();};t();setInterval(t,800);})();</script>
    <script>
    (function() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const birthForm   = document.getElementById('birth-form');
        const marriageForm= document.getElementById('marriage-form');
        const mykadForm   = document.getElementById('mykad-form');
        // suppress native validation bubbles on submit — show pretty popup at first invalid field
        [birthForm, marriageForm, mykadForm].forEach(f=>{
            if(!f) return;
            f.addEventListener('submit', e=>{
                if (f.checkValidity()) return;
                e.preventDefault();
                const bad = f.querySelector(':invalid');
                if (!bad) return;
                const step = bad.closest('.wiz-step');
                if (step && !step.classList.contains('active')){
                    f.querySelectorAll('.wiz-step').forEach(s=>s.classList.remove('active'));
                    step.classList.add('active');
                }
                reportPretty(bad);
            });
        });
        const birthGuide  = document.getElementById('birth-guide');
        const marriageGuide=document.getElementById('marriage-guide');
        const mykadGuide  = document.getElementById('mykad-guide');

        // ---- Doc-type toggle (birth / marriage / mykad wizards) ----
        function applyDocType(){
            const v = document.querySelector('.doc-type-radio:checked')?.value || 'birth';
            const show = (form, guide, on) => { form.style.display = on?'':'none'; guide.style.display = on?'flex':'none'; };
            show(birthForm,    birthGuide,    v==='birth');
            show(marriageForm, marriageGuide, v==='marriage');
            show(mykadForm,    mykadGuide,    v==='mykad');
        }
        document.querySelectorAll('.doc-type-radio').forEach(r => r.addEventListener('change', applyDocType));
        applyDocType(); // sync on load (honours ?type= from controller)

        // ---- Registry pull (mother / father) via mock-OCR ----
        function fmtDate(iso){ if(!iso) return '—'; const p=iso.split('-'); return p.length===3?`${p[2]}/${p[1]}/${p[0]}`:iso; }
        function setSel(name,v){ const el=birthForm.querySelector(`[name="${name}"]`); if(el&&v!=null) el.value=v; }

        async function pull(who){
            const ic = document.getElementById(who+'-ic').value.trim();
            const card = document.getElementById(who+'-card');
            const conf = document.getElementById(who+'-conf');
            if (!/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/.test(ic)) {
                card.classList.add('show'); conf.textContent = '✕ Format IC tidak sah';
                return;
            }
            conf.textContent = '● Mengimbas…'; card.classList.add('show');
            try {
                const res = await fetch('/api/mock-ocr', {
                    method:'POST',
                    headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},
                    body: JSON.stringify({ ic }),
                });
                if (!res.ok) { conf.textContent = '✕ Tiada rekod'; return; }
                const j = await res.json();
                document.getElementById(who+'-name-d').textContent = j.data.full_name;
                document.getElementById(who+'-dob-d').textContent  = fmtDate(j.data.dob);
                document.getElementById(who+'-name').value = j.data.full_name;
                document.getElementById(who+'-dob').value  = j.data.dob;
                if (who === 'mother') {
                    document.getElementById('mother-addr-d').textContent = j.data.address;
                    document.getElementById('mother-addr').value = j.data.address;
                }
                // Auto-fill registry-derived selects (keturunan / agama / pemastautin)
                setSel(`${who}[race]`,     j.data.race);
                setSel(`${who}[religion]`, j.data.religion);
                setSel(`${who}[resident]`, j.data.resident);
                conf.textContent = `● ${j.processing_ms}ms · ${Math.round(j.confidence*100)}%`;
            } catch(e) { conf.textContent = '✕ Ralat'; }
        }
        document.querySelectorAll('[data-pull]').forEach(b => b.addEventListener('click', () => pull(b.dataset.pull)));
        document.querySelectorAll('[data-sample]').forEach(b => b.addEventListener('click', () => {
            const t = document.getElementById(b.dataset.sample+'-ic'); t.value = b.textContent.trim(); t.focus();
        }));

        // ---- Hospital prefill (Bahagian B) ----
        document.getElementById('hosp-prefill')?.addEventListener('click', () => {
            document.getElementById('dlv-doc').value  = 'KKM-FHIR-' + Math.floor(100000 + Math.random()*899999);
            document.getElementById('dlv-type').value = 'MyKad · Malaysia';
            document.getElementById('dlv-name').value = 'Hospital Kuala Lumpur (Jabatan O&G)';
        });

        // ---- Hospital clinical prefill (Bahagian A — child) ----
        document.getElementById('child-prefill')?.addEventListener('click', () => {
            setSel('child[sex]', 'Perempuan');
            const set = (n,v)=>{ const el=birthForm.querySelector(`[name="${n}"]`); if(el) el.value=v; };
            const today = new Date().toISOString().slice(0,10);
            set('child[dob]', today);
            set('child[born_time]', '03:14');
            setSel('child[born_period]', 'Pagi');
            set('child[weight_kg]', '3.20');
            set('child[measure_cm]', '49');
            set('child[born_place]', 'Hospital Kuala Lumpur');
            setSel('child[born_state]', 'W.P. Kuala Lumpur');
        });

        // ---- Informant "Lain-lain" toggle ----
        const other = document.getElementById('informant-other');
        document.querySelectorAll('input[name="informant[relation]"]').forEach(r =>
            r.addEventListener('change', () => { other.style.display = (r.value==='Lain-lain' && r.checked) ? 'block' : 'none'; }));

        // ---- Wizard step engine ----
        const steps = [...birthForm.querySelectorAll('.wiz-step')];
        const segs  = [...birthForm.querySelectorAll('.wiz-progress .seg')];
        const guides= [...birthGuide.querySelectorAll('[data-guide]')];
        const back  = document.getElementById('wiz-back');
        const next  = document.getElementById('wiz-next');
        const submit= document.getElementById('wiz-submit');
        const curEl = document.getElementById('wiz-cur');
        let cur = 0;
        const LAST = steps.length - 1;

        function show(i){
            steps.forEach((s,k)=>s.classList.toggle('active', k===i));
            segs.forEach((s,k)=>s.classList.toggle('on', k<=i));
            guides.forEach((g,k)=>{ g.classList.toggle('is-active', k===i); g.classList.toggle('is-done', k<i); });
            back.hidden = i===0;
            next.style.display  = i===LAST ? 'none' : '';
            submit.style.display= i===LAST ? '' : 'none';
            curEl.textContent = i+1;
            if (i===LAST) buildReview();
            birthForm.scrollIntoView({behavior:'smooth', block:'start'});
        }
        // pretty inline validation — styled popup instead of native browser bubble
        let _pf;
        function reportPretty(el){
            const cb = el.type === 'checkbox';
            const anchor = cb ? (el.closest('.declare') || el) : el;
            let msg = el.validationMessage;
            if (el.validity.valueMissing)            msg = cb ? 'Sila tanda kotak ini untuk teruskan.' : 'Medan ini wajib diisi.';
            else if (el.validity.patternMismatch)    msg = 'Format tidak sah. Contoh: 760101-10-1234';
            else if (el.validity.typeMismatch && el.type === 'email') msg = 'Alamat e-mel tidak sah.';
            if (!_pf){
                _pf = document.createElement('div'); _pf.className = 'pf-pop';
                _pf.innerHTML = '<span class="pf-ic">!</span><p class="pf-msg"></p>';
                document.body.appendChild(_pf);
            }
            _pf.querySelector('.pf-msg').textContent = msg;
            anchor.classList.remove('pf-bad'); void anchor.offsetWidth; anchor.classList.add('pf-bad');
            _pf.style.visibility = 'hidden'; _pf.classList.add('show');
            const r = anchor.getBoundingClientRect(), pw = _pf.offsetWidth, ph = _pf.offsetHeight;
            let top = r.top - ph - 10, pos = 'top';
            if (top < 8){ top = r.bottom + 10; pos = 'bottom'; }
            let left = Math.max(8, Math.min(r.left, window.innerWidth - pw - 8));
            _pf.dataset.pos = pos; _pf.style.top = top + 'px'; _pf.style.left = left + 'px';
            _pf.style.setProperty('--ax', Math.min(Math.max(r.left + 22 - left, 14), pw - 22) + 'px');
            _pf.style.visibility = '';
            const hide = ()=>{ _pf.classList.remove('show'); anchor.classList.remove('pf-bad');
                el.removeEventListener('input', hide); el.removeEventListener('change', hide);
                window.removeEventListener('scroll', hide, true); clearTimeout(_pf._t); };
            el.addEventListener('input', hide); el.addEventListener('change', hide);
            window.addEventListener('scroll', hide, true);
            clearTimeout(_pf._t); _pf._t = setTimeout(hide, 4500);
            try { el.focus({preventScroll:true}); } catch(e){ el.focus(); }
        }
        // native validity check for required fields in the current step only
        function stepValid(i){
            const reqs = steps[i].querySelectorAll('input[required],select[required],textarea[required]');
            for (const el of reqs){ if(!el.checkValidity()){ reportPretty(el); return false; } }
            return true;
        }
        next.addEventListener('click', ()=>{ if(stepValid(cur) && cur<LAST){ cur++; show(cur); } });
        back.addEventListener('click', ()=>{ if(cur>0){ cur--; show(cur); } });

        function val(name){ const el=birthForm.querySelector(`[name="${name}"]`); return el ? el.value : ''; }
        function buildReview(){
            const rows = (title, pairs) => {
                const cells = pairs.filter(p=>p[1]).map(p=>`<div class="k">${p[0]}</div><div class="v">${p[1]}</div>`).join('');
                if(!cells) return '';
                return `<div class="rev-block"><h5>${title}</h5><div class="rev-grid">${cells}</div></div>`;
            };
            const rel = birthForm.querySelector('input[name="informant[relation]"]:checked')?.value || '';
            document.getElementById('review-out').innerHTML =
                rows('Bahagian A · Bayi', [
                    ['Nama', val('child[full_name]')], ['Jantina', val('child[sex]')],
                    ['Tarikh Lahir', fmtDate(val('child[dob]'))], ['Waktu', val('child[born_time]')],
                    ['Tempat', val('child[born_place]')], ['Negeri', val('child[born_state]')],
                ]) +
                rows('Bahagian C · Ibu', [
                    ['Nama', val('mother[full_name]')], ['No. KP', val('mother[ic]')],
                    ['Keturunan', val('mother[race]')], ['Agama', val('mother[religion]')],
                ]) +
                rows('Bahagian D · Bapa', [
                    ['Nama', val('father[full_name]')], ['No. KP', val('father[ic]')],
                ]) +
                rows('Bahagian B · Penyambut', [
                    ['Nama', val('deliverer[full_name]')], ['No. Dok', val('deliverer[doc_no]')],
                ]) +
                rows('Bahagian E · Pemberitahu', [
                    ['Hubungan', rel], ['Telefon', val('confirm[phone]')], ['E-mel', val('confirm[email]')],
                ]);
        }

        // ============ MARRIAGE WIZARD ============
        // Registry pull for a party (male/female) — reuses mock-OCR.
        async function pullM(who){
            const ic = document.getElementById(who+'-ic').value.trim();
            const card = document.getElementById(who+'-card');
            const conf = document.getElementById(who+'-conf');
            if (!/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/.test(ic)) { card.classList.add('show'); conf.textContent='✕ Format IC tidak sah'; return; }
            conf.textContent='● Mengimbas…'; card.classList.add('show');
            try {
                const res = await fetch('/api/mock-ocr', {method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({ic})});
                if (!res.ok) { conf.textContent='✕ Tiada rekod'; return; }
                const j = await res.json();
                const sel = n => marriageForm.querySelector(`[name="${n}"]`);
                document.getElementById(who+'-name-d').textContent = j.data.full_name;
                document.getElementById(who+'-dob-d').textContent  = fmtDate(j.data.dob);
                document.getElementById(who+'-addr-d').textContent = j.data.address;
                document.getElementById(who+'-name').value = j.data.full_name;
                document.getElementById(who+'-dob').value  = j.data.dob;
                document.getElementById(who+'-addr').value = j.data.address;
                const pc = document.getElementById(who+'-postcode'); if (pc) pc.value = j.data.postcode || '';
                // Auto-populate every field except Pekerjaan (occupation).
                const fill = (k, v) => { const el = sel(`${who}[${k}]`); if (el && v != null) el.value = v; };
                fill('state',       j.data.state);
                fill('religion',    j.data.religion);
                fill('citizenship', j.data.citizenship || 'Malaysia');
                fill('father_name', j.data.father_name);
                fill('phone',       j.data.phone);
                fill('city',        j.data.city);
                fill('domicile',    j.data.domicile);
                fill('marital',     j.data.marital);
                conf.textContent = `● ${j.processing_ms}ms · ${Math.round(j.confidence*100)}%`;
            } catch(e) { conf.textContent='✕ Ralat'; }
        }
        document.querySelectorAll('[data-pullm]').forEach(b => b.addEventListener('click', ()=>pullM(b.dataset.pullm)));
        document.querySelectorAll('[data-samplem]').forEach(b => b.addEventListener('click', ()=>{
            const t=document.getElementById(b.dataset.samplem+'-ic'); t.value=b.textContent.trim(); t.focus();
        }));

        // Generic step engine (data-* driven) — used by the marriage wizard.
        function initWizard(form, guide, onReview){
            const steps = [...form.querySelectorAll('.wiz-step')];
            const segs  = [...form.querySelectorAll('.wiz-progress .seg')];
            const guides= [...guide.querySelectorAll('[data-guide]')];
            const back  = form.querySelector('[data-back]');
            const next  = form.querySelector('[data-next]');
            const submit= form.querySelector('[data-submit]');
            const curEl = form.querySelector('[data-cur]');
            const LAST = steps.length - 1; let cur = 0;
            function show(i){
                steps.forEach((s,k)=>s.classList.toggle('active',k===i));
                segs.forEach((s,k)=>s.classList.toggle('on',k<=i));
                guides.forEach((g,k)=>{ g.classList.toggle('is-active',k===i); g.classList.toggle('is-done',k<i); });
                back.hidden=i===0; next.style.display=i===LAST?'none':''; submit.style.display=i===LAST?'':'none';
                curEl.textContent=i+1; if(i===LAST && onReview) onReview(); form.scrollIntoView({behavior:'smooth',block:'start'});
            }
            function valid(i){ for(const el of steps[i].querySelectorAll('input[required],select[required],textarea[required]')){ if(!el.checkValidity()){ reportPretty(el); return false; } } return true; }
            next.addEventListener('click', ()=>{ if(valid(cur)&&cur<LAST){ cur++; show(cur); } });
            back.addEventListener('click', ()=>{ if(cur>0){ cur--; show(cur); } });
        }
        function mval(name){ const el=marriageForm.querySelector(`[name="${name}"]`); return el?el.value:''; }
        initWizard(marriageForm, marriageGuide, function(){
            const rows=(title,pairs)=>{ const c=pairs.filter(p=>p[1]).map(p=>`<div class="k">${p[0]}</div><div class="v">${p[1]}</div>`).join(''); return c?`<div class="rev-block"><h5>${title}</h5><div class="rev-grid">${c}</div></div>`:''; };
            document.getElementById('m-review-out').innerHTML =
                rows('Pejabat', [['Pendaftar di', mval('office')]]) +
                rows('Bahagian A · Lelaki', [['Nama', mval('male[full_name]')],['No. KP', mval('male[ic]')],['Nama Bapa', mval('male[father_name]')],['Agama', mval('male[religion]')],['Pekerjaan', mval('male[occupation]')],['Taraf', mval('male[marital]')]]) +
                rows('Bahagian B · Perempuan', [['Nama', mval('female[full_name]')],['No. KP', mval('female[ic]')],['Nama Bapa', mval('female[father_name]')],['Agama', mval('female[religion]')],['Pekerjaan', mval('female[occupation]')],['Taraf', mval('female[marital]')]]);
        });

        // ============ MYKAD WIZARD (JPN.KP01) ============
        async function pullK(who){
            const ic = document.getElementById('k-'+who+'-ic').value.trim();
            const card = document.getElementById('k-'+who+'-card');
            const conf = document.getElementById('k-'+who+'-conf');
            if (!/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/.test(ic)) { card.classList.add('show'); conf.textContent='✕ Format IC tidak sah'; return; }
            conf.textContent='● Mengimbas…'; card.classList.add('show');
            try {
                const res = await fetch('/api/mock-ocr', {method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({ic})});
                if (!res.ok) { conf.textContent='✕ Tiada rekod'; return; }
                const j = await res.json();
                const sel = n => mykadForm.querySelector(`[name="${n}"]`);
                document.getElementById('k-'+who+'-name-d').textContent = j.data.full_name;
                document.getElementById('k-'+who+'-name').value = j.data.full_name;
                if (who === 'applicant') {
                    document.getElementById('k-applicant-dob-d').textContent  = fmtDate(j.data.dob);
                    document.getElementById('k-applicant-addr-d').textContent = j.data.address;
                    document.getElementById('k-applicant-dob').value  = j.data.dob;
                    document.getElementById('k-applicant-addr').value = j.data.address;
                    const pc=document.getElementById('k-applicant-postcode'); if(pc) pc.value=j.data.postcode||'';
                    const fill=(k,v)=>{ const el=sel(`applicant[${k}]`); if(el&&v!=null) el.value=v; };
                    fill('sex',         j.data.gender === 'M' ? 'Lelaki' : (j.data.gender === 'F' ? 'Perempuan' : ''));
                    fill('phone',       j.data.phone);
                    fill('city',        j.data.city);
                    fill('state',       j.data.state);
                    fill('race',        j.data.race);
                    fill('religion',    j.data.religion);
                    fill('birth_state', j.data.state);
                    fill('marital',     j.data.marital);
                }
                conf.textContent = `● ${j.processing_ms}ms · ${Math.round(j.confidence*100)}%`;
            } catch(e) { conf.textContent='✕ Ralat'; }
        }
        document.querySelectorAll('[data-pullk]').forEach(b => b.addEventListener('click', ()=>pullK(b.dataset.pullk)));
        document.querySelectorAll('[data-samplek]').forEach(b => b.addEventListener('click', ()=>{
            const t=document.getElementById('k-'+b.dataset.samplek+'-ic'); t.value=b.textContent.trim(); t.focus();
        }));

        function kval(name){ const el=mykadForm.querySelector(`[name="${name}"]`); return el?el.value:''; }
        initWizard(mykadForm, mykadGuide, function(){
            const rel = mykadForm.querySelector('input[name="guardian[relation]"]:checked')?.value || '';
            const rows=(title,pairs)=>{ const c=pairs.filter(p=>p[1]).map(p=>`<div class="k">${p[0]}</div><div class="v">${p[1]}</div>`).join(''); return c?`<div class="rev-block"><h5>${title}</h5><div class="rev-grid">${c}</div></div>`:''; };
            document.getElementById('k-review-out').innerHTML =
                rows('Bahagian A & B · Pemohon', [['Nama', kval('applicant[full_name]')],['No. KP', kval('applicant[ic]')],['Jantina', kval('applicant[sex]')],['Keturunan', kval('applicant[race]')],['Agama', kval('applicant[religion]')],['Negeri', kval('applicant[state]')]]) +
                rows('Bahagian E · Penganjur', [['Nama', kval('guardian[full_name]')],['No. KP', kval('guardian[ic]')],['Pertalian', rel]]) +
                rows('Pejabat Kutipan', [['Pejabat', kval('office')]]);
        });
    })();
    </script>
    @endpush
@endsection
