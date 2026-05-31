<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Citizen;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ApplicationSeeder extends Seeder
{
    /**
     * Status distribution — biased toward active stages so officer dashboard feels busy.
     */
    private const STATUS_WEIGHTS = [
        'received'       => 18,
        'verified'       => 20,
        'officer_review' => 25,
        'approved'       => 15,
        'issued'         => 18,
        'rejected'       => 4,
    ];

    private const DOC_TYPES = ['birth', 'marriage', 'mykad'];

    private const SLA_HOURS = [
        'birth'    => 5  * 24,
        'marriage' => 7  * 24,
        'mykad'    => 14 * 24,
    ];

    /**
     * Civil marriage (Akta 164) is for NON-Muslims only — never Malay/Muslim names.
     * Each marriage application draws a couple from this pool; both parties are
     * seeded as Citizens so their MyKad + biometrics are "already on file".
     */
    private const COUPLES = [
        ['groom' => ['name' => 'Marcus Lim Chee Hong',   'ic' => '900312-14-5021', 'dob' => '1990-03-12'], 'bride' => ['name' => 'Priya Nair',            'ic' => '920708-10-5566', 'dob' => '1992-07-08']],
        ['groom' => ['name' => 'Vijay Kumar a/l Rajan',  'ic' => '880925-14-5133', 'dob' => '1988-09-25'], 'bride' => ['name' => 'Anitha a/p Subramaniam', 'ic' => '910214-07-5388', 'dob' => '1991-02-14']],
        ['groom' => ['name' => 'Anthony Fernandez',      'ic' => '870519-14-5071', 'dob' => '1987-05-19'], 'bride' => ['name' => 'Michelle Wong Li Ying',  'ic' => '930618-01-5642', 'dob' => '1993-06-18']],
        ['groom' => ['name' => 'Harpreet Singh',         'ic' => '891102-14-5217', 'dob' => '1989-11-02'], 'bride' => ['name' => 'Simran Kaur',            'ic' => '940228-10-5904', 'dob' => '1994-02-28']],
        ['groom' => ['name' => 'Benjamin Goh Wei Sheng', 'ic' => '910407-07-5183', 'dob' => '1991-04-07'], 'bride' => ['name' => 'Rachel Ong Hui Min',     'ic' => '930911-07-5226', 'dob' => '1993-09-11']],
        ['groom' => ['name' => 'Dinesh a/l Maniam',      'ic' => '860730-14-5499', 'dob' => '1986-07-30'], 'bride' => ['name' => 'Shanti a/p Govindasamy', 'ic' => '900105-14-5612', 'dob' => '1990-01-05']],
        ['groom' => ['name' => 'Christopher D\'Cruz',    'ic' => '880815-04-5077', 'dob' => '1988-08-15'], 'bride' => ['name' => 'Angela Sebastian',       'ic' => '920423-04-5388', 'dob' => '1992-04-23']],
        ['groom' => ['name' => 'Kenneth Chan Kok Wai',   'ic' => '900627-14-5145', 'dob' => '1990-06-27'], 'bride' => ['name' => 'Vivian Lau Pei Qi',      'ic' => '940102-14-5260', 'dob' => '1994-01-02']],
        ['groom' => ['name' => 'Arvind a/l Pillai',      'ic' => '871204-10-5331', 'dob' => '1987-12-04'], 'bride' => ['name' => 'Deepa a/p Krishnan',     'ic' => '910530-10-5748', 'dob' => '1991-05-30']],
        ['groom' => ['name' => 'Edmund Yeoh Boon Kiat',  'ic' => '890918-07-5019', 'dob' => '1989-09-18'], 'bride' => ['name' => 'Stephanie Loh Mei Xin',  'ic' => '930714-07-5582', 'dob' => '1993-07-14']],
        ['groom' => ['name' => 'Gabriel Lopez',          'ic' => '860421-12-5263', 'dob' => '1986-04-21'], 'bride' => ['name' => 'Maria Dominic',          'ic' => '910816-12-5104', 'dob' => '1991-08-16']],
        ['groom' => ['name' => 'Samuel Raj',             'ic' => '880203-08-5471', 'dob' => '1988-02-03'], 'bride' => ['name' => 'Esther Thomas',          'ic' => '920927-08-5338', 'dob' => '1992-09-27']],
    ];

    public function run(): void
    {
        $citizens = Citizen::all();
        if ($citizens->isEmpty()) {
            $this->command?->error('No citizens to attach applications to. Run CitizenSeeder first.');
            return;
        }

        $officers = User::whereIn('role', ['officer', 'supervisor'])->get();

        $statuses = $this->expandWeights(self::STATUS_WEIGHTS);
        $coupleIdx = 0;
        $usedRefs = Application::pluck('reference_number')->flip()->toArray(); // avoid clashing with anchors

        for ($i = 0; $i < 80; $i++) {
            $docType = self::DOC_TYPES[array_rand(self::DOC_TYPES)];
            $status = $statuses[array_rand($statuses)];

            // Resolve applicant (+ spouse for marriage couples)
            $spouseName = null;
            $spouseIc = null;
            if ($docType === 'marriage') {
                $couple = self::COUPLES[$coupleIdx % count(self::COUPLES)];
                $coupleIdx++;
                foreach (['groom' => 'M', 'bride' => 'F'] as $role => $gender) {
                    Citizen::updateOrCreate(
                        ['ic' => $couple[$role]['ic']],
                        ['full_name' => $couple[$role]['name'], 'dob' => $couple[$role]['dob'], 'gender' => $gender,
                         'address' => '12, Jalan Damai', 'postcode' => '50480', 'state' => 'Kuala Lumpur'],
                    );
                }
                $applicantIc = $couple['groom']['ic'];
                $applicantName = $couple['groom']['name'] . ' & ' . $couple['bride']['name'];
                $applicantAddress = '12, Jalan Damai, 50480 Kuala Lumpur';
                $spouseName = $couple['bride']['name'];
                $spouseIc = $couple['bride']['ic'];
            } else {
                $citizen = $citizens->random();
                $applicantIc = $citizen->ic;
                $applicantName = $citizen->full_name;
                $applicantAddress = $citizen->address . ', ' . $citizen->postcode . ' ' . $citizen->state;
            }

            // Generate created_at scattered across last 14 days
            $createdAt = Carbon::now()
                ->subDays(random_int(0, 14))
                ->subHours(random_int(0, 23))
                ->subMinutes(random_int(0, 59));

            $aiEta = (clone $createdAt)->addHours(self::SLA_HOURS[$docType]);
            $aiScore = $status === 'rejected'
                ? round(mt_rand(200, 500) / 1000, 3)
                : round(mt_rand(600, 950) / 1000, 3);

            $slaState = $this->computeSlaState($createdAt, $aiEta, $status);

            // Reference format: APP-YYYYMMDD-XXXX — regenerate on collision so a
            // duplicate random number never aborts the seed.
            do {
                $reference = sprintf('APP-%s-%04d', $createdAt->format('Ymd'), random_int(1, 9999));
            } while (isset($usedRefs[$reference]));
            $usedRefs[$reference] = true;

            // Assigned officer only when past initial intake
            $assignedOfficer = in_array($status, ['officer_review', 'approved', 'issued', 'rejected'], true)
                && $officers->isNotEmpty()
                ? $officers->random()
                : null;

            $application = Application::create([
                'reference_number' => $reference,
                'doc_type' => $docType,
                'applicant_ic' => $applicantIc,
                'applicant_name' => $applicantName,
                'spouse_name' => $spouseName,
                'spouse_ic' => $spouseIc,
                'applicant_address' => $applicantAddress,
                'status' => $status,
                'ai_score' => $aiScore,
                'ai_eta' => $aiEta,
                'sla_state' => $slaState,
                'assigned_officer_id' => $assignedOfficer?->id,
                'notes' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $this->seedAuditLogs($application, $assignedOfficer, $createdAt);
        }

        $this->command?->info('Seeded 80 applications.');
    }

    /**
     * Convert weight map ['A' => 18, 'B' => 25] to a flat array for random pick.
     *
     * @param  array<string,int>  $weights
     * @return array<int,string>
     */
    private function expandWeights(array $weights): array
    {
        $out = [];
        foreach ($weights as $value => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $out[] = $value;
            }
        }

        return $out;
    }

    private function computeSlaState(Carbon $createdAt, Carbon $eta, string $status): string
    {
        if (in_array($status, ['issued', 'approved'], true)) {
            return 'on_track';
        }

        $totalHours = $createdAt->diffInHours($eta);
        $elapsedHours = $createdAt->diffInHours(Carbon::now());

        if ($totalHours === 0) {
            return 'on_track';
        }

        $progress = $elapsedHours / $totalHours;

        if ($progress >= 1.0) {
            return 'breached';
        }
        if ($progress >= 0.75) {
            return 'at_risk';
        }

        return 'on_track';
    }

    private function seedAuditLogs(Application $application, ?User $officer, Carbon $createdAt): void
    {
        $logs = [];

        $logs[] = [
            'application_id' => $application->id,
            'officer_id' => null,
            'action' => 'created',
            'payload' => json_encode(['source' => 'portal', 'doc_type' => $application->doc_type]),
            'created_at' => $createdAt,
        ];

        $stageOrder = ['received', 'verified', 'officer_review', 'approved', 'issued'];
        $currentIndex = array_search($application->status, $stageOrder, true);

        if ($currentIndex === false) {
            // rejected
            if ($officer) {
                $logs[] = [
                    'application_id' => $application->id,
                    'officer_id' => $officer->id,
                    'action' => 'rejected',
                    'payload' => json_encode(['reason' => 'IC mismatch with submitted documents']),
                    'created_at' => (clone $createdAt)->addHours(random_int(2, 18)),
                ];
            }
        } else {
            for ($i = 1; $i <= $currentIndex; $i++) {
                $stage = $stageOrder[$i];
                $logs[] = [
                    'application_id' => $application->id,
                    'officer_id' => $i >= 2 ? $officer?->id : null,
                    'action' => 'stage_advanced',
                    'payload' => json_encode(['to' => $stage]),
                    'created_at' => (clone $createdAt)->addHours($i * random_int(4, 24)),
                ];
            }
        }

        AuditLog::insert($logs);
    }
}
