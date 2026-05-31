<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplyController extends Controller
{
    private const SLA_HOURS = [
        'birth'    => 5  * 24,
        'marriage' => 7  * 24,
        'mykad'    => 14 * 24,
    ];

    public function show(Request $request): View
    {
        $type = $request->query('type', 'birth');
        if (! in_array($type, ['birth', 'marriage', 'mykad'], true)) {
            $type = 'birth';
        }

        return view('apply', ['docType' => $type]);
    }

    public function store(Request $request): RedirectResponse
    {
        $docType = $request->input('doc_type');

        // Birth + marriage use deep section-based wizards; mykad keeps the simple
        // 4-field path. All write the same three "applicant_*" columns (so the
        // officer dashboard + tracker work) plus an optional form_data blob.
        [$columns, $formData] = match ($docType) {
            'birth'    => $this->collectBirth($request),
            'marriage' => $this->collectMarriage($request),
            default    => $this->collectMykad($request),
        };

        $reference = sprintf(
            'APP-%s-%04d',
            now()->format('Ymd'),
            random_int(1, 9999),
        );

        $application = Application::create(array_merge($columns, [
            'reference_number' => $reference,
            'doc_type' => $docType,
            'form_data' => $formData,
            'status' => 'received',
            'ai_score' => round(mt_rand(700, 950) / 1000, 3),
            'ai_eta' => Carbon::now()->addHours(self::SLA_HOURS[$docType]),
            'sla_state' => 'on_track',
        ]));

        AuditLog::create([
            'application_id' => $application->id,
            'officer_id' => null,
            'action' => 'created',
            'payload' => ['source' => 'portal', 'doc_type' => $application->doc_type],
            'created_at' => now(),
        ]);

        // The applicant proved ownership by submitting their own IC, so trust
        // this reference for tracking immediately (matches TrackController gate).
        $request->session()->push('tracked_refs', $application->reference_number);

        return redirect()->route('track.show', $application->reference_number)
            ->with('flash_just_submitted', true);
    }

    /**
     * MyKad application (JPN.KP01 — Borang Permohonan Kad Pengenalan).
     * One applicant (A + B), optional guardian/penganjur (section E, for
     * minors), optional Police/Army (C) + Immigration (D) blocks.
     *
     * @return array{0: array<string,mixed>, 1: array<string,mixed>}
     */
    private function collectMykad(Request $request): array
    {
        $validated = $request->validate([
            'doc_type' => 'required|in:mykad',
            'office'   => 'required|string|max:80',
            'confirm.declared' => 'accepted',
            // A + B — Maklumat Pemohon
            'applicant.ic'          => 'required|string|regex:/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/',
            'applicant.full_name'   => 'required|string|max:120',
            'applicant.other_doc'   => 'nullable|string|max:40',
            'applicant.dob'         => 'nullable|date',
            'applicant.sex'         => 'nullable|in:Lelaki,Perempuan',
            'applicant.kp_borneo'   => 'nullable|in:Sabah,Sarawak',
            'applicant.address'     => 'nullable|string|max:200',
            'applicant.postcode'    => 'nullable|string|max:5',
            'applicant.city'        => 'nullable|string|max:60',
            'applicant.state'       => 'nullable|string|max:40',
            'applicant.phone'       => 'nullable|string|max:20',
            'applicant.race'        => 'nullable|string|max:40',
            'applicant.religion'    => 'nullable|string|max:40',
            'applicant.birth_state' => 'nullable|string|max:40',
            'applicant.marital'     => 'nullable|string|max:40',
            // E — Penganjur / Ibu Bapa (jika pemohon bawah umur)
            'guardian.ic'        => 'nullable|string|regex:/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/',
            'guardian.full_name' => 'nullable|string|max:120',
            'guardian.relation'  => 'nullable|in:Ibu,Bapa,Penjaga',
            // C — Polis / Tentera (jika berkenaan)
            'service.type'        => 'nullable|in:Polis,Tentera',
            'service.no'          => 'nullable|string|max:30',
            'service.retire_date' => 'nullable|date',
            // D — Imigresen (jika berkenaan)
            'immigration.resident'    => 'nullable|string|max:40',
            'immigration.permit_no'   => 'nullable|string|max:40',
            'immigration.passport_no' => 'nullable|string|max:40',
            'immigration.passport_country' => 'nullable|string|max:40',
        ]);

        $formData = [
            'form_no'     => 'JPN.KP01',
            'office'      => $validated['office'],
            'applicant'   => $validated['applicant'],
            'guardian'    => $validated['guardian'] ?? [],
            'service'     => array_filter($validated['service'] ?? []),
            'immigration' => array_filter($validated['immigration'] ?? []),
        ];

        return [
            [
                'applicant_ic'      => $validated['applicant']['ic'],
                'applicant_name'    => $validated['applicant']['full_name'],
                'applicant_address' => $validated['applicant']['address'] ?? 'Alamat dari rekod MyKad',
            ],
            $formData,
        ];
    }

    /**
     * Marriage registration (Akta 164 — civil, non-Muslim). Two mirrored
     * sections: male (A) + female (B). Most identity fields are pulled from
     * the national registry client-side; captured here into form_data.
     *
     * @return array{0: array<string,mixed>, 1: array<string,mixed>}
     */
    private function collectMarriage(Request $request): array
    {
        $party = fn (string $p, bool $required) => [
            "{$p}.ic"             => ($required ? 'required' : 'nullable') . '|string|regex:/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/',
            "{$p}.full_name"      => ($required ? 'required' : 'nullable') . '|string|max:120',
            "{$p}.father_name"    => 'nullable|string|max:120',
            "{$p}.dob"            => 'nullable|date',
            "{$p}.address"        => 'nullable|string|max:200',
            "{$p}.postcode"       => 'nullable|string|max:5',
            "{$p}.city"           => 'nullable|string|max:60',
            "{$p}.state"          => 'nullable|string|max:40',
            "{$p}.phone"          => 'nullable|string|max:20',
            "{$p}.citizenship"    => 'nullable|string|max:40',
            "{$p}.domicile"       => 'nullable|string|max:40',
            "{$p}.religion"       => 'nullable|string|max:40',
            "{$p}.occupation"     => 'nullable|string|max:60',
            "{$p}.marital"        => 'nullable|string|max:40',
            "{$p}.other_doc_no"   => 'nullable|string|max:30',
            "{$p}.other_doc_type" => 'nullable|string|max:40',
            "{$p}.other_doc_country" => 'nullable|string|max:40',
        ];

        $validated = $request->validate(array_merge(
            ['doc_type' => 'required|in:marriage', 'office' => 'required|string|max:80', 'confirm.declared' => 'accepted'],
            $party('male', true),
            $party('female', true),
        ));

        $formData = [
            'form_no' => 'Borang Pendaftaran Perkahwinan · Akta 164',
            'office'  => $validated['office'],
            'male'    => $validated['male'],
            'female'  => $validated['female'],
        ];

        return [
            [
                'applicant_ic'      => $validated['male']['ic'],
                'applicant_name'    => $validated['male']['full_name'] . ' & ' . $validated['female']['full_name'],
                'spouse_name'       => $validated['female']['full_name'],
                'spouse_ic'         => $validated['female']['ic'],
                'applicant_address' => $validated['male']['address'] ?? 'Alamat dari rekod MyKad',
            ],
            $formData,
        ];
    }

    /**
     * Deep LM01 birth path. Most parent data is pulled from the national
     * registry (Citizen DB) client-side; here we just capture the submitted
     * payload into a structured form_data blob and derive the 3 flat columns.
     *
     * @return array{0: array<string,mixed>, 1: array<string,mixed>}
     */
    private function collectBirth(Request $request): array
    {
        $validated = $request->validate([
            'doc_type'           => 'required|in:birth',
            // Child (Bahagian A) — the genuinely new info.
            'child.full_name'    => 'required|string|max:120',
            'child.sex'          => 'required|in:Lelaki,Perempuan,Ragu',
            'child.dob'          => 'required|date',
            'child.born_time'    => 'nullable|string|max:12',
            'child.born_period'  => 'nullable|in:Pagi,Tengah Hari,Petang,Malam,Tengah Malam',
            'child.weight_kg'    => 'nullable|string|max:8',
            'child.measure_cm'   => 'nullable|string|max:8',
            'child.born_place'   => 'nullable|string|max:120',
            'child.born_state'   => 'nullable|string|max:40',
            'child.race'         => 'nullable|string|max:40',
            'child.religion'     => 'nullable|string|max:40',
            // Mother (C) — IC drives the DB pull.
            'mother.ic'          => 'required|string|regex:/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/',
            'mother.full_name'   => 'required|string|max:120',
            'mother.dob'         => 'nullable|date',
            'mother.address'     => 'nullable|string|max:200',
            'mother.race'        => 'nullable|string|max:40',
            'mother.occupation'  => 'nullable|string|max:60',
            'mother.education'   => 'nullable|string|max:60',
            'mother.resident'    => 'nullable|string|max:40',
            'mother.religion'    => 'nullable|string|max:40',
            'mother.marital'     => 'nullable|string|max:40',
            'mother.marriage_date' => 'nullable|date',
            // Father (D) — optional (single mother / Section 13 cases).
            'father.ic'          => 'nullable|string|regex:/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/',
            'father.full_name'   => 'nullable|string|max:120',
            'father.dob'         => 'nullable|date',
            'father.race'        => 'nullable|string|max:40',
            'father.occupation'  => 'nullable|string|max:60',
            'father.education'   => 'nullable|string|max:60',
            'father.resident'    => 'nullable|string|max:40',
            'father.religion'    => 'nullable|string|max:40',
            'father.section13'   => 'nullable|boolean',
            // Person who delivers the baby (B).
            'deliverer.doc_no'   => 'nullable|string|max:30',
            'deliverer.doc_type' => 'nullable|string|max:40',
            'deliverer.full_name'=> 'nullable|string|max:120',
            // Informant (E) + confirmation (F).
            'informant.relation' => 'required|in:Ibu,Bapa,Lain-lain',
            'informant.ic'       => 'nullable|string|max:30',
            'informant.full_name'=> 'nullable|string|max:120',
            'confirm.phone'      => 'nullable|string|max:20',
            'confirm.email'      => 'nullable|email|max:120',
            'confirm.declared'   => 'accepted',
        ]);

        $formData = [
            'form_no'   => 'JPN.LM01',
            'child'     => $validated['child'],
            'mother'    => $validated['mother'],
            'father'    => $validated['father'] ?? [],
            'deliverer' => $validated['deliverer'] ?? [],
            'informant' => $validated['informant'],
            'confirm'   => [
                'phone' => $validated['confirm']['phone'] ?? null,
                'email' => $validated['confirm']['email'] ?? null,
            ],
            // Bahagian I — citizenship is registrar-determined; prototype defaults.
            'citizenship' => 'Warganegara Malaysia',
        ];

        // Flat columns: subject = child, tracker/owner = informant (parent).
        return [
            [
                'applicant_ic'      => $validated['informant']['ic']
                    ?? $validated['mother']['ic'],
                'applicant_name'    => $validated['child']['full_name'],
                'applicant_address' => $validated['mother']['address']
                    ?? 'Alamat dari rekod MyKad',
            ],
            $formData,
        ];
    }
}
