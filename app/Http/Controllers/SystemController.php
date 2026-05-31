<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Citizen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function utama(): View
    {
        $role = Auth::user()->role ?? 'officer';

        return match ($role) {
            'admin'      => view('system.utama-admin'),
            'supervisor' => view('system.utama-supervisor'),
            default      => view('system.utama'),
        };
    }

    public function tapisan(Request $request): View
    {
        $this->gate(['officer', 'supervisor']);
        $activeNav = match ($request->get('doc')) {
            'birth' => 'birth',
            'marriage' => 'marriage',
            'mykad' => 'mykad',
            default => 'tapisan',
        };

        return view('system.tapisan', ['activeNav' => $activeNav]);
    }

    public function show(string $reference): View
    {
        $this->gate(['officer', 'supervisor']);
        $app = Application::with(['citizen', 'auditLogs.officer'])
            ->where('reference_number', $reference)
            ->firstOrFail();

        return view('system.tapisan-detail', ['app' => $app]);
    }

    public function approve(string $reference): RedirectResponse
    {
        $this->gate(['officer', 'supervisor']);
        $app = Application::where('reference_number', $reference)->firstOrFail();

        $this->advance($app, 'approved', 'Diluluskan secara manual oleh pegawai.');

        return redirect()
            ->route('system.tapisan.show', $reference)
            ->with('toast', "Permohonan {$reference} diluluskan.");
    }

    public function reject(string $reference): RedirectResponse
    {
        $this->gate(['officer', 'supervisor']);
        $app = Application::where('reference_number', $reference)->firstOrFail();

        $this->advance($app, 'rejected', 'Ditolak oleh pegawai.');

        return redirect()
            ->route('system.tapisan.show', $reference)
            ->with('toast', "Permohonan {$reference} ditolak.");
    }

    public function bulkApprove(Request $request): RedirectResponse
    {
        $this->gate(['officer', 'supervisor']);
        $ids = (array) $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('toast', 'Tiada permohonan dipilih.');
        }

        $count = 0;
        DB::transaction(function () use ($ids, &$count) {
            $apps = Application::whereIn('id', $ids)
                ->whereIn('status', ['received', 'verified', 'officer_review'])
                ->get();

            foreach ($apps as $app) {
                $this->advance($app, 'approved', 'Lulus pukal oleh pegawai.');
                $count++;
            }
        });

        return back()->with('toast', "{$count} permohonan diluluskan secara pukal.");
    }

    public function audit(): View
    {
        return view('system.audit');
    }

    public function wizardShow(Request $request): View
    {
        $this->gate(['officer', 'supervisor']);

        // "Pendaftaran Baru" is a parent over every registrable document. Without a
        // ?type it shows the document chooser; ?type=birth opens the Kelahiran wizard.
        $type = $request->get('type');
        if ($type === 'birth') {
            return view('system.wizard');
        }

        return view('system.pendaftaran-pilih');
    }

    public function wizardStore(Request $request): RedirectResponse
    {
        $this->gate(['officer', 'supervisor']);
        $data = $request->validate([
            'hospital_code' => ['required', 'string', 'max:32'],
            'medical_officer' => ['required', 'string', 'max:120'],
            'date_issued' => ['required', 'date'],
            'mother_name' => ['required', 'string', 'max:160'],
            'mother_ic' => ['required', 'string', 'max:14'],
            'mother_address' => ['required', 'string'],
            'baby_name' => ['required', 'string', 'max:160'],
            'baby_dob' => ['required', 'date'],
            'baby_gender' => ['required', 'in:M,F'],
            'baby_weight' => ['nullable', 'string'],
            'father_name' => ['nullable', 'string', 'max:160'],
            'father_ic' => ['nullable', 'string', 'max:14'],
        ]);

        $ref = 'APP-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        $app = Application::create([
            'reference_number' => $ref,
            'doc_type' => 'birth',
            'applicant_ic' => $data['mother_ic'],
            'applicant_name' => $data['baby_name'],
            'applicant_address' => $data['mother_address'],
            'status' => 'received',
            'ai_score' => 0.95,
            'ai_eta' => now()->addDays(5),
            'sla_state' => 'on_track',
        ]);

        AuditLog::create([
            'application_id' => $app->id,
            'officer_id' => Auth::id(),
            'action' => 'birth_registration_submitted',
            'payload' => [
                'from' => null,
                'to' => 'received',
                'notes' => "Pendaftaran kelahiran oleh hospital {$data['hospital_code']}, MO: {$data['medical_officer']}",
                'hospital' => $data['hospital_code'],
            ],
        ]);

        return redirect()
            ->route('system.tapisan.show', $ref)
            ->with('toast', "Permohonan {$ref} berjaya didaftarkan.");
    }

    public function kanban(): View
    {
        $this->gate(['officer', 'supervisor']);
        $role = Auth::user()->role ?? 'officer';

        return match ($role) {
            'admin'      => $this->kanbanAdmin(),
            'supervisor' => $this->kanbanSupervisor(),
            default      => $this->kanbanOfficer(),
        };
    }

    private function kanbanOfficer(): View
    {
        $columns = ['received', 'verified', 'officer_review', 'approved'];

        $apps = Application::with('citizen')
            ->whereIn('status', $columns)
            ->orderBy('created_at')
            ->get();

        // The 3 demo anchor cases — pin to top of their column + flag for the blink hint.
        $anchorRefs = collect(config('demo_cases'))->pluck('reference')->filter()->values()->all();

        $grouped = [];
        foreach ($columns as $c) {
            $grouped[$c] = $apps->where('status', $c)
                ->sortByDesc(fn ($a) => in_array($a->reference_number, $anchorRefs, true) ? 1 : 0)
                ->values();
        }

        return view('system.kanban', ['grouped' => $grouped, 'columns' => $columns, 'anchorRefs' => $anchorRefs]);
    }

    private function kanbanSupervisor(): View
    {
        $columns = ['received', 'verified', 'officer_review', 'approved'];

        $team = User::where('role', 'officer')->orderBy('name')->get();

        $apps = Application::with('citizen')
            ->whereIn('status', $columns)
            ->orderBy('created_at')
            ->get();

        $unassigned = $apps->whereNull('assigned_officer_id')->values();

        // Per-officer breakdown
        $byOfficer = [];
        foreach ($team as $officer) {
            $owned = $apps->where('assigned_officer_id', $officer->id);
            $byOfficer[$officer->id] = [
                'officer'  => $officer,
                'total'    => $owned->count(),
                'late'     => $owned->where('sla_state', 'breached')->count(),
                'risk'     => $owned->where('sla_state', 'at_risk')->count(),
                'recent'   => $owned->sortByDesc('created_at')->take(4)->values(),
                'by_col'   => collect($columns)->mapWithKeys(fn ($c) => [$c => $owned->where('status', $c)->count()])->toArray(),
            ];
        }

        $totals = [
            'team_size'   => $team->count(),
            'total_apps'  => $apps->count(),
            'unassigned'  => $unassigned->count(),
            'late'        => $apps->where('sla_state', 'breached')->count(),
        ];

        return view('system.kanban-supervisor', [
            'columns'    => $columns,
            'team'       => $team,
            'byOfficer'  => $byOfficer,
            'unassigned' => $unassigned,
            'totals'     => $totals,
        ]);
    }

    private function kanbanAdmin(): View
    {
        // System job queue — distinct domain from application Kanban.
        // Dummy jobs spanning backup / integration / cert / report.
        $now = now();

        $jobs = [
            // BERJALAN
            ['id' => 'JOB-001', 'name' => 'Sandaran Pangkalan Data Penuh',     'type' => 'backup',      'status' => 'running',  'progress' => 64, 'eta' => '4 min lagi',  'started' => $now->copy()->subMinutes(8),  'severity' => 'normal', 'owner' => 'system'],
            ['id' => 'JOB-002', 'name' => 'Sync MyKad Master · Delta Harian',  'type' => 'integration', 'status' => 'running',  'progress' => 87, 'eta' => '1 min lagi',  'started' => $now->copy()->subMinutes(3),  'severity' => 'normal', 'owner' => 'system'],
            // BERJADUAL
            ['id' => 'JOB-003', 'name' => 'Sijil SSL · Perbaharu untuk ' . config('brand.name'), 'type' => 'cert',   'status' => 'queued',   'progress' => 0,  'eta' => '02:00',       'started' => null,                          'severity' => 'normal', 'owner' => 'jadual'],
            ['id' => 'JOB-004', 'name' => 'Laporan Bulanan KPI · Eksport CSV', 'type' => 'report',      'status' => 'queued',   'progress' => 0,  'eta' => '23:00',       'started' => null,                          'severity' => 'normal', 'owner' => 'jadual'],
            ['id' => 'JOB-005', 'name' => 'Sync BNM-OFAC · Senarai Sekatan',   'type' => 'integration', 'status' => 'queued',   'progress' => 0,  'eta' => '06:00',       'started' => null,                          'severity' => 'normal', 'owner' => 'jadual'],
            ['id' => 'JOB-006', 'name' => 'Cuci Log Audit · Lebih 90 Hari',    'type' => 'cleanup',     'status' => 'queued',   'progress' => 0,  'eta' => '00:30',       'started' => null,                          'severity' => 'low',    'owner' => 'jadual'],
            // SELESAI
            ['id' => 'JOB-007', 'name' => 'Sandaran Pangkalan Data Penuh',     'type' => 'backup',      'status' => 'done',     'progress' => 100, 'eta' => '14 min',     'started' => $now->copy()->subHours(6),     'severity' => 'normal', 'owner' => 'system'],
            ['id' => 'JOB-008', 'name' => 'Sync Imigresen · Pintu Masuk',      'type' => 'integration', 'status' => 'done',     'progress' => 100, 'eta' => '38 saat',    'started' => $now->copy()->subMinutes(22),  'severity' => 'normal', 'owner' => 'system'],
            ['id' => 'JOB-009', 'name' => 'Putar Kunci API · MyKad Master',    'type' => 'cert',        'status' => 'done',     'progress' => 100, 'eta' => '2 saat',     'started' => $now->copy()->subHours(2),      'severity' => 'high',   'owner' => 'admin'],
            ['id' => 'JOB-010', 'name' => 'Laporan Mingguan SLA · Pasukan',    'type' => 'report',      'status' => 'done',     'progress' => 100, 'eta' => '47 saat',    'started' => $now->copy()->subDay(),          'severity' => 'normal', 'owner' => 'jadual'],
            // GAGAL
            ['id' => 'JOB-011', 'name' => 'Sync JPJ-LIC · Lesen Memandu',      'type' => 'integration', 'status' => 'failed',   'progress' => 42, 'eta' => '—',           'started' => $now->copy()->subMinutes(45),  'severity' => 'high',   'owner' => 'system', 'error' => 'TUMPAS · endpoint 504 timeout selepas 3 cubaan'],
            ['id' => 'JOB-012', 'name' => 'Sync PDRM-BG · Semakan Latar',      'type' => 'integration', 'status' => 'failed',   'progress' => 18, 'eta' => '—',           'started' => $now->copy()->subHours(3),     'severity' => 'medium', 'owner' => 'system', 'error' => 'PERLAHAN · 12 rekod tergantung, ulang manual diperlukan'],
        ];

        $columns = [
            'queued'  => 'Berjadual',
            'running' => 'Berjalan',
            'done'    => 'Selesai',
            'failed'  => 'Gagal',
        ];

        $grouped = [];
        foreach (array_keys($columns) as $c) {
            $grouped[$c] = array_values(array_filter($jobs, fn ($j) => $j['status'] === $c));
        }

        $totals = [
            'all'      => count($jobs),
            'running'  => count($grouped['running']),
            'queued'   => count($grouped['queued']),
            'done'     => count($grouped['done']),
            'failed'   => count($grouped['failed']),
        ];

        return view('system.kanban-admin', [
            'columns' => $columns,
            'grouped' => $grouped,
            'totals'  => $totals,
        ]);
    }

    public function kanbanMove(Request $request): RedirectResponse
    {
        $this->gate(['officer', 'supervisor']);
        return $this->kanbanMoveInternal($request);
    }

    private function kanbanMoveInternal(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string'],
            'to' => ['required', 'in:received,verified,officer_review,approved'],
        ]);

        $app = Application::where('reference_number', $data['reference'])->firstOrFail();
        $from = $app->status;

        if ($from === $data['to']) {
            return back();
        }

        $app->status = $data['to'];
        $app->save();

        AuditLog::create([
            'application_id' => $app->id,
            'officer_id' => Auth::id(),
            'action' => 'kanban_move',
            'payload' => [
                'from' => $from,
                'to' => $data['to'],
                'notes' => 'Dialih melalui papan Kanban.',
            ],
        ]);

        return back()->with('toast', "{$data['reference']} dialih ke " . (Application::STAGE_LABELS[$data['to']] ?? $data['to']));
    }

    public function statistik(): View
    {
        $now = now();
        $start = $now->copy()->subDays(29)->startOfDay();

        // 30-day daily series
        $daily = DB::table('applications')
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as n')
            ->groupBy('date')
            ->pluck('n', 'date');

        $series = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->format('Y-m-d');
            $series[] = ['date' => $d, 'n' => (int) ($daily[$d] ?? 0)];
        }

        // By doc type — real for 3 implemented, dummy for stubs (proposal-ready)
        $realByDoc = Application::selectRaw('doc_type, COUNT(*) as n')
            ->groupBy('doc_type')
            ->pluck('n', 'doc_type')
            ->toArray();

        $byDoc = [
            'birth'     => $realByDoc['birth']     ?? 0,
            'marriage'  => $realByDoc['marriage']  ?? 0,
            'mykad'     => $realByDoc['mykad']     ?? 0,
            'death'     => 18,
            'adoption'  => 4,
            'name_change' => 7,
        ];

        // Module-group activity (dummy for proposal — all sidebar groups represented)
        $modules = [
            ['key' => 'permohonan',    'label' => 'Permohonan Dokumen',   'count' => array_sum($byDoc), 'sub' => '6 jenis dokumen',         'icon' => 'inbox',   'cls' => 'is-teal'],
            ['key' => 'rekod',         'label' => 'Rekod Warganegara',    'count' => 4280,              'sub' => 'Carian + daftar + kemaskini', 'icon' => 'users',   'cls' => 'is-pine'],
            ['key' => 'perakuan',      'label' => 'Perakuan & Sijil',     'count' => 2140,              'sub' => 'Salinan, pendua, pengesahan', 'icon' => 'file',    'cls' => 'is-teal'],
            ['key' => 'kewarga',       'label' => 'Kewarganegaraan',      'count' => 78,                'sub' => 'Status, pengesahan, pelepasan', 'icon' => 'globe', 'cls' => 'is-orange'],
            ['key' => 'laporan',       'label' => 'Laporan',              'count' => 142,               'sub' => 'Statistik + eksport CSV',  'icon' => 'chart',   'cls' => 'is-pine'],
            ['key' => 'pentadbiran',   'label' => 'Pentadbiran',          'count' => 38,                'sub' => 'Pengguna, peranan, tetapan', 'icon' => 'cog',    'cls' => 'is-mute'],
        ];

        // By status
        $byStatus = Application::selectRaw('status, COUNT(*) as n')
            ->groupBy('status')
            ->pluck('n', 'status')
            ->toArray();

        // Top officers (by approve_application count, last 30d)
        $topOfficers = AuditLog::where('action', 'approve_application')
            ->whereNotNull('officer_id')
            ->where('created_at', '>=', $start)
            ->selectRaw('officer_id, COUNT(*) as n')
            ->groupBy('officer_id')
            ->orderByDesc('n')
            ->take(5)
            ->with('officer')
            ->get();

        // Heatmap 7 x 24 (last 14 days)
        $heatmap = array_fill(0, 7, array_fill(0, 24, 0));
        AuditLog::where('created_at', '>=', $now->copy()->subDays(14))
            ->select('created_at')
            ->orderBy('created_at')
            ->chunk(1000, function ($logs) use (&$heatmap) {
                foreach ($logs as $log) {
                    $dow = (int) $log->created_at->dayOfWeek;
                    $hr = (int) $log->created_at->hour;
                    $heatmap[$dow][$hr]++;
                }
            });

        // KPI totals
        $approvedPeriod = Application::whereIn('status', ['approved', 'issued'])
            ->where('updated_at', '>=', $start)
            ->count();

        $kpi = [
            'total' => Application::where('created_at', '>=', $start)->count(),
            'approved' => $approvedPeriod,
            'late' => Application::where('sla_state', 'breached')
                ->whereNotIn('status', ['issued', 'rejected'])
                ->count(),
            'avg_hours' => 2.4,
        ];

        return view('system.statistik', compact('series', 'byDoc', 'byStatus', 'topOfficers', 'heatmap', 'kpi', 'modules'));
    }

    private function advance(Application $app, string $newStatus, string $notes): void
    {
        $from = $app->status;
        $officerId = Auth::id();

        $app->status = $newStatus;
        $app->assigned_officer_id = $officerId;
        $app->sla_state = 'on_track';
        $app->save();

        AuditLog::create([
            'application_id' => $app->id,
            'officer_id' => $officerId,
            'action' => $newStatus === 'approved' ? 'approve_application' : 'reject_application',
            'payload' => [
                'from' => $from,
                'to' => $newStatus,
                'notes' => $notes,
            ],
        ]);

        // Auto-advance approved → issued after officer click (simulates downstream system)
        if ($newStatus === 'approved') {
            $app->status = 'issued';
            $app->save();

            AuditLog::create([
                'application_id' => $app->id,
                'officer_id' => null,
                'action' => 'issue_document',
                'payload' => [
                    'from' => 'approved',
                    'to' => 'issued',
                    'notes' => 'Auto-keluarkan selepas kelulusan.',
                ],
            ]);
        }
    }

    // ============== Sistem Wajib (LAMPIRAN A) — pitch/demo screens ==============

    private function gate(array $allowed): void
    {
        $role = Auth::user()->role ?? 'officer';
        abort_unless(in_array($role, $allowed, true), 403, 'Modul ini terhad untuk peranan: ' . implode(', ', $allowed));
    }

    /**
     * Resolve the active case for a Sistem Wajib screen.
     *  1. `?ref=APP-…` → that exact application (rich fixture if it's an anchor, else a
     *     case built dynamically from the real application — so ANY applicant threads correctly).
     *  2. `?case=birth|marriage|mykad` → the demo anchor for that module (used by the switcher).
     *  3. otherwise → the screen's natural default anchor, in BROWSE mode (not threaded).
     *
     * `threaded` is true ONLY when the officer arrived via "Semak" / the case breadcrumb
     * (i.e. ?ref or ?case present). When browsing a submodule from the sidebar, threaded
     * is false → screens show the full queue (everyone) and the KES SEMASA rail is hidden.
     */
    private function caseCtx(Request $request, string $default): array
    {
        $threaded = $request->filled('ref') || $request->filled('case');

        $ref = $request->get('ref');
        if ($ref) {
            foreach (config('demo_cases') as $anchor) {
                if ($anchor['reference'] === $ref) {
                    return $anchor + ['threaded' => true]; // one of the 3 curated demo cases
                }
            }
            $app = Application::where('reference_number', $ref)->first();
            if ($app) {
                return $this->dynamicCase($app) + ['threaded' => true];
            }
        }

        $key = $request->get('case');
        if (! in_array($key, ['birth', 'marriage', 'mykad'], true)) {
            $key = $default;
        }

        return config("demo_cases.$key") + ['threaded' => $threaded];
    }

    /** Detect ethnicity from a Malaysian name so a generated family stays single-race. */
    private function detectEthnicity(string $name): string
    {
        $n = ' ' . mb_strtolower($name) . ' ';
        if (str_contains($n, ' bin ') || str_contains($n, ' binti ')
            || preg_match('/\b(muhammad|mohd|nur|siti|ahmad|abdul|nor|wan|amir|hamzah|farah|nadia|salleh|roslan|karim|halimah|rohana|idris|wahab|omar|yusof|aminah|faridah|razak|hisham|mustafa|aishah|zulkifli|hafiz|khairul|faizal|huda|anuar)\b/u', $n)) {
            return 'malay';
        }
        if (preg_match('#a/[lp]#u', $n)
            || preg_match('/\b(kumar|raj|nair|pillai|singh|kaur|devi|subramaniam|krishnan|maniam|gopal|ramasamy|govindasamy|anand|munusamy|rajan|priya|anitha|deepa|shanti|suresh|ramesh|dinesh|vijay|kavitha|lakshmi|uma|mohan|bala|pillay|samy)\b/u', $n)) {
            return 'indian';
        }
        if (preg_match('/\b(tan|lim|wong|lee|ong|goh|teoh|chan|lau|yeoh|loh|chua|ng|chong|chen|khoo|yap|gan|low|chin|mei|ling|hui|wei|chee|boon|kok|hwa|peng|seng|jie|shan|ying|yen|qi|pei|siew|hoon|hock|huat|keong|han|aun|hua|xin|ting|fang|xiu)\b/u', $n)) {
            return 'chinese';
        }
        return 'malay'; // safe default for the Malaysian context
    }

    /** First 6 digits of a Malaysian IC → 'Y-m-d' (pivot at 2026 → 19xx for older). */
    private function icToDob(?string $ic): ?string
    {
        if (! $ic) {
            return null;
        }
        $d = preg_replace('/\D/', '', $ic);
        if (strlen($d) < 6) {
            return null;
        }
        $yy = (int) substr($d, 0, 2);
        $mm = (int) substr($d, 2, 2);
        $dd = (int) substr($d, 4, 2);
        if ($mm < 1 || $mm > 12 || $dd < 1 || $dd > 31) {
            return null;
        }
        $year = $yy <= 26 ? 2000 + $yy : 1900 + $yy;

        return sprintf('%04d-%02d-%02d', $year, $mm, $dd);
    }

    /**
     * Build a complete, single-race, fully-populated birth family from a real
     * Application — so a screenshotted dynamic case has NO blank mandatory fields,
     * NO "Direkod dari MyKad" placeholder, and a consistent ethnicity throughout.
     * The applicant citizen is treated as the MOTHER; the baby + father + 4
     * grandparents are generated deterministically in the same race.
     */
    private function buildBirthFamily(Application $app): array
    {
        $eth   = $this->detectEthnicity($app->applicant_name);
        $digits = preg_replace('/\D/', '', $app->reference_number) ?: '000000';
        $seed  = (int) substr($digits, -2);
        $pick  = fn (array $a, int $off = 0) => $a[($seed + $off) % count($a)];

        $babyF      = ($seed % 2) === 0;
        $babySym    = $babyF ? '♀' : '♂';
        $bornDate   = optional($app->created_at)->format('Y-m-d') ?? '2026-05-26';
        $bornTime   = sprintf('%02d:%02d', 6 + ($seed % 12), ($seed * 7) % 60);

        $jobs = ['Akauntan', 'Jurutera Awam', 'Guru', 'Doktor', 'Peniaga', 'Eksekutif Pemasaran', 'Arkitek', 'Peguam', 'Jururawat', 'Pegawai Bank'];

        // Generate a same-race male + female full name, derive baby from the father's name,
        // and build 4 race-consistent grandparents.
        if ($eth === 'chinese') {
            $surnames = ['Tan', 'Lim', 'Lee', 'Wong', 'Ong', 'Goh', 'Chua', 'Ng', 'Chong', 'Yeoh'];
            $maleG    = ['Boon Huat', 'Wei Jie', 'Chee Keong', 'Kok Wai', 'Beng Hock', 'Wei Han', 'Chin Aun', 'Seng Hua'];
            $femG     = ['Mei Ling', 'Hui Min', 'Siew Hoon', 'Pei Qi', 'Li Ying', 'Mei Xin', 'Wan Ting', 'Xiu Fang'];
            $genMale   = $pick($surnames, 1) . ' ' . $pick($maleG);
            $genFemale = $pick($surnames, 4) . ' ' . $pick($femG, 2);
            $babyFrom  = fn (string $f) => explode(' ', trim($f))[0] . ' ' . ($babyF ? $pick($femG, 3) : $pick($maleG, 5));
            $maternal  = fn (string $m) => $m;
            $lineageFn = fn (string $fFull, string $mFull) => [
                ['name' => explode(' ', trim($fFull))[0] . ' ' . $pick($maleG, 5), 'sym' => '♂', 'role' => 'Datuk (sebelah bapa)'],
                ['name' => $pick($surnames, 6) . ' ' . $pick($femG, 4), 'sym' => '♀', 'role' => 'Nenek (sebelah bapa)'],
                ['name' => explode(' ', trim($mFull))[0] . ' ' . $pick($maleG, 7), 'sym' => '♂', 'role' => 'Datuk (sebelah ibu)'],
                ['name' => $pick($surnames, 3) . ' ' . $pick($femG, 1), 'sym' => '♀', 'role' => 'Nenek (sebelah ibu)'],
            ];
            $keturunan = 'Cina';
            $agama     = 'Buddha';
        } elseif ($eth === 'indian') {
            $maleN  = ['Suresh', 'Rajesh', 'Vijay', 'Anand', 'Ramesh', 'Dinesh', 'Mohan', 'Bala'];
            $femN   = ['Priya', 'Anitha', 'Deepa', 'Kavitha', 'Shanti', 'Lakshmi', 'Devi', 'Uma'];
            $patN   = ['Raman', 'Gopal', 'Krishnan', 'Maniam', 'Subramaniam', 'Govindasamy', 'Pillai', 'Rajan'];
            $genMale   = $pick($maleN) . ' a/l ' . $pick($patN);
            $genFemale = $pick($femN, 2) . ' a/p ' . $pick($patN, 1);
            // child's patronymic = father's given name (first token before a/l)
            $babyFrom  = fn (string $f) => ($babyF ? $pick($femN, 3) : $pick($maleN, 5)) . ($babyF ? ' a/p ' : ' a/l ') . explode(' ', trim($f))[0];
            $maternal  = fn (string $m) => explode(' ', trim($m))[0];
            $lineageFn = fn (string $patFG, string $matFG) => [
                ['name' => $pick($patN) . ' a/l ' . $pick($patN, 5), 'sym' => '♂', 'role' => 'Datuk (sebelah bapa)'],
                ['name' => $pick($femN, 4) . ' a/p ' . $pick($patN, 6), 'sym' => '♀', 'role' => 'Nenek (sebelah bapa)'],
                ['name' => $pick($patN, 7) . ' a/l ' . $pick($patN, 2), 'sym' => '♂', 'role' => 'Datuk (sebelah ibu)'],
                ['name' => $pick($femN, 1) . ' a/p ' . $pick($patN, 3), 'sym' => '♀', 'role' => 'Nenek (sebelah ibu)'],
            ];
            $keturunan = 'India';
            $agama     = 'Hindu';
        } else { // malay
            $maleG = ['Amir Hamzah', 'Faizal', 'Khairul Anuar', 'Idris', 'Zulkifli', 'Hafiz', 'Azman', 'Rosli'];
            $femG  = ['Farah Nadia', 'Siti Aisyah', 'Nurul Huda', 'Halimah', 'Rohana', 'Aishah', 'Noraini', 'Zarina'];
            $patN  = ['Roslan', 'Karim', 'Salleh', 'Omar', 'Wahab', 'Idris', 'Hamzah', 'Yusof'];
            $genMale   = $pick($maleG) . ' bin ' . $pick($patN);
            $genFemale = $pick($femG, 2) . ' binti ' . $pick($patN, 1);
            // child: <given> bin/binti <father's given name> (everything before ' bin ')
            $fatherGivenOf = fn (string $f) => trim(preg_split('/\s+bin\s+/i', $f)[0]);
            $babyFrom  = fn (string $f) => ($babyF ? $pick($femG, 3) : $pick($maleG, 5)) . ($babyF ? ' binti ' : ' bin ') . $fatherGivenOf($f);
            $maternal  = fn (string $m) => $m;
            $lineageFn = fn (string $patFG, string $matFG) => [
                ['name' => $pick($patN) . ' bin ' . $pick($patN, 5), 'sym' => '♂', 'role' => 'Datuk (sebelah bapa)'],
                ['name' => $pick($femG, 4) . ' binti ' . $pick($patN, 6), 'sym' => '♀', 'role' => 'Nenek (sebelah bapa)'],
                ['name' => $pick($patN, 7) . ' bin ' . $pick($patN, 2), 'sym' => '♂', 'role' => 'Datuk (sebelah ibu)'],
                ['name' => $pick($femG, 1) . ' binti ' . $pick($patN, 3), 'sym' => '♀', 'role' => 'Nenek (sebelah ibu)'],
            ];
            $keturunan = 'Melayu';
            $agama     = 'Islam';
        }

        // Place the REAL applicant in the correct parent slot by their citizen gender;
        // generate the other parent (same race). Baby derives from the father.
        $realCitizen = Citizen::where('ic', $app->applicant_ic)->first();
        $realIsFather = (optional($realCitizen)->gender ?? 'F') === 'M';
        $realDob = $this->icToDob($app->applicant_ic) ?? '1992-04-18';

        // Generated parent: an adult close in age to the real parent (never future/child)
        $realYear  = (int) substr($realDob, 0, 4);
        $genYear   = max(1958, $realYear + (($seed % 7) - 3));
        if ($genYear === $realYear) { $genYear -= 2; }
        $gMonth    = (($seed * 3) % 12) + 1;
        $gDay      = (($seed * 5) % 28) + 1;
        $genDob    = sprintf('%04d-%02d-%02d', $genYear, $gMonth, $gDay);
        $stateCode = strlen($digits) >= 2 ? substr(str_pad($digits, 2, '0'), -2) : '14';
        $genIc     = sprintf('%02d%02d%02d-%02d-%04d', $genYear % 100, $gMonth, $gDay, ($seed % 14) + 1, 5001 + ($seed * 7) % 3998);

        if ($realIsFather) {
            $fatherName = $app->applicant_name; $fatherIc = $app->applicant_ic; $fatherDob = $realDob;
            $motherName = $genFemale;           $motherIc = $genIc;            $motherDob = $genDob;
        } else {
            $motherName = $app->applicant_name; $motherIc = $app->applicant_ic; $motherDob = $realDob;
            $fatherName = $genMale;             $fatherIc = $genIc;            $fatherDob = $genDob;
        }

        $baby    = $babyFrom($fatherName);
        $lineage = $lineageFn($fatherName, $maternal($motherName));

        $addr      = $app->applicant_address ?: '8, Jalan Pinggiran Putra 2, 43300 Seri Kembangan, Selangor';
        $hospName  = 'HOSPITAL PUTRAJAYA';
        $hospAddr  = 'Presint 7, 62250 Putrajaya, Wilayah Persekutuan';

        return [
            'name'   => $baby,
            'ic'     => null, // newborn — no IC yet
            'dob'    => $bornDate,
            'gender' => $babyF ? 'F' : 'M',
            'baby_sym' => $babySym,
            'lineage' => $lineage,
            'mother' => [
                'name' => $motherName, 'ic' => $motherIc, 'doc_type' => 'MyKad', 'negara' => 'Malaysia',
                'dob' => $motherDob, 'alamat' => $addr, 'keturunan' => $keturunan, 'pekerjaan' => $pick($jobs),
                'pemastautin' => 'Warganegara', 'warganegara' => 'Warganegara', 'agama' => $agama,
                'kahwin' => 'Berkahwin', 'tarikh_kahwin' => '2018-0' . (($seed % 9) + 1) . '-12',
            ],
            'father' => [
                'name' => $fatherName, 'ic' => $fatherIc, 'doc_type' => 'MyKad', 'negara' => 'Malaysia',
                'dob' => $fatherDob, 'alamat' => $addr, 'keturunan' => $keturunan, 'pekerjaan' => $pick($jobs, 3),
                'pemastautin' => 'Warganegara', 'warganegara' => 'Warganegara', 'agama' => $agama,
            ],
            'hospital' => [
                'name' => $hospName, 'address' => $hospAddr, 'mo' => 'Dr. ' . $genMale, 'weight' => (30 + $seed % 12) / 10 . ' kg',
                'sex' => $babyF ? 'P' : 'L', 'notif' => 'FHIR-' . substr($digits, -7), 'born_at' => $bornDate . ' ' . $bornTime,
            ],
            'borang' => [
                'form_no' => 'JPN.LM01', 'channel' => 'Portal MyJPN · dalam talian', 'submitted_at' => $bornDate . ' ' . $bornTime,
                'status' => 'Menunggu pengesahan biometrik di kaunter', 'baby_name' => $baby, 'sex' => $babyF ? 'Perempuan' : 'Lelaki',
                'dob' => $bornDate, 'born_time' => $bornTime, 'born_place' => $hospName, 'weight' => (30 + $seed % 12) / 10 . ' kg',
                'informant' => $motherName . ' (Ibu)',
            ],
        ];
    }

    /**
     * Build a case-context array (same shape as config/demo_cases.php) from a real
     * Application, so non-anchor applicants thread through the capability screens too.
     */
    private function dynamicCase(Application $app): array
    {
        $meta = [
            'birth'    => ['module' => 'Modul 01 · Kelahiran',                 'label' => 'Sijil Kelahiran',     'steps' => ['hospital', 'borang', 'biometric', 'familytree', 'sijil'], 'cc' => 'kelahiran-cc', 'event' => 'BirthRegistered',     'prefix' => 'KLH'],
            'marriage' => ['module' => 'Modul 05 · Perkahwinan & Perceraian',  'label' => 'Sijil Perkahwinan',   'steps' => ['familytree', 'kaveat', 'upacara', 'sijil'],          'cc' => 'kahwin-cc',    'event' => 'PerkahwinanRegistered', 'prefix' => 'PK'],
            'mykad'    => ['module' => 'Modul 04 · Kad Pengenalan',             'label' => 'MyKad · Gantian Hilang', 'steps' => ['lapor', 'biometric', 'abis', 'clms', 'kad', 'mydigital'], 'cc' => 'mykad-cc', 'event' => 'MyKadIssued', 'prefix' => 'MK'],
        ];
        $m = $meta[$app->doc_type] ?? $meta['mykad'];

        $digits  = preg_replace('/\D/', '', $app->reference_number) ?: '000000';
        $serial  = $m['prefix'] . '-2026-' . substr(str_pad($digits, 6, '0', STR_PAD_LEFT), -6);
        $score   = round(($app->ai_score ?? 0.9) * 100, 2);
        $block   = 1925016 + (intval(substr($digits, -4)) % 900);
        $hash    = '0x' . substr(md5($app->reference_number), 0, 10) . '...';
        $ts      = optional($app->created_at)->format('Y-m-d H:i:s') ?? '2026-05-28 09:00:00';
        $matched = $score >= 80;

        // Resolve the real couple from records (everything is digitally checked — no placeholders)
        $groomCitizen = Citizen::where('ic', $app->applicant_ic)->first();
        $brideCitizen = $app->spouse_ic ? Citizen::where('ic', $app->spouse_ic)->first() : null;
        $groomName = $groomCitizen->full_name ?? trim(explode('&', $app->applicant_name)[0]);
        $brideName = $app->spouse_name ?? ($brideCitizen->full_name ?? '—');

        // Deterministic (seeded) registrar + 2 witnesses, drawn from a verified pool
        $seed = (int) substr($digits, -2);
        $registrars = [
            ['Pn. Sasikala a/p Raman', 'REG-PK-0148'],
            ['En. David Chua Beng Hock', 'REG-PK-0203'],
            ['Pn. Agnes Lim Siew Hoon', 'REG-PK-0117'],
            ['Rev. Thomas Anand', 'REG-PK-0148 (Penolong)'],
        ];
        $witnessPool = [
            ['Kevin Tan Ah Lim', '870412-14-5231', 'Rakan pengantin lelaki'],
            ['Susan Wong Mei Yee', '910822-14-5668', 'Rakan pengantin perempuan'],
            ['Ramesh a/l Gopal', '850307-10-5419', 'Saksi keluarga'],
            ['Catherine Lee Pui San', '930115-07-5102', 'Kakak pengantin perempuan'],
            ['Daniel Ong Kah Wai', '890925-01-5337', 'Abang pengantin lelaki'],
            ['Anita a/p Maniam', '920518-10-5744', 'Saksi keluarga'],
        ];
        $reg = $registrars[$seed % count($registrars)];
        $w1 = $witnessPool[$seed % count($witnessPool)];
        $w2 = $witnessPool[($seed + 3) % count($witnessPool)];

        // Birth: build a complete, single-race family (no blanks, no placeholders)
        $birth = $app->doc_type === 'birth' ? $this->buildBirthFamily($app) : null;

        return [
            'key'        => $app->doc_type,
            'reference'  => $app->reference_number,
            'doc_type'   => $app->doc_type,
            'doc_label'  => $m['label'],
            'module'     => $m['module'],
            'name'       => $birth['name'] ?? $app->applicant_name,
            'ic'         => $birth ? $birth['ic'] : $app->applicant_ic,
            'dob'        => $birth['dob'] ?? optional(optional($groomCitizen)->dob)->format('Y-m-d') ?? $this->icToDob($app->applicant_ic),
            'gender'     => $birth['gender'] ?? (optional($groomCitizen)->gender),
            'lineage'    => $birth['lineage'] ?? null,
            'address'    => $app->applicant_address,
            'ai_score'   => $app->ai_score,
            'status'     => $app->status,
            'steps'      => $m['steps'],
            'card_no'    => $serial,
            'biometric'  => ['counter' => 'K-01 Putrajaya', 'officer' => Auth::user()?->name ?? 'Pegawai Kaunter', 'nfiq' => 95, 'duration' => '2 min 10s', 'started' => '—', 'note' => 'Sesi biometrik standard untuk permohonan ini.'],
            'abis'       => ['result' => $matched ? 'MATCH' : 'NO MATCH', 'tone' => $matched ? 'green' : 'amber', 'score' => $matched ? $score : 0.0, 'time' => 3.40, 'summary' => $matched ? 'Padanan automatik terhadap rekod warganegara sedia ada.' : 'Tiada padanan dalam pangkalan data — semakan manual.', 'candidates' => []],
            'blockchain' => ['cc' => $m['cc'], 'events' => [['block' => $block, 'hash' => $hash, 'event' => $m['event'], 'subj' => $serial, 'ts' => $ts]]],
            'mydigital'  => ['action' => 'AUTO_PROVISION', 'status' => 'success'],
            'religion'   => $app->doc_type === 'mykad' ? ['malay' => 'Islam', 'indian' => 'Hindu', 'chinese' => 'Buddha', 'default' => 'Kristian'][$this->detectEthnicity($app->applicant_name)] : null,
            'clms'       => ['serial' => $serial, 'type' => $app->doc_type === 'mykad' ? 'Gantian Hilang' : 'Permohonan', 'stage' => 'Cetakan', 'eta' => '10 min', 'priority' => 'normal'],
            'lapor'      => $app->doc_type === 'mykad'
                ? ['report_no' => 'RPT/PJ/2026/' . substr($digits, -7), 'station' => 'Balai Polis Putrajaya', 'report_date' => substr($ts, 0, 16), 'old_card_no' => 'MK-2014-' . substr($digits, -7), 'old_status' => 'DIBATALKAN', 'reason' => 'MyKad hilang — permohonan kad gantian.', 'fee' => 'RM 110.00', 'fee_status' => 'DIBAYAR', 'declared_by' => $app->applicant_name]
                : null,
            'card'       => $app->doc_type === 'mykad'
                ? ['card_no' => $serial, 'issue_date' => substr($ts, 0, 10), 'expiry' => 'SEUMUR HIDUP', 'citizenship' => 'WARGANEGARA', 'birth_place' => 'MALAYSIA', 'type_label' => 'Gantian Hilang']
                : null,
            'kaveat'     => $app->doc_type === 'marriage'
                ? ['ref' => 'KAV-2026-' . substr($digits, -4), 'lodged' => $ts, 'expires' => $ts, 'days_left' => 7, 'objections' => 0, 'tone' => 'amber']
                : null,
            'upacara'    => $app->doc_type === 'marriage'
                ? ['venue' => 'JPN Putrajaya · Kaunter Perkahwinan', 'venue_type' => 'jpn', 'venue_detail' => [], 'registrar' => $reg[0], 'registrar_id' => $reg[1], 'date' => substr($ts, 0, 10), 'time' => '10:30', 'reg_no' => $serial, 'status' => 'selesai', 'witnesses' => [['name' => $w1[0], 'ic' => $w1[1], 'rel' => $w1[2]], ['name' => $w2[0], 'ic' => $w2[1], 'rel' => $w2[2]]]]
                : null,
            'sijil'      => match ($app->doc_type) {
                'marriage' => ['cert_no' => 'JPN.KC02-' . substr($serial, 3), 'reg_no' => $serial, 'issued_date' => substr($ts, 0, 10), 'block' => $block, 'tx_hash' => $hash, 'ledger_wait' => '612 ms', 'copies' => 2],
                'birth'    => ['cert_no' => 'JPN.LM05-' . substr($serial, 4), 'reg_no' => $serial, 'mykid_no' => 'MYKID-' . substr($serial, 4), 'issued_date' => substr($ts, 0, 10), 'block' => $block, 'tx_hash' => $hash, 'ledger_wait' => '612 ms'],
                default    => null,
            },
            'borang'     => $birth['borang'] ?? null,
            'groom'      => ['name' => $groomName, 'ic' => $app->applicant_ic, 'status' => 'Bujang', 'dob' => optional(optional($groomCitizen)->dob)->format('Y-m-d')],
            'bride'      => ['name' => $brideName, 'ic' => $app->spouse_ic ?? '—', 'status' => 'Bujang', 'dob' => optional(optional($brideCitizen)->dob)->format('Y-m-d')],
            'mother'     => $birth['mother'] ?? ['name' => $groomCitizen->full_name ?? $app->applicant_name, 'ic' => $app->applicant_ic],
            'father'     => $birth['father'] ?? ['name' => '—', 'ic' => '—'],
            'hospital'   => $birth['hospital'] ?? null,
            'agencies'   => ['MAMPU', 'PDRM'],
        ];
    }

    public function abisMatch(Request $r): View         { $this->gate(['officer','supervisor','admin']); return view('system.abis-match',         ['case' => $this->caseCtx($r, 'mykad')]); }
    public function biometricCapture(Request $r): View  { $this->gate(['officer','supervisor','admin']); return view('system.biometric-capture',  ['case' => $this->caseCtx($r, 'mykad')]); }
    public function kaveatBoard(Request $r): View       { $this->gate(['officer','supervisor','admin']); return view('system.kaveat-board',       ['case' => $this->caseCtx($r, 'marriage')]); }
    public function upacara(Request $r): View           { $this->gate(['officer','supervisor']);         return view('system.upacara-perkahwinan', ['case' => $this->caseCtx($r, 'marriage')]); }
    public function borang(Request $r): View            { $this->gate(['officer','supervisor']);         return view('system.borang-kelahiran',    ['case' => $this->caseCtx($r, 'birth')]); }

    public function sijil(Request $r): View
    {
        $this->gate(['officer', 'supervisor']);
        $case = $this->caseCtx($r, 'marriage');
        $view = $case['doc_type'] === 'birth' ? 'system.sijil-kelahiran' : 'system.sijil-perkahwinan';

        return view($view, ['case' => $case]);
    }
    public function subFungsiKatalog(): View            { $this->gate(['officer','supervisor','admin']); return view('system.sub-fungsi-katalog'); }
    public function hospitalPraDaftar(Request $r): View { $this->gate(['officer','supervisor','admin']); return view('system.hospital-pra-daftar', ['case' => $this->caseCtx($r, 'birth')]); }
    public function clmsPipeline(Request $r): View      { $this->gate(['officer','supervisor','admin']); return view('system.clms-pipeline',      ['case' => $this->caseCtx($r, 'mykad')]); }
    public function laporKehilangan(Request $r): View   { $this->gate(['officer','supervisor','admin']); return view('system.lapor-kehilangan',    ['case' => $this->caseCtx($r, 'mykad')]); }
    public function cardMyKad(Request $r): View         { $this->gate(['officer','supervisor']);         return view('system.kad-mykad',           ['case' => $this->caseCtx($r, 'mykad')]); }
    public function familyTree(Request $r): View
    {
        $this->gate(['officer', 'supervisor']);
        $case = $this->caseCtx($r, 'marriage');
        // Browse (sidebar): no fixed family — show a directory to pick whose salasilah to view.
        $directory = empty($case['threaded'])
            ? Application::with('citizen')->orderByDesc('created_at')->take(14)->get()
            : collect();

        return view('system.family-tree', ['case' => $case, 'directory' => $directory]);
    }

    public function blockchainLedger(Request $r): View  { $this->gate(['supervisor','admin']); return view('system.blockchain-ledger', ['case' => $this->caseCtx($r, 'mykad')]); }
    public function agensiIntegrasi(Request $r): View   { $this->gate(['supervisor','admin']); return view('system.agensi-integrasi',  ['case' => $this->caseCtx($r, 'mykad')]); }
    public function perkakasanStatus(): View            { $this->gate(['supervisor','admin']); return view('system.perkakasan-status'); }

    public function kafkaEvents(): View                 { $this->gate(['admin']); return view('system.kafka-events'); }
    public function mydigitalId(Request $r): View       { $this->gate(['admin']); return view('system.mydigital-id', ['case' => $this->caseCtx($r, 'mykad')]); }
}
