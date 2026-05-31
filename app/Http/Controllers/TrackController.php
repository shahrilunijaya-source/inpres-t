<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\AiEtaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackController extends Controller
{
    public function __construct(private readonly AiEtaService $eta) {}

    public function search(): View
    {
        return view('track-search');
    }

    /**
     * Verify ownership (reference + matching IC) before granting access to a
     * tracker. On success the reference is added to a per-session allowlist so
     * the citizen — and only the citizen — can view it. IC is accepted by POST
     * only, never in a URL or query string.
     */
    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reference' => 'required|string|regex:/^APP-[0-9]{8}-[0-9]{4}$/',
            'applicant_ic' => 'required|string|regex:/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/',
        ]);

        $matches = Application::where('reference_number', $validated['reference'])
            ->where('applicant_ic', $validated['applicant_ic'])
            ->exists();

        // Generic failure — do not reveal whether the reference exists, to block
        // enumeration of valid reference numbers.
        if (! $matches) {
            return back()->withErrors([
                'reference' => 'No. rujukan atau No. Kad Pengenalan tidak sepadan.',
            ])->withInput();
        }

        $request->session()->push('tracked_refs', $validated['reference']);

        return redirect()->route('track.show', $validated['reference']);
    }

    public function show(Request $request, string $reference): View|RedirectResponse
    {
        if (! $this->isVerified($request, $reference)) {
            return redirect()->route('track.search')->withErrors([
                'reference' => 'Sila masukkan No. rujukan dan No. Kad Pengenalan untuk melihat status.',
            ]);
        }

        $application = Application::with(['citizen', 'assignedOfficer', 'auditLogs.officer'])
            ->where('reference_number', $reference)
            ->firstOrFail();

        $prediction = $this->eta->predict($application);

        return view('track', [
            'application' => $application,
            'prediction' => $prediction,
        ]);
    }

    public function status(Request $request, string $reference): JsonResponse
    {
        if (! $this->isVerified($request, $reference)) {
            return response()->json(['message' => 'Tidak dibenarkan.'], 403);
        }

        $application = Application::where('reference_number', $reference)->firstOrFail();
        $prediction = $this->eta->predict($application);

        return response()->json([
            'reference' => $application->reference_number,
            'status' => $application->status,
            'sla_state' => $application->sla_state,
            'stage_index' => $application->stageIndex(),
            'updated_at' => $application->updated_at?->toIso8601String(),
            'confidence' => $prediction['confidence'],
            'queue_depth' => $prediction['queue_depth'],
        ]);
    }

    /**
     * A reference is viewable only if it was proven owned this session — either
     * via verify() (reference + IC) or by submitting it through /apply.
     */
    private function isVerified(Request $request, string $reference): bool
    {
        return in_array($reference, $request->session()->get('tracked_refs', []), true);
    }
}
