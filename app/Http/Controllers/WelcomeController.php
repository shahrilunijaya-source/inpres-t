<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Public-safe event labels for the homepage "live" ticker.
     *
     * Intentionally generic: the ticker shows that the system is alive without
     * leaking a reference number, document type, or any per-citizen detail.
     * Reference number + IC together unlock Semak Status, so refs must never
     * appear on a public page.
     */
    private const EVENT_LABELS = [
        'received'       => 'Permohonan baharu diterima',
        'verified'       => 'Dokumen disahkan automatik',
        'officer_review' => 'Semakan pegawai dijalankan',
        'approved'       => 'Permohonan diluluskan',
        'issued'         => 'Sijil dijana & dihantar',
    ];

    public function index(): View
    {
        // Live service stats — same query pattern the officer console uses,
        // so citizen-facing numbers stay truthful and in sync with the System.
        $processed  = Application::whereIn('status', ['approved', 'issued'])->count();
        $inProgress = Application::whereIn('status', ['received', 'verified', 'officer_review'])->count();
        $today      = Application::whereDate('created_at', today())->count();

        // SLA met = share of active applications still on track (non-identifying aggregate).
        $activeTotal  = max($inProgress, 1);
        $onTrack      = Application::whereIn('status', ['received', 'verified', 'officer_review'])
            ->where('sla_state', 'on_track')->count();
        $slaMet = (int) round($onTrack / $activeTotal * 100);

        return view('welcome', [
            'statProcessed'  => $processed,
            'statInProgress' => $inProgress,
            'statToday'      => $today,
            'statSlaMet'     => $slaMet,
            'statStages'     => count(Application::STAGES),
            'events'         => $this->liveEvents(),
        ]);
    }

    /**
     * Anonymized recent-activity feed: generic event + relative time only.
     * No reference number, no document type, no applicant data.
     */
    private function liveEvents(): array
    {
        return Application::latest('updated_at')->take(5)->get()->map(function (Application $a) {
            return [
                'label' => self::EVENT_LABELS[$a->status] ?? 'Permohonan dikemas kini',
                'ago'   => $this->relativeMalay($a->updated_at),
            ];
        })->all();
    }

    /** Compact Bahasa Melayu relative timestamp. */
    private function relativeMalay(\Illuminate\Support\Carbon $time): string
    {
        $mins = (int) $time->diffInMinutes(now());

        return match (true) {
            $mins < 1     => 'baru sahaja',
            $mins < 60    => $mins . ' min lalu',
            $mins < 1440  => intdiv($mins, 60) . ' jam lalu',
            default       => intdiv($mins, 1440) . ' hari lalu',
        };
    }
}
