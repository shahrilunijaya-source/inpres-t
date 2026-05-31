<?php

/*
|--------------------------------------------------------------------------
| Demo anchor cases — proposal screenshot threading
|--------------------------------------------------------------------------
|
| Single source of truth for the 3 vertical-slice demo cases. Each case is
| ONE applicant threaded through every relevant "Sistem Wajib" (LAMPIRAN A)
| screen, so a reviewer sees one continuous application terrain instead of
| disconnected module mockups.
|
| Used by:
|   - DemoCaseSeeder        → creates the matching Application + Citizen rows
|   - SystemController      → passes the active case to each Sistem Wajib view
|   - _case-rail.blade.php  → the persistent "KES SEMASA" banner + breadcrumb
|
| Civil (non-Muslim) marriage uses non-Malay names by requirement.
|
*/

return [

    // ===================== MyKad — Lim Pei Shan =========================
    'mykad' => [
        'key'        => 'mykad',
        'reference'  => 'APP-20260528-7001',
        'doc_type'   => 'mykad',
        'doc_label'  => 'MyKad · Gantian Hilang',
        'module'     => 'Modul 04 · Kad Pengenalan',
        'name'       => 'Lim Pei Shan',
        'ic'         => '900214-14-5588',
        'dob'        => '1990-02-14',
        'gender'     => 'F',
        'religion'   => 'Buddha',
        'address'    => '12, Jalan SS2/24, 47300 Petaling Jaya, Selangor',
        'postcode'   => '47300',
        'state'      => 'Selangor',
        'ai_score'   => 0.97,
        'status'     => 'officer_review',
        'card_no'    => 'MK-2026-770001',

        // ordered Sistem Wajib screens for this case (rail breadcrumb)
        'steps'      => ['lapor', 'biometric', 'abis', 'clms', 'kad', 'mydigital'],

        // Lapor kehilangan — police report mandatory for a lost MyKad (gantian hilang)
        'lapor'      => [
            'report_no'   => 'RPT/PJ/2026/0051287',
            'station'     => 'Balai Polis Petaling Jaya',
            'report_date' => '2026-05-25 14:20',
            'old_card_no' => 'MK-2014-3320891',
            'old_status'  => 'DIBATALKAN',
            'reason'      => 'Dompet hilang di pusat membeli-belah — MyKad tiada di dalam.',
            'fee'         => 'RM 110.00',
            'fee_status'  => 'DIBAYAR',
            'declared_by' => 'Lim Pei Shan',
        ],

        // Physical card artifact (the deliverable) — issued after CLMS personalization
        'card'       => [
            'card_no'     => 'MK-2026-770001',
            'issue_date'  => '2026-05-28',
            'expiry'      => 'SEUMUR HIDUP',
            'citizenship' => 'WARGANEGARA',
            'birth_place' => 'PULAU PINANG',
            'type_label'  => 'Gantian Hilang (ke-2)',
        ],

        'biometric'  => [
            'counter'  => 'K-04 Putrajaya',
            'officer'  => 'Pn. Faridah · OFC-2031',
            'nfiq'     => 97,
            'duration' => '2 min 47s',
            'started'  => '20:38:11',
            'note'     => 'MyKad asal HILANG — re-capture 10 cap jari + muka + iris untuk sahkan identiti & elak penyalahgunaan kad lama.',
        ],
        'abis'       => [
            'result'      => 'MATCH',
            'tone'        => 'green',
            'score'       => 99.91,
            'time'        => 3.42,
            'summary'     => 'Padan dengan enrolmen sedia ada — sahkan identiti, bukan pendua.',
            'candidates'  => [
                ['rank' => 1, 'id' => 'ENR-2008-1144239', 'score' => 99.91, 'note' => 'Enrolmen MyKad 2008 (subjek sama)', 'tone' => 'green'],
                ['rank' => 2, 'id' => 'ENR-2015-7782104', 'score' => 41.20, 'note' => 'Tidak berkaitan', 'tone' => 'amber'],
                ['rank' => 3, 'id' => 'ENR-2011-3329870', 'score' => 33.07, 'note' => 'Tidak berkaitan', 'tone' => 'amber'],
            ],
        ],
        'blockchain' => [
            'cc'    => 'mykad-cc',
            'events'=> [
                ['block' => 1925013, 'hash' => '0xf4a1c9e7d2...', 'event' => 'MyKadIssued',       'subj' => 'MK-2026-770001', 'ts' => '2026-05-28 11:02:41'],
                ['block' => 1925009, 'hash' => '0xb8d4e2f1a9...', 'event' => 'BiometricVerified', 'subj' => 'MK-2026-770001', 'ts' => '2026-05-28 10:58:18'],
                ['block' => 1924880, 'hash' => '0x7c3b9a2d8e...', 'event' => 'CardRevoked',       'subj' => 'MK-2014-3320891', 'ts' => '2026-05-25 14:25:02'],
            ],
        ],
        'mydigital'  => [
            'action' => 'AUTO_PROVISION',
            'status' => 'success',
        ],
        'clms'       => [
            'serial'   => 'MK-2026-770001',
            'type'     => 'Gantian Hilang',
            'stage'    => 'Personalisasi',
            'eta'      => '6 min',
            'priority' => 'normal',
        ],
        'agencies'   => ['JIM', 'PDRM', 'MAMPU'],
    ],

    // ===================== Kelahiran — newborn ==========================
    'birth' => [
        'key'        => 'birth',
        'reference'  => 'APP-20260528-7002',
        'doc_type'   => 'birth',
        'doc_label'  => 'Sijil Kelahiran · Pendaftaran Baru',
        'module'     => 'Modul 01 · Kelahiran',
        'name'       => 'Nur Sofia binti Amir Hamzah',
        'ic'         => '920418-10-5566', // mother's IC anchors the application
        'dob'        => '2026-05-26',
        'gender'     => 'F',
        'address'    => '8, Jalan Pinggiran Putra 2, 43300 Seri Kembangan, Selangor',
        'postcode'   => '43300',
        'state'      => 'Selangor',
        'ai_score'   => 0.95,
        'status'     => 'received',
        'cert_no'    => 'KLH-2026-770002',

        // Ibu bapa — semua medan auto-isi dari Pendaftaran Negara (rekod MyKad), bukan ditaip semula
        'mother'     => [
            'name'        => 'Farah Nadia binti Salleh',
            'ic'          => '920418-10-5566',
            'doc_type'    => 'MyKad',
            'negara'      => 'Malaysia',
            'dob'         => '1992-04-18',
            'alamat'      => '8, Jalan Pinggiran Putra 2, 43300 Seri Kembangan, Selangor',
            'keturunan'   => 'Melayu',
            'pekerjaan'   => 'Akauntan',
            'pemastautin' => 'Warganegara',
            'warganegara' => 'Warganegara',
            'agama'       => 'Islam',
            'kahwin'      => 'Berkahwin',
            'tarikh_kahwin' => '2018-06-23',
        ],
        'father'     => [
            'name'        => 'Amir Hamzah bin Roslan',
            'ic'          => '880302-10-3321',
            'doc_type'    => 'MyKad',
            'negara'      => 'Malaysia',
            'dob'         => '1988-03-02',
            'keturunan'   => 'Melayu',
            'pekerjaan'   => 'Jurutera Awam',
            'pemastautin' => 'Warganegara',
            'warganegara' => 'Warganegara',
            'agama'       => 'Islam',
        ],
        'hospital'   => [
            'name'    => 'HOSPITAL PUTRAJAYA',
            'address' => 'Presint 7, 62250 Putrajaya, Wilayah Persekutuan',
            'mo'      => 'Dr. Lim Choo Keat',
            'weight'  => '3.2 kg',
            'sex'     => 'P',
            'notif'   => 'FHIR-2026-0528-4471',
            'born_at' => '2026-05-26 04:18',
        ],

        // Borang Pendaftaran Kelahiran — diisi oleh ibu bapa DALAM TALIAN; data klinikal
        // pra-isi dari hospital; pengesahan biometrik dibuat di kaunter.
        'borang'     => [
            'form_no'      => 'JPN.LM01',
            'channel'      => 'Portal MyJPN · dalam talian',
            'submitted_at' => '2026-05-27 21:40',
            'status'       => 'Menunggu pengesahan biometrik di kaunter',
            'baby_name'    => 'Nur Sofia binti Amir Hamzah',
            'sex'          => 'Perempuan',
            'dob'          => '2026-05-26',
            'born_time'    => '04:18',
            'born_place'   => 'Hospital Putrajaya',
            'weight'       => '3.2 kg',
            'informant'    => 'Farah Nadia binti Salleh (Ibu)',
        ],

        'sijil'      => [
            'cert_no'     => 'JPN.LM05-2026-770002',
            'reg_no'      => 'KLH-2026-770002',
            'mykid_no'    => 'MYKID-2026-770002',
            'issued_date' => '2026-05-28',
            'block'       => 1925014,
            'tx_hash'     => '0x9c8b7a6d5e...',
            'ledger_wait' => '612 ms',
        ],

        'steps'      => ['hospital', 'borang', 'biometric', 'familytree', 'sijil'],

        'biometric'  => [
            'counter'  => 'K-02 Seri Kembangan',
            'officer'  => 'En. Tan Boon Hock · OFC-1187',
            'nfiq'     => 95,
            'duration' => '1 min 52s',
            'started'  => '09:14:03',
            'note'     => 'Pengesahan biometrik IBU BAPA (bayi: cap tapak kaki sahaja).',
        ],
        'abis'       => [
            'result'   => 'NO MATCH',
            'tone'     => 'amber',
            'score'    => 0.00,
            'time'     => 4.61,
            'summary'  => 'Tiada padanan — disahkan bayi baru, bukan pendaftaran berganda.',
            'candidates' => [],
        ],
        'blockchain' => [
            'cc'    => 'kelahiran-cc',
            'events'=> [
                ['block' => 1925014, 'hash' => '0x9c8b7a6d5e...', 'event' => 'BirthRegistered',   'subj' => 'KLH-2026-770002', 'ts' => '2026-05-28 09:21:07'],
                ['block' => 1925012, 'hash' => '0xd2e5f4a1c7...', 'event' => 'HospitalNotified',   'subj' => 'KLH-2026-770002', 'ts' => '2026-05-26 04:31:55'],
            ],
        ],
        'agencies'   => ['KKM', 'MAMPU', 'KPM'],
    ],

    // ============ Perkahwinan Sivil — non-Muslim couple ================
    'marriage' => [
        'key'        => 'marriage',
        'reference'  => 'APP-20260528-7003',
        'doc_type'   => 'marriage',
        'doc_label'  => 'Sijil Perkahwinan Sivil · Akta 164',
        'module'     => 'Modul 05 · Perkahwinan & Perceraian',
        'name'       => 'Daniel Tan Wei Jie & Sarah Pereira', // couple — marriage is two parties
        'ic'         => '880905-14-5071',
        'dob'        => '1988-09-05',
        'gender'     => 'M',
        'address'    => '27, Persiaran Bukit Tunku, 50480 Kuala Lumpur',
        'postcode'   => '50480',
        'state'      => 'Kuala Lumpur',
        'ai_score'   => 0.93,
        'status'     => 'received',
        'record_no'  => 'PK-2026-770003',

        'groom'      => ['name' => 'Daniel Tan Wei Jie', 'ic' => '880905-14-5071', 'status' => 'Bujang', 'dob' => '1988-09-05'],
        'bride'      => ['name' => 'Sarah Pereira',      'ic' => '910722-07-5388', 'status' => 'Bujang', 'dob' => '1991-07-22'],
        'kaveat'     => [
            'ref'        => 'KAV-2026-002220',
            'lodged'     => '2026-05-10',
            'expires'    => '2026-05-31',
            'days_left'  => 1,
            'objections' => 0,
            'tone'       => 'amber',
        ],
        'upacara'    => [
            'venue'      => 'Gereja St. John · Bukit Nanas, Kuala Lumpur',
            'venue_type' => 'ibadat', // jpn | ibadat | malawakil | tribunal
            'venue_detail' => [
                'place'     => 'Gereja St. John (Katolik)',
                'address'   => '5, Jalan Bukit Nanas, 50250 Kuala Lumpur',
                'authority' => 'Rev. Thomas Anand · Penolong Pendaftar Perkahwinan (dilantik)',
            ],
            'registrar'  => 'Rev. Thomas Anand',
            'registrar_id' => 'REG-PK-0148 (Penolong)',
            'date'       => '2026-05-31',
            'time'       => '10:30',
            'reg_no'     => 'PK-2026-770003',
            'status'     => 'selesai', // ikrar + tandatangan lengkap
            'witnesses'  => [
                ['name' => 'Michael Tan Wei Han', 'ic' => '850310-14-5290', 'rel' => 'Abang pengantin lelaki'],
                ['name' => 'Grace Pereira',       'ic' => '930115-07-5102', 'rel' => 'Kakak pengantin perempuan'],
            ],
        ],

        // Officer cycle: Semak(detail) → Salasilah → Kaveat → Upacara → [blockchain passthrough] → Sijil.
        // Blockchain module stays admin-locked; the business process passes through it to the certificate.
        'steps'      => ['familytree', 'kaveat', 'upacara', 'sijil'],

        'sijil'      => [
            'cert_no'     => 'JPN.KC02-2026-770003',
            'reg_no'      => 'PK-2026-770003',
            'issued_date' => '2026-05-31',
            'block'       => 1925015,
            'tx_hash'     => '0xc5e7a1b9f2d4...',
            'ledger_wait' => '612 ms',
            'copies'      => 2,
        ],

        'blockchain' => [
            'cc'    => 'kahwin-cc',
            'events'=> [
                ['block' => 1925015, 'hash' => '0xc5e7a1b9f2...', 'event' => 'PerkahwinanRegistered', 'subj' => 'PK-2026-770003', 'ts' => '2026-05-31 10:05:31'],
                ['block' => 1925005, 'hash' => '0xf1e2d3c4b5...', 'event' => 'CaveatCleared',         'subj' => 'KAV-2026-002220', 'ts' => '2026-05-31 09:55:00'],
            ],
        ],
        'agencies'   => ['MAHKAMAH', 'LHDN', 'PDRM'],
    ],

];
