@extends('layouts.system', ['active' => 'wizard', 'title' => 'Pendaftaran Kelahiran Baru'])

@section('content')
@php
    $steps = [
        ['label' => 'Sumber', 'name' => 'Hospital / Klinik'],
        ['label' => 'Pemohon', 'name' => 'Maklumat Ibu'],
        ['label' => 'Bayi', 'name' => 'Maklumat Bayi'],
        ['label' => 'Ayah', 'name' => 'Maklumat Ayah'],
        ['label' => 'Sahkan', 'name' => 'Semak & Hantar'],
    ];
@endphp

<div class="wiz-head">
    <div class="wiz-head__eyebrow">Pendaftaran Baru</div>
    <h1 class="wiz-head__h1">Pendaftaran Sijil Kelahiran</h1>
    <p class="wiz-head__sub">
        Daftar permohonan sijil kelahiran baru bagi pihak hospital atau klinik. Lengkapkan 5 langkah berikut.
        Data dihantar terus ke saluran tapisan untuk semakan pegawai.
    </p>
</div>

<div class="wiz-rail" id="wizRail">
    @foreach($steps as $i => $step)
        <div class="wiz-step {{ $i === 0 ? 'wiz-step--current' : '' }}" data-step="{{ $i + 1 }}">
            <div class="wiz-step__num">{{ $i + 1 }}</div>
            <div class="wiz-step__body">
                <div class="wiz-step__label">{{ $step['label'] }}</div>
                <div class="wiz-step__name">{{ $step['name'] }}</div>
            </div>
        </div>
        @if($i < count($steps) - 1)
            <div class="wiz-step__connector"></div>
        @endif
    @endforeach
</div>

