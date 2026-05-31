{{-- Marriage party section (mirror used for male A + female B).
     Vars: $p (prefix 'male'|'female'), $title, $sample (sample IC) --}}
<div class="pull-row">
    <div class="fld">
        <label>No. Kad Pengenalan {{ $title }}</label>
        <input type="text" id="{{ $p }}-ic" name="{{ $p }}[ic]" class="mono" placeholder="000000-00-0000"
               pattern="[0-9]{6}-[0-9]{2}-[0-9]{4}" required>
    </div>
    <button type="button" class="btn-pull" data-pullm="{{ $p }}">
        <i data-lucide="scan-line" style="width:15px;height:15px;"></i> Dapatkan rekod
    </button>
</div>
<p class="hint" style="margin-top:6px;">Cuba IC contoh:
    <button type="button" data-samplem="{{ $p }}" class="underline" style="color:var(--ai-indigo);background:none;border:none;cursor:pointer;padding:0;font:inherit;">{{ $sample }}</button>
</p>

<div class="pull-card" id="{{ $p }}-card">
    <div class="pc-head">
        <strong style="color:var(--ink-navy);font:700 14px/1 var(--font-sans);">Rekod {{ $title }}</strong>
        <span class="pc-badge" id="{{ $p }}-conf">● Auto-isi</span>
    </div>
    <div class="pc-grid">
        <div><div class="k">Nama Penuh</div><div class="v" id="{{ $p }}-name-d">—</div></div>
        <div><div class="k">Tarikh Lahir</div><div class="v" id="{{ $p }}-dob-d">—</div></div>
        <div style="grid-column:1/-1;"><div class="k">Alamat</div><div class="v" id="{{ $p }}-addr-d">—</div></div>
    </div>
    <input type="hidden" name="{{ $p }}[full_name]" id="{{ $p }}-name">
    <input type="hidden" name="{{ $p }}[dob]"       id="{{ $p }}-dob">
    <input type="hidden" name="{{ $p }}[address]"   id="{{ $p }}-addr">

    <div class="grid-2" style="margin-top:14px;">
        <div class="fld"><label>Nama Bapa</label><input type="text" name="{{ $p }}[father_name]" placeholder="Nama penuh bapa"></div>
        <div class="fld"><label>No. Telefon</label><input type="text" name="{{ $p }}[phone]" placeholder="cth. 012-3456789"></div>
    </div>
    <div class="grid-3">
        <div class="fld"><label>Poskod</label><input type="text" name="{{ $p }}[postcode]" id="{{ $p }}-postcode" class="mono" maxlength="5"></div>
        <div class="fld"><label>Bandar</label><input type="text" name="{{ $p }}[city]" placeholder="cth. Kepala Batas"></div>
        <div class="fld"><label>Negeri</label>@include('partials.opt-state', ['name' => $p.'[state]'])</div>
    </div>
    <div class="grid-3">
        <div class="fld"><label>Warganegara</label><input type="text" name="{{ $p }}[citizenship]" value="Malaysia"></div>
        <div class="fld"><label>Negara Domisil</label><input type="text" name="{{ $p }}[domicile]" placeholder="Malaysia"></div>
        <div class="fld"><label>Agama</label>@include('partials.opt-religion-civil', ['name' => $p.'[religion]'])</div>
    </div>
    <div class="grid-2">
        <div class="fld"><label>Pekerjaan</label><input type="text" name="{{ $p }}[occupation]" placeholder="cth. Jurutera"></div>
        <div class="fld"><label>Taraf Perkahwinan</label>@include('partials.opt-marital-civil', ['name' => $p.'[marital]'])</div>
    </div>

    <details style="margin-top:6px;">
        <summary style="cursor:pointer;font:500 12.5px/1 var(--font-sans);color:var(--ai-text-mute,#7c8794);">Dokumen pengenalan lain (jika bukan MyKad)</summary>
        <div class="grid-3" style="margin-top:12px;">
            <div class="fld"><label>No. Dokumen Lain</label><input type="text" name="{{ $p }}[other_doc_no]"></div>
            <div class="fld"><label>Jenis Dokumen</label><input type="text" name="{{ $p }}[other_doc_type]" placeholder="cth. Pasport"></div>
            <div class="fld"><label>Negara Pengeluar</label><input type="text" name="{{ $p }}[other_doc_country]"></div>
        </div>
    </details>
</div>
