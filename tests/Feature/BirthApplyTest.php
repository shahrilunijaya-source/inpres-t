<?php

namespace Tests\Feature;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BirthApplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_birth_wizard_stores_form_data(): void
    {
        $payload = [
            'doc_type' => 'birth',
            'child' => [
                'full_name' => 'Aisyah binti Daniel',
                'sex' => 'Perempuan',
                'dob' => '2026-05-20',
                'born_time' => '03:14',
                'born_period' => 'Pagi',
                'weight_kg' => '3.20',
                'measure_cm' => '49',
                'born_place' => 'Hospital Kuala Lumpur',
                'born_state' => 'W.P. Kuala Lumpur',
                'race' => 'Melayu',
                'religion' => 'Islam',
            ],
            'mother' => [
                'ic' => '920418-10-5566',
                'full_name' => 'Nur Sofia binti Amir',
                'dob' => '1992-04-18',
                'address' => '10, Jalan Damai, 50480 Kuala Lumpur',
                'race' => 'Melayu',
                'religion' => 'Islam',
                'resident' => 'Warganegara Malaysia',
                'occupation' => 'Guru',
                'education' => 'Tertiari / Tinggi',
                'marital' => 'Berkahwin',
                'marriage_date' => '2018-06-01',
            ],
            'father' => [
                'ic' => '900214-14-5588',
                'full_name' => 'Daniel bin Hamzah',
                'dob' => '1990-02-14',
                'race' => 'Melayu',
                'religion' => 'Islam',
                'resident' => 'Warganegara Malaysia',
                'occupation' => 'Jurutera',
                'education' => 'Tertiari / Tinggi',
            ],
            'deliverer' => [
                'doc_no' => 'KKM-FHIR-123456',
                'doc_type' => 'MyKad · Malaysia',
                'full_name' => 'Hospital Kuala Lumpur',
            ],
            'informant' => ['relation' => 'Ibu'],
            'confirm' => ['phone' => '012-3456789', 'email' => 'sofia@contoh.com', 'declared' => '1'],
        ];

        $res = $this->post('/apply', $payload);
        $res->assertRedirect();

        $app = Application::latest('id')->first();
        $this->assertNotNull($app);
        $this->assertSame('birth', $app->doc_type);
        $this->assertSame('Aisyah binti Daniel', $app->applicant_name);
        $this->assertSame('920418-10-5566', $app->applicant_ic); // informant = Ibu -> mother IC
        $this->assertSame('JPN.LM01', $app->form_data['form_no']);
        $this->assertSame('Perempuan', $app->form_data['child']['sex']);
        $this->assertSame('Daniel bin Hamzah', $app->form_data['father']['full_name']);
        $this->assertSame('Warganegara Malaysia', $app->form_data['citizenship']);
    }

    public function test_birth_requires_declaration_and_child_name(): void
    {
        $res = $this->post('/apply', [
            'doc_type' => 'birth',
            'mother' => ['ic' => '920418-10-5566', 'full_name' => 'X'],
            'informant' => ['relation' => 'Ibu'],
            // missing child.full_name, child.sex, child.dob, confirm.declared
        ]);
        $res->assertSessionHasErrors();
    }

    public function test_marriage_wizard_stores_both_parties(): void
    {
        $res = $this->post('/apply', [
            'doc_type' => 'marriage',
            'office' => 'JPN Daerah SPU · Seberang Perai Utara',
            'male' => [
                'ic' => '900312-14-5021', 'full_name' => 'Marcus Lim Chee Hong',
                'father_name' => 'Lim Kam Thong', 'dob' => '1990-03-12',
                'address' => 'No 1, Lorong Panglima', 'postcode' => '13200', 'city' => 'Kepala Batas',
                'state' => 'Pulau Pinang', 'phone' => '012-6422111', 'citizenship' => 'Malaysia',
                'religion' => 'Buddha', 'occupation' => 'Jurutera', 'marital' => 'Bujang',
            ],
            'female' => [
                'ic' => '920708-10-5566', 'full_name' => 'Priya Nair',
                'father_name' => 'Nair Govind', 'dob' => '1992-07-08',
                'address' => 'No 2, Lorong Bidara', 'postcode' => '14000', 'city' => 'Bukit Mertajam',
                'state' => 'Pulau Pinang', 'phone' => '016-6662222', 'citizenship' => 'Malaysia',
                'religion' => 'Hindu', 'occupation' => 'Kerani', 'marital' => 'Bujang',
            ],
            'confirm' => ['declared' => '1'],
        ]);
        $res->assertRedirect();

        $app = Application::latest('id')->first();
        $this->assertSame('marriage', $app->doc_type);
        $this->assertSame('Marcus Lim Chee Hong & Priya Nair', $app->applicant_name);
        $this->assertSame('900312-14-5021', $app->applicant_ic);
        $this->assertSame('920708-10-5566', $app->spouse_ic);
        $this->assertSame('Priya Nair', $app->spouse_name);
        $this->assertSame('Buddha', $app->form_data['male']['religion']);
        $this->assertSame('Hindu', $app->form_data['female']['religion']);
        $this->assertStringContainsString('Akta 164', $app->form_data['form_no']);
    }

    public function test_marriage_requires_office_and_both_ics(): void
    {
        $res = $this->post('/apply', [
            'doc_type' => 'marriage',
            'male' => ['full_name' => 'X'],
            'confirm' => ['declared' => '1'],
        ]);
        $res->assertSessionHasErrors();
    }

    public function test_mykad_wizard_stores_applicant_and_guardian(): void
    {
        $res = $this->post('/apply', [
            'doc_type' => 'mykad',
            'office' => 'JPN Daerah SPU · Seberang Perai Utara',
            'applicant' => [
                'ic' => '900214-14-5588', 'full_name' => 'Lim Pei Shan',
                'sex' => 'Perempuan', 'dob' => '2009-02-03', 'address' => 'No XX Lorong Panglima',
                'postcode' => '13200', 'city' => 'Kepala Batas', 'state' => 'Pulau Pinang',
                'phone' => '012-6422111', 'race' => 'Cina', 'religion' => 'Buddha',
                'birth_state' => 'Pulau Pinang', 'marital' => 'Bujang',
            ],
            'guardian' => ['ic' => '761112-10-3285', 'full_name' => 'Ahmad bin Ahmad', 'relation' => 'Bapa'],
            'service' => ['type' => 'Polis', 'no' => 'RF12345'],
            'confirm' => ['declared' => '1'],
        ]);
        $res->assertRedirect();

        $app = Application::latest('id')->first();
        $this->assertSame('mykad', $app->doc_type);
        $this->assertSame('Lim Pei Shan', $app->applicant_name);
        $this->assertSame('900214-14-5588', $app->applicant_ic);
        $this->assertSame('JPN.KP01', $app->form_data['form_no']);
        $this->assertSame('Bapa', $app->form_data['guardian']['relation']);
        $this->assertSame('Polis', $app->form_data['service']['type']);
    }

    public function test_mykad_requires_applicant_ic_and_office(): void
    {
        $res = $this->post('/apply', [
            'doc_type' => 'mykad',
            'applicant' => ['full_name' => 'X'],
            'confirm' => ['declared' => '1'],
        ]);
        $res->assertSessionHasErrors();
    }
}