<form method="POST" action="{{ route('system.wizard.store') }}" id="wizForm" autocomplete="off">
    @csrf

    <div class="wiz-body">

        {{-- ============ STEP 1: Hospital / Klinik ============ --}}
        <div class="wiz-panel is-active" data-panel="1">
            <div class="wiz-panel__head">
                <div class="wiz-panel__eyebrow">Langkah 1 / 5</div>
                <h2 class="wiz-panel__h">Sumber Pendaftaran<span class="dot"></span></h2>
                <p class="wiz-panel__sub">Maklumat hospital atau klinik yang mengeluarkan rekod kelahiran.</p>
            </div>

            <div class="wiz-grid">
                <div class="wiz-field">
                    <label class="wiz-field__label">Kod Hospital / Klinik</label>
                    <input type="text" name="hospital_code" class="wiz-field__input" placeholder="HKL-KL-001" value="HKL-KL-001" required>
                    <span class="wiz-field__hint">Format: kategori-bandar-nombor</span>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Nama Pegawai Perubatan</label>
                    <input type="text" name="medical_officer" class="wiz-field__input" placeholder="Dr. Ahmad bin Hassan" value="Dr. Ahmad bin Hassan" required>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Tarikh Pengeluaran Rekod</label>
                    <input type="date" name="date_issued" class="wiz-field__input" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Nombor Rujukan Hospital</label>
                    <input type="text" name="hospital_ref" class="wiz-field__input" placeholder="HOSP-{{ now()->format('Ym') }}-XXXX" value="HOSP-{{ now()->format('Ym') }}-{{ rand(1000, 9999) }}">
                </div>
            </div>
        </div>

        {{-- ============ STEP 2: Ibu ============ --}}
        <div class="wiz-panel" data-panel="2">
            <div class="wiz-panel__head">
                <div class="wiz-panel__eyebrow">Langkah 2 / 5</div>
                <h2 class="wiz-panel__h">Maklumat Ibu<span class="dot"></span></h2>
                <p class="wiz-panel__sub">Butiran identiti ibu kandung sebagaimana dalam Kad Pengenalan.</p>
            </div>

            <div class="wiz-grid">
                <div class="wiz-field wiz-field--span-2">
                    <label class="wiz-field__label">Nama Penuh Ibu</label>
                    <input type="text" name="mother_name" class="wiz-field__input" placeholder="Aisyah binti Othman" value="Aisyah binti Othman" required>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">No. Kad Pengenalan</label>
                    <input type="text" name="mother_ic" class="wiz-field__input" placeholder="900215-14-5678" value="900215-14-5678" required>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Kewarganegaraan</label>
                    <select name="mother_nationality" class="wiz-field__select">
                        <option>Malaysia</option>
                        <option>Indonesia</option>
                        <option>Filipina</option>
                        <option>Lain-lain</option>
                    </select>
                </div>
                <div class="wiz-field wiz-field--span-2">
                    <label class="wiz-field__label">Alamat Tetap</label>
                    <textarea name="mother_address" class="wiz-field__textarea" required>No. 23, Jalan Bunga Raya, Taman Sentosa, 50450 Kuala Lumpur</textarea>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">No. Telefon</label>
                    <input type="text" name="mother_phone" class="wiz-field__input" placeholder="+60 12-345 6789" value="+60 12-345 6789">
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Emel</label>
                    <input type="email" name="mother_email" class="wiz-field__input" placeholder="aisyah@example.com">
                </div>
            </div>
        </div>

        {{-- ============ STEP 3: Bayi ============ --}}
        <div class="wiz-panel" data-panel="3">
            <div class="wiz-panel__head">
                <div class="wiz-panel__eyebrow">Langkah 3 / 5</div>
                <h2 class="wiz-panel__h">Maklumat Bayi<span class="dot"></span></h2>
                <p class="wiz-panel__sub">Butiran bayi baru lahir untuk pendaftaran.</p>
            </div>

            <div class="wiz-grid">
                <div class="wiz-field wiz-field--span-2">
                    <label class="wiz-field__label">Nama Penuh Bayi</label>
                    <input type="text" name="baby_name" class="wiz-field__input" placeholder="Muhammad Adam bin Razak" value="Muhammad Adam bin Razak" required>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Tarikh Lahir</label>
                    <input type="date" name="baby_dob" class="wiz-field__input" value="{{ now()->subDays(2)->format('Y-m-d') }}" required>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Masa Lahir</label>
                    <input type="time" name="baby_time" class="wiz-field__input" value="08:42">
                </div>
                <div class="wiz-field wiz-field--span-2">
                    <label class="wiz-field__label">Jantina</label>
                    <div class="wiz-radio">
                        <label><input type="radio" name="baby_gender" value="M" checked> Lelaki</label>
                        <label><input type="radio" name="baby_gender" value="F"> Perempuan</label>
                    </div>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Berat Lahir (kg)</label>
                    <input type="text" name="baby_weight" class="wiz-field__input" placeholder="3.2" value="3.2">
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Bilangan Anak</label>
                    <select name="baby_birth_order" class="wiz-field__select">
                        <option>Anak Pertama</option>
                        <option>Anak Kedua</option>
                        <option>Anak Ketiga</option>
                        <option>Anak Keempat dan ke atas</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ============ STEP 4: Ayah ============ --}}
        <div class="wiz-panel" data-panel="4">
            <div class="wiz-panel__head">
                <div class="wiz-panel__eyebrow">Langkah 4 / 5</div>
                <h2 class="wiz-panel__h">Maklumat Ayah<span class="dot"></span></h2>
                <p class="wiz-panel__sub">Butiran bapa kandung. Pilihan jika tidak berkenaan.</p>
            </div>

            <div class="wiz-grid">
                <div class="wiz-field wiz-field--span-2">
                    <label class="wiz-field__label">Nama Penuh Ayah</label>
                    <input type="text" name="father_name" class="wiz-field__input" placeholder="Razak bin Ibrahim" value="Razak bin Ibrahim">
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">No. Kad Pengenalan</label>
                    <input type="text" name="father_ic" class="wiz-field__input" placeholder="850315-14-1234" value="850315-14-1234">
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Kewarganegaraan</label>
                    <select name="father_nationality" class="wiz-field__select">
                        <option>Malaysia</option>
                        <option>Indonesia</option>
                        <option>Filipina</option>
                        <option>Lain-lain</option>
                    </select>
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">Pekerjaan</label>
                    <input type="text" name="father_occupation" class="wiz-field__input" placeholder="Jurutera" value="Jurutera">
                </div>
                <div class="wiz-field">
                    <label class="wiz-field__label">No. Telefon</label>
                    <input type="text" name="father_phone" class="wiz-field__input" placeholder="+60 13-987 6543">
                </div>
            </div>
        </div>

        {{-- ============ STEP 5: Sahkan ============ --}}
        <div class="wiz-panel" data-panel="5">
            <div class="wiz-panel__head">
                <div class="wiz-panel__eyebrow">Langkah 5 / 5</div>
                <h2 class="wiz-panel__h">Semak & Hantar<span class="dot"></span></h2>
                <p class="wiz-panel__sub">Sila semak semua maklumat sebelum hantar untuk tapisan pegawai.</p>
            </div>

            <div class="wiz-summary" id="wizSummary">
                <div class="wiz-summary__card">
                    <div class="wiz-summary__head">
                        <span class="wiz-summary__title">Sumber</span>
                        <button type="button" class="wiz-summary__edit" data-goto="1">Ubah →</button>
                    </div>
                    <div class="wiz-summary__row"><span class="k">Hospital</span><span class="v mono" data-summary="hospital_code">—</span></div>
                    <div class="wiz-summary__row"><span class="k">Pegawai MO</span><span class="v" data-summary="medical_officer">—</span></div>
                    <div class="wiz-summary__row"><span class="k">Tarikh</span><span class="v" data-summary="date_issued">—</span></div>
                </div>

                <div class="wiz-summary__card">
                    <div class="wiz-summary__head">
                        <span class="wiz-summary__title">Ibu</span>
                        <button type="button" class="wiz-summary__edit" data-goto="2">Ubah →</button>
                    </div>
                    <div class="wiz-summary__row"><span class="k">Nama</span><span class="v" data-summary="mother_name">—</span></div>
                    <div class="wiz-summary__row"><span class="k">No. KP</span><span class="v mono" data-summary="mother_ic">—</span></div>
                    <div class="wiz-summary__row"><span class="k">Alamat</span><span class="v" data-summary="mother_address">—</span></div>
                </div>

                <div class="wiz-summary__card">
                    <div class="wiz-summary__head">
                        <span class="wiz-summary__title">Bayi</span>
                        <button type="button" class="wiz-summary__edit" data-goto="3">Ubah →</button>
                    </div>
                    <div class="wiz-summary__row"><span class="k">Nama</span><span class="v" data-summary="baby_name">—</span></div>
                    <div class="wiz-summary__row"><span class="k">Tarikh Lahir</span><span class="v" data-summary="baby_dob">—</span></div>
                    <div class="wiz-summary__row"><span class="k">Jantina</span><span class="v" data-summary="baby_gender">—</span></div>
                    <div class="wiz-summary__row"><span class="k">Berat</span><span class="v" data-summary="baby_weight">—</span> kg</div>
                </div>

                <div class="wiz-summary__card">
                    <div class="wiz-summary__head">
                        <span class="wiz-summary__title">Ayah</span>
                        <button type="button" class="wiz-summary__edit" data-goto="4">Ubah →</button>
                    </div>
                    <div class="wiz-summary__row"><span class="k">Nama</span><span class="v" data-summary="father_name">—</span></div>
                    <div class="wiz-summary__row"><span class="k">No. KP</span><span class="v mono" data-summary="father_ic">—</span></div>
                </div>
            </div>

            <label class="wiz-consent">
                <input type="checkbox" id="wizConsent" required>
                <span>
                    Saya mengesahkan <strong>semua maklumat adalah benar dan tepat</strong>. Pengakuan palsu adalah kesalahan di bawah Akta Pendaftaran Kelahiran dan Kematian 1957. Audit log akan merekod pendaftaran ini di bawah akaun saya.
                </span>
            </label>
        </div>

        {{-- ============ ACTIONS ============ --}}
        <div class="wiz-actions">
            <div class="wiz-actions__progress">
                LANGKAH <strong id="wizProgressNum">1</strong> / 5
            </div>
            <div class="wiz-actions__btns">
                <button type="button" class="wiz-btn wiz-btn--back" id="wizBack" style="display:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m11 18-6-6 6-6"/></svg>
                    Kembali
                </button>
                <button type="button" class="wiz-btn wiz-btn--next" id="wizNext">
                    Teruskan
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                </button>
                <button type="submit" class="wiz-btn wiz-btn--submit" id="wizSubmit" style="display:none;" disabled>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    Hantar Pendaftaran
                </button>
            </div>
        </div>

    </div>
