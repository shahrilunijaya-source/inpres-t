<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Citizen;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeds the 3 proposal anchor cases (config/demo_cases.php) so that the
 * functional tapisan-detail page and the Sistem Wajib capability screens
 * resolve to the SAME applicant. Fixed reference numbers keep the screenshot
 * flow deterministic across re-seeds.
 */
class DemoCaseSeeder extends Seeder
{
    public function run(): void
    {
        $officer = User::where('email', 'demo@jpn.gov.my')->first();
        $base = Carbon::parse('2026-05-28 09:05:00');

        // Sample ICs advertised on the Apply form ("Cuba IC contoh: …") that are
        // NOT demo anchors. CitizenSeeder generates random ICs, so these would
        // only exist by chance — seed them deterministically so the OCR pull
        // works on every fresh DB (local + host). The mother sample
        // (920418-10-5566) is already seeded below as the birth anchor.
        $sampleCitizens = [
            ['ic' => '761112-10-3285', 'full_name' => 'Ahmad bin Ahmad', 'dob' => '1976-11-12', 'gender' => 'M',
             'address' => '8, Jalan Pinggiran Putra 2', 'postcode' => '43300', 'state' => 'Selangor'],
        ];
        foreach ($sampleCitizens as $c) {
            Citizen::updateOrCreate(['ic' => $c['ic']], $c);
        }

        foreach (config('demo_cases') as $case) {
            // Citizen (OCR source record) — a citizen is one person, so for a
            // marriage couple use the groom's name, not the combined couple label.
            // For a birth the anchor IC is the mother's MyKad (she is the
            // informant/applicant); the newborn has no IC yet, so seed the
            // mother — otherwise an IC lookup on her IC returns the baby's
            // record (a 2026-born "mother").
            $isBirthMother = $case['doc_type'] === 'birth' && ! empty($case['mother']);
            Citizen::updateOrCreate(
                ['ic' => $case['ic']],
                [
                    'full_name' => $isBirthMother
                        ? $case['mother']['name']
                        : ($case['groom']['name'] ?? $case['name']),
                    'dob'       => $isBirthMother
                        ? ($case['mother']['dob'] ?? $case['dob'])
                        : $case['dob'],
                    'gender'    => $isBirthMother ? 'F' : $case['gender'],
                    'address'   => $case['address'],
                    'postcode'  => $case['postcode'],
                    'state'     => $case['state'],
                ]
            );

            // For a marriage, also seed the bride as a citizen (MyKad on file)
            if ($case['doc_type'] === 'marriage' && !empty($case['bride'])) {
                Citizen::updateOrCreate(
                    ['ic' => $case['bride']['ic']],
                    [
                        'full_name' => $case['bride']['name'],
                        'dob'       => $case['bride']['dob'] ?? '1991-01-01',
                        'gender'    => 'F',
                        'address'   => $case['address'],
                        'postcode'  => $case['postcode'],
                        'state'     => $case['state'],
                    ]
                );
            }

            // Application (anchor)
            $app = Application::updateOrCreate(
                ['reference_number' => $case['reference']],
                [
                    'doc_type'          => $case['doc_type'],
                    'applicant_ic'      => $case['ic'],
                    'applicant_name'    => $case['name'],
                    'spouse_name'       => $case['bride']['name'] ?? null,
                    'spouse_ic'         => $case['bride']['ic'] ?? null,
                    'applicant_address' => $case['address'],
                    'status'            => $case['status'],
                    'ai_score'          => $case['ai_score'],
                    'ai_eta'            => $base->copy()->addDays(3),
                    'sla_state'         => 'on_track',
                    'assigned_officer_id' => $officer?->id,
                ]
            );

            // Backdate without auto-touching updated_at
            $app->timestamps = false;
            $app->created_at = $base->copy();
            $app->updated_at = $base->copy()->addMinutes(12);
            $app->save();

            // Audit trail so the timeline reads as a real case
            $app->auditLogs()->delete();
            $trail = [
                ['action' => 'application_received', 'from' => null,       'to' => 'received',       'notes' => 'Permohonan diterima melalui portal.', 'off' => 0],
                ['action' => 'auto_verify',          'from' => 'received', 'to' => 'verified',       'notes' => 'OCR + padanan rekod warganegara lulus auto.', 'off' => 4],
                ['action' => 'route_officer',        'from' => 'verified', 'to' => 'officer_review', 'notes' => 'Dirujuk untuk semakan pegawai.', 'off' => 9],
            ];

            $logs = [];
            foreach ($trail as $t) {
                $logs[] = [
                    'application_id' => $app->id,
                    'officer_id'     => $t['off'] === 0 ? null : $officer?->id,
                    'action'         => $t['action'],
                    'payload'        => json_encode(['from' => $t['from'], 'to' => $t['to'], 'notes' => $t['notes']]),
                    'created_at'     => $base->copy()->addMinutes($t['off']),
                ];
            }
            AuditLog::insert($logs);
        }

        $this->command?->info('Seeded 3 demo anchor cases: '
            . collect(config('demo_cases'))->pluck('reference')->implode(', '));
    }
}
