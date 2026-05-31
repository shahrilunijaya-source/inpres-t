<?php

namespace App\Services;

use App\Models\Application;
use Carbon\Carbon;

/**
 * Mock AI ETA service — computes a plausible-looking ETA + confidence
 * for the Smart Tracker. Not real ML; choreographed for the demo.
 *
 * Inputs: application stage, AI score, officer workload (current queue),
 * SLA target for the doc type.
 */
class AiEtaService
{
    private const STAGE_PROGRESS = [
        'received'       => 0.05,
        'verified'       => 0.30,
        'officer_review' => 0.55,
        'approved'       => 0.85,
        'issued'         => 1.00,
        'rejected'       => 1.00,
    ];

    public function predict(Application $application): array
    {
        $eta = $application->ai_eta ?? $application->created_at;

        $stageProgress = self::STAGE_PROGRESS[$application->status] ?? 0.0;
        $score = (float) ($application->ai_score ?? 0.7);

        // Confidence: blend AI score with stage progress + SLA state
        $base = 0.55 + ($score * 0.35);

        $slaModifier = match ($application->sla_state) {
            'on_track' => 0.05,
            'at_risk'  => -0.05,
            'breached' => -0.15,
            default    => 0,
        };

        $confidence = max(0.05, min(0.99, $base + $slaModifier + ($stageProgress * 0.05)));

        // Officer load — count of active queue items
        $queueDepth = Application::whereIn('status', ['received', 'verified', 'officer_review'])
            ->where('id', '!=', $application->id)
            ->count();

        $reason = $this->buildReason($application, $confidence, $queueDepth);

        return [
            'eta' => $eta,
            'confidence' => round($confidence, 2),
            'queue_depth' => $queueDepth,
            'stage_progress' => $stageProgress,
            'reason' => $reason,
        ];
    }

    private function buildReason(Application $app, float $confidence, int $queueDepth): string
    {
        if ($app->status === 'issued') {
            return 'Dokumen anda telah dikeluarkan dan boleh dimuat turun.';
        }
        if ($app->status === 'rejected') {
            return 'Permohonan ditolak. Sila hubungi helpdesk untuk maklum balas terperinci.';
        }

        $confidencePct = (int) round($confidence * 100);

        if ($app->sla_state === 'breached') {
            return "Permohonan telah melepasi tempoh SLA. Pasukan kami sedang mendahulukan kes anda — anggaran kemas kini dalam 24 jam.";
        }

        if ($app->sla_state === 'at_risk') {
            return "Permohonan hampir tempoh SLA. AI menjangkakan {$confidencePct}% kebarangkalian siap mengikut jadual berdasarkan {$queueDepth} kes dalam barisan.";
        }

        return "AI menjangkakan {$confidencePct}% kebarangkalian siap mengikut jadual. {$queueDepth} kes dalam barisan pegawai.";
    }
}
