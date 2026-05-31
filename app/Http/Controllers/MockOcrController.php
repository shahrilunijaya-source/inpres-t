<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mock "AI OCR" — citizen lookup by IC. Returns prefill data so the
 * Apply form can auto-populate, simulating an IC scan.
 */
class MockOcrController extends Controller
{
    public function lookup(Request $request): JsonResponse
    {
        $request->validate([
            'ic' => 'required|string|regex:/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/',
        ]);

        $citizen = Citizen::where('ic', $request->input('ic'))->first();

        if (! $citizen) {
            return response()->json([
                'found' => false,
                'message' => 'Tiada rekod citizen dijumpai untuk IC ini.',
            ], 404);
        }

        [$race, $religion] = $this->inferRaceReligion($citizen->full_name);

        // Some seeded addresses already bake in the postcode + state; only
        // append them when they are not already present, to avoid a doubled
        // "..., 43300 Selangor, 43300 Selangor" tail on the prefill.
        $address = $citizen->address;
        if (! str_contains($address, (string) $citizen->postcode)) {
            $address .= ', ' . $citizen->postcode . ' ' . $citizen->state;
        }

        return response()->json([
            'found' => true,
            'data' => [
                'full_name' => $citizen->full_name,
                'dob' => $citizen->dob->format('Y-m-d'),
                'gender' => $citizen->gender,
                'address' => $address,
                'postcode' => $citizen->postcode,
                'state' => $citizen->state,
                // Not columns on the citizens table — derived/synthesized so the
                // "AI pull" returns a complete Malaysian record. Everything is
                // auto-populated except occupation (applicant fills that).
                'race' => $race,
                'religion' => $religion,
                'resident' => 'Warganegara Malaysia',
                'father_name' => $this->deriveFatherName($citizen->full_name),
                'city' => $this->cityForState($citizen->state),
                'phone' => $this->fakePhone(),
                'citizenship' => 'Malaysia',
                'domicile' => 'Malaysia',
                'marital' => 'Bujang',
            ],
            'confidence' => round(mt_rand(880, 990) / 1000, 3),
            'processing_ms' => random_int(820, 1450),
        ]);
    }

    /**
     * Synthesize a plausible father's name from the applicant's name
     * (prototype demo only — keeps the same race convention).
     */
    private function deriveFatherName(string $name): string
    {
        $n = trim($name);

        // Malay: "<given> bin/binti <father>" -> father portion already in the name.
        if (preg_match('/\b(?:bin|binti)\s+(.+)$/i', $n, $m)) {
            return trim($m[1]);
        }
        // Indian: "<given> a/l|a/p <father>" -> father portion.
        if (preg_match('#\b(?:a/l|a/p)\s+(.+)$#i', $n, $m)) {
            return trim($m[1]);
        }
        // Chinese: keep the family surname, swap in a generic given name.
        $surnames = ['Lim', 'Tan', 'Wong', 'Lee', 'Ong', 'Goh', 'Chan', 'Lau', 'Yeoh', 'Loh', 'Chong', 'Ng', 'Teh', 'Khoo', 'Chong'];
        foreach (preg_split('/\s+/', $n) as $tok) {
            if (in_array($tok, $surnames, true)) {
                return $tok . ' Beng Hock';
            }
        }
        // Indian (no a/l marker, e.g. "Priya Nair") -> reuse the family token.
        $parts = preg_split('/\s+/', $n);
        if (str_contains(mb_strtolower($n), 'nair') || str_contains(mb_strtolower($n), 'kumar') || str_contains(mb_strtolower($n), 'raj')) {
            return 'Subramaniam ' . end($parts);
        }

        return 'Tan Beng Hock';
    }

    private function cityForState(string $state): string
    {
        return [
            'Kuala Lumpur' => 'Kuala Lumpur',
            'Selangor' => 'Shah Alam',
            'Pulau Pinang' => 'Kepala Batas',
            'Johor' => 'Johor Bahru',
            'Perak' => 'Ipoh',
            'Kedah' => 'Alor Setar',
            'Melaka' => 'Melaka',
            'Negeri Sembilan' => 'Seremban',
            'Pahang' => 'Kuantan',
            'Sabah' => 'Kota Kinabalu',
            'Sarawak' => 'Kuching',
        ][$state] ?? $state;
    }

    private function fakePhone(): string
    {
        $prefix = ['012', '013', '016', '017', '019'][array_rand(['012', '013', '016', '017', '019'])];

        return $prefix . '-' . random_int(200, 899) . random_int(1000, 9999);
    }

    /**
     * Rough name-based heuristic for keturunan + agama (prototype only).
     *
     * @return array{0: string, 1: string}
     */
    private function inferRaceReligion(string $name): array
    {
        $n = mb_strtolower($name);

        if (str_contains($n, 'bin ') || str_contains($n, 'binti ') || str_contains($n, 'abdul') || str_contains($n, 'mohd') || str_contains($n, 'nur') || str_contains($n, 'siti')) {
            return ['Melayu', 'Islam'];
        }
        if (str_contains($n, 'a/l') || str_contains($n, 'a/p') || str_contains($n, 'kumar') || str_contains($n, 'raj') || str_contains($n, 'nair') || str_contains($n, 'singh') || str_contains($n, 'kaur')) {
            return ['India', 'Hindu'];
        }
        if (str_contains($n, 'lim') || str_contains($n, 'tan') || str_contains($n, 'wong') || str_contains($n, 'lee') || str_contains($n, 'ong') || str_contains($n, 'goh') || str_contains($n, 'chan') || str_contains($n, 'lau') || str_contains($n, 'yeoh') || str_contains($n, 'loh')) {
            return ['Cina', 'Buddha'];
        }

        return ['Melayu', 'Islam'];
    }
}
