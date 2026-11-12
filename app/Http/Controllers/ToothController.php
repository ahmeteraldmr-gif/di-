<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientTooth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ToothController extends Controller
{
    /**
     * Belirli bir hastanın tek bir diş kaydını JSON olarak döndürür.
     */
    public function show(Patient $patient, int $toothNumber): JsonResponse
    {
        abort_unless(in_array($toothNumber, PatientTooth::FDI_TEETH, true), 404, 'Geçersiz FDI diş numarası.');

        $tooth = $patient->teeth()->where('tooth_number', $toothNumber)->first();
        $status = $tooth?->status ?? PatientTooth::STATUS_HEALTHY;
        $meta = PatientTooth::STATUS_META[$status];

        return response()->json([
            'tooth' => [
                'tooth_number' => $toothNumber,
                'status' => $status,
                'status_label' => $meta['label'],
                'color' => $meta['color'],
                'treatment_type' => $tooth?->treatment_type ?? '',
                'notes' => $tooth?->notes ?? '',
                'treated_at' => $tooth?->treated_at?->format('Y-m-d') ?? '',
                'treated_at_formatted' => $tooth?->treated_at?->format('d.m.Y') ?? '',
            ],
        ]);
    }

    /**
     * AJAX ile diş durumunu oluşturur veya günceller (upsert).
     */
    public function upsert(Request $request, Patient $patient): JsonResponse
    {
        $validated = $request->validate([
            'tooth_number' => ['required', 'integer', Rule::in(PatientTooth::FDI_TEETH)],
            'status' => ['required', 'string', Rule::in(array_keys(PatientTooth::STATUS_META))],
            'treatment_type' => ['nullable', 'string', 'max:150'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'treated_at' => ['nullable', 'date'],
        ]);

        $treatedAt = $validated['treated_at'] ?? null;
        if ($treatedAt === null && $validated['status'] === PatientTooth::STATUS_TREATED) {
            $treatedAt = now()->toDateString();
        }

        $tooth = PatientTooth::updateOrCreate(
            [
                'patient_id' => $patient->id,
                'tooth_number' => (int) $validated['tooth_number'],
            ],
            [
                'status' => $validated['status'],
                'treatment_type' => $validated['treatment_type'] ?? null,
                'price' => array_key_exists('price', $validated) && $validated['price'] !== null
                    ? round((float) $validated['price'], 2)
                    : null,
                'notes' => $validated['notes'] ?? null,
                'treated_at' => $treatedAt,
            ]
        );

        $patient->load(['teeth', 'payments']);

        return response()->json([
            'success' => true,
            'message' => "{$tooth->tooth_number} numaralı diş durumu güncellendi.",
            'tooth' => [
                'id' => $tooth->id,
                'tooth_number' => $tooth->tooth_number,
                'status' => $tooth->status,
                'status_label' => $tooth->status_label,
                'color' => $tooth->color,
                'treatment_type' => $tooth->treatment_type ?? '',
                'price' => $tooth->price !== null ? (float) $tooth->price : null,
                'notes' => $tooth->notes ?? '',
                'treated_at' => $tooth->treated_at?->format('Y-m-d') ?? '',
                'treated_at_formatted' => $tooth->treated_at?->format('d.m.Y') ?? '',
            ],
            'finance' => $patient->financeSummary(),
            'ledger' => $patient->financialLedger(),
        ]);
    }
}