</form>

<script>
    const wizTotal = 5;
    let wizCurrent = 1;

    const wizStepsEls = document.querySelectorAll('.wiz-step');
    const wizPanels = document.querySelectorAll('.wiz-panel');
    const wizBack = document.getElementById('wizBack');
    const wizNext = document.getElementById('wizNext');
    const wizSubmit = document.getElementById('wizSubmit');
    const wizProgress = document.getElementById('wizProgressNum');
    const wizConsent = document.getElementById('wizConsent');
    const wizForm = document.getElementById('wizForm');

    function wizRender() {
        wizStepsEls.forEach((el, i) => {
            const step = i + 1;
            el.classList.remove('wiz-step--current', 'wiz-step--done');
            if (step < wizCurrent) el.classList.add('wiz-step--done');
            else if (step === wizCurrent) el.classList.add('wiz-step--current');
        });
        wizPanels.forEach(p => p.classList.toggle('is-active', Number(p.dataset.panel) === wizCurrent));
        wizBack.style.display = wizCurrent > 1 ? 'inline-flex' : 'none';
        wizNext.style.display = wizCurrent < wizTotal ? 'inline-flex' : 'none';
        wizSubmit.style.display = wizCurrent === wizTotal ? 'inline-flex' : 'none';
        wizProgress.textContent = wizCurrent;

        if (wizCurrent === wizTotal) wizPopulateSummary();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function wizValidateCurrent() {
        const panel = document.querySelector(`.wiz-panel[data-panel="${wizCurrent}"]`);
        if (!panel) return true;
        const required = panel.querySelectorAll('[required]');
        for (const f of required) {
            if (!f.value.trim()) {
                f.focus();
                f.style.borderColor = 'var(--danger)';
                setTimeout(() => f.style.borderColor = '', 1600);
                return false;
            }
        }
        return true;
    }

    function wizPopulateSummary() {
        document.querySelectorAll('[data-summary]').forEach(el => {
            const name = el.dataset.summary;
            const input = wizForm.querySelector(`[name="${name}"]`);
            let val = input?.value || '—';
            if (name === 'baby_gender') {
                const checked = wizForm.querySelector('[name="baby_gender"]:checked');
                val = checked?.value === 'M' ? 'Lelaki' : 'Perempuan';
            }
            el.textContent = val || '—';
        });
    }

    wizNext.addEventListener('click', () => {
        if (!wizValidateCurrent()) return;
        wizCurrent = Math.min(wizTotal, wizCurrent + 1);
        wizRender();
    });

    wizBack.addEventListener('click', () => {
        wizCurrent = Math.max(1, wizCurrent - 1);
        wizRender();
    });

    document.querySelectorAll('.wiz-summary__edit').forEach(btn => {
        btn.addEventListener('click', () => {
            wizCurrent = Number(btn.dataset.goto);
            wizRender();
        });
    });

    wizConsent.addEventListener('change', () => {
        wizSubmit.disabled = !wizConsent.checked;
    });

    wizRender();
</script>
@endsection
