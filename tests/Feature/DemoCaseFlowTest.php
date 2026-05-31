<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoCaseFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function officer(): User { return User::where('email', 'demo@jpn.gov.my')->firstOrFail(); }
    private function supervisor(): User { return User::where('email', 'nurul@jpn.gov.my')->firstOrFail(); }
    private function admin(): User { return User::where('email', 'ibrahim@jpn.gov.my')->firstOrFail(); }

    public function test_mykad_anchor_threads_through_officer_screens(): void
    {
        $this->actingAs($this->officer());

        $this->get('/system/tapisan/APP-20260528-7001')->assertOk()->assertSee('Lim Pei Shan');

        $this->get('/system/biometric-capture?case=mykad')->assertOk()
            ->assertSee('Lim Pei Shan')->assertSee('Kes Semasa')
            ->assertDontSee('Proses Selesai'); // not the last step

        $this->get('/system/abis-match?case=mykad')->assertOk()
            ->assertSee('Lim Pei Shan')->assertSee('Padanan 1:N Semasa')->assertSee('KES SEMASA')
            ->assertDontSee('Encik Arjun')->assertDontSee('Raj Kumar');

        $this->get('/system/clms-pipeline?case=mykad')->assertOk()
            ->assertSee('Lim Pei Shan')->assertSee('MK-2026-770001')
            ->assertDontSee('Anand Singh')->assertDontSee('Dr. Ahmad Hisham');
    }

    public function test_birth_anchor_threads(): void
    {
        $this->actingAs($this->officer());

        $this->get('/system/hospital-pra-daftar?case=birth')->assertOk()
            ->assertSee('Farah Nadia binti Salleh')->assertSee('Kes Semasa')
            ->assertDontSee('Siti Aisyah binti Hisham')->assertDontSee('Anita anak Joseph');

        $this->get('/system/family-tree?case=birth')->assertOk()
            ->assertSee('Nur Sofia binti Amir Hamzah');

        // Borang Pendaftaran (online form, hospital-prefilled)
        $this->get('/system/borang-kelahiran?case=birth')->assertOk()
            ->assertSee('JPN.LM01')->assertSee('Nur Sofia binti Amir Hamzah')
            ->assertSee('Farah Nadia binti Salleh')->assertSee('DALAM TALIAN');

        // Sijil Kelahiran (LM05 + MyKid) — branches to birth certificate
        $this->get('/system/sijil?case=birth')->assertOk()
            ->assertSee('Sijil Kelahiran')->assertSee('JPN.LM05-2026-770002')
            ->assertSee('MYKID-2026-770002')->assertSee('Nur Sofia binti Amir Hamzah');
    }

    public function test_civil_marriage_anchor_uses_non_malay_names(): void
    {
        $this->actingAs($this->officer());

        // Detail page shows BOTH parties, not one person
        $this->get('/system/tapisan/APP-20260528-7003')->assertOk()
            ->assertSee('Daniel Tan Wei Jie')->assertSee('Sarah Pereira');

        $this->get('/system/kaveat-board?case=marriage')->assertOk()
            ->assertSee('Daniel Tan Wei Jie')->assertSee('Sarah Pereira')
            ->assertDontSee('Ahmad bin Hisham')->assertDontSee('Michael Tan Boon Hwa');

        $this->get('/system/family-tree?case=marriage')->assertOk()
            ->assertSee('Daniel Tan Wei Jie')->assertSee('Sarah Pereira')
            ->assertSee('LAYAK BERKAHWIN')->assertSee('Halangan Perkahwinan')
            ->assertSee('880905-14-5071'); // node shows IC detail

        // Upacara (solemnization) — couple + 2 saksi + venue (rumah ibadat) + blockchain passthrough
        $this->get('/system/upacara-perkahwinan?case=marriage')->assertOk()
            ->assertSee('Daniel Tan Wei Jie')->assertSee('Sarah Pereira')
            ->assertSee('PK-2026-770003')->assertSee('Michael Tan Wei Han')
            ->assertSee('2 Saksi')->assertSee('Gereja St. John')
            ->assertSee('Jana Sijil Perkahwinan');

        // Sijil — issued after blockchain passthrough
        $this->get('/system/sijil?case=marriage')->assertOk()
            ->assertSee('Daniel Tan Wei Jie')->assertSee('Sarah Pereira')
            ->assertSee('JPN.KC02-2026-770003')->assertSee('SUAMI ISTERI SAH');
    }

    public function test_marriage_applications_are_non_muslim_couples(): void
    {
        // Civil marriage (Akta 164) is non-Muslim only — no Malay/Muslim bin/binti names,
        // and every marriage application must carry a spouse (couple).
        $marriages = \App\Models\Application::where('doc_type', 'marriage')->get();
        $this->assertNotEmpty($marriages);
        foreach ($marriages as $m) {
            $this->assertStringNotContainsStringIgnoringCase(' bin ', ' ' . $m->applicant_name . ' ');
            $this->assertStringNotContainsStringIgnoringCase(' binti ', ' ' . $m->applicant_name . ' ');
            $this->assertNotNull($m->spouse_name, "Marriage {$m->reference_number} missing spouse");
        }

        // Anchor detail shows both parties with civil status
        $this->actingAs($this->officer());
        $this->get('/system/tapisan/APP-20260528-7003')->assertOk()
            ->assertSee('Daniel Tan Wei Jie')->assertSee('Sarah Pereira')->assertSee('Bujang');

        // A non-anchor marriage app must thread real data — no "rujuk borang" placeholders
        $other = $marriages->firstWhere('reference_number', '!=', 'APP-20260528-7003');
        $this->get('/system/upacara-perkahwinan?ref=' . $other->reference_number)->assertOk()
            ->assertDontSee('rujuk borang')
            ->assertSee($other->spouse_name);
    }

    public function test_supervisor_and_admin_screens_thread(): void
    {
        $this->actingAs($this->supervisor());
        $this->get('/system/blockchain-ledger?case=mykad')->assertOk()
            ->assertSee('MK-2026-770001')->assertSee('Kes Semasa')
            ->assertDontSee('PK-2026-009876')->assertDontSee('KMT-2026-004412');
        $this->get('/system/agensi-integrasi?case=mykad')->assertOk()
            ->assertSee('Lim Pei Shan');

        $this->actingAs($this->admin());
        $this->get('/system/mydigital-id?case=mykad')->assertOk()
            ->assertSee('Lim Pei Shan')->assertSee('Kes Semasa')
            ->assertDontSee('Anand Singh')->assertDontSee('John Tan Wei Ming')
            ->assertSee('Proses Selesai'); // mydigital = last step for mykad
    }

    public function test_experience_features_render(): void
    {
        $this->actingAs($this->officer());

        // Interactive biometric: scan controls present
        $this->get('/system/biometric-capture?case=mykad')->assertOk()
            ->assertSee('Imbas Semua')->assertSee('sampel');

        // Forensic audit: human labels + IP/hash detail, not raw "stage_advanced" only
        $this->get('/system/audit')->assertOk()
            ->assertSee('Jejak Forensik')
            ->assertSee('Alamat IP')
            ->assertSee('Integriti Rekod');

        // Tugasan Saya (officer kanban): demo anchor cards flagged for the evaluator
        $this->get('/system/kanban')->assertOk()
            ->assertSee('CONTOH')->assertSee('Lim Pei Shan');
    }

    public function test_submodule_browse_shows_everyone_threaded_shows_one(): void
    {
        $this->actingAs($this->officer());

        // Browse (sidebar, no ?ref/?case): full queue — everyone — and NO case rail.
        $this->get('/system/clms-pipeline')->assertOk()
            ->assertSee('Arjun a/l Subramaniam')
            ->assertSee('Anand Singh')
            ->assertDontSee('Kes Semasa');

        // Threaded (arrived via Semak / ?case): only this applicant + the KES SEMASA rail.
        $this->get('/system/clms-pipeline?case=mykad')->assertOk()
            ->assertSee('Lim Pei Shan')
            ->assertSee('Kes Semasa')
            ->assertDontSee('Arjun a/l Subramaniam');

        // "Pendaftaran Baru" is a multi-document chooser, not a birth-only wizard.
        $this->get('/system/pendaftaran/baru')->assertOk()
            ->assertSee('Pilih Jenis Dokumen')
            ->assertSee('Sijil Kematian'); // a submodule other than birth is listed

        // Family Tree from the sidebar = a directory, not one fixed family.
        $this->get('/system/family-tree')->assertOk()
            ->assertSee('Carian Salasilah')
            ->assertDontSee('Pohon Salasilah');
        $this->get('/system/family-tree?case=marriage')->assertOk()
            ->assertSee('Pohon Salasilah')
            ->assertSee('Daniel Tan Wei Jie');
    }

    public function test_non_anchor_application_threads_its_own_applicant(): void
    {
        $this->actingAs($this->officer());

        \App\Models\Citizen::create([
            'ic' => '770808-08-7777', 'full_name' => 'Sarah Nadia binti Mahmud',
            'dob' => '1977-08-08', 'gender' => 'F', 'address' => 'No 1, Ipoh',
            'postcode' => '30000', 'state' => 'Perak',
        ]);
        \App\Models\Application::create([
            'reference_number' => 'APP-20260528-9999', 'doc_type' => 'mykad',
            'applicant_ic' => '770808-08-7777', 'applicant_name' => 'Sarah Nadia binti Mahmud',
            'applicant_address' => 'No 1, Ipoh', 'status' => 'officer_review',
            'ai_score' => 0.9, 'sla_state' => 'on_track',
        ]);

        // Threading by ?ref must show the REAL applicant, never the anchor
        $this->get('/system/biometric-capture?ref=APP-20260528-9999')->assertOk()
            ->assertSee('Sarah Nadia binti Mahmud')->assertDontSee('Lim Pei Shan');

        $this->get('/system/clms-pipeline?ref=APP-20260528-9999')->assertOk()
            ->assertSee('Sarah Nadia binti Mahmud')->assertDontSee('Lim Pei Shan');

        // Detail page strip links carry the real reference
        $this->get('/system/tapisan/APP-20260528-9999')->assertOk()
            ->assertSee('ref=APP-20260528-9999', false);
    }
}
