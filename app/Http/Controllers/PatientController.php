<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\PatientTooth;
use App\Models\Payment;
use App\Models\TreatmentPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(): View
    {
        $todayAppointments = Appointment::with('patient')
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_date')
            ->get();

        $totalPatientsCount = Patient::count();

        $pendingTreatmentsCount = PatientTooth::where('status', PatientTooth::STATUS_PLANNED)->count();

        $completedTodayCount = $todayAppointments
            ->where('status', Appointment::STATUS_COMPLETED)
            ->count();

        $recentPatients = Patient::latest()
            ->take(6)
            ->get();

        return view('patients.index', compact(
            'todayAppointments',
            'totalPatientsCount',
            'pendingTreatmentsCount',
            'completedTodayCount',
            'recentPatients'
        ));
    }

    public function cancelAppointment(Appointment $appointment): RedirectResponse
    {
        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
        ]);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Randevu iptal edildi.');
    }

    /**
     * AJAX anlık hasta arama (Ad Soyad, Telefon veya TC Kimlik No).
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->string('q')->toString();

        $patients = Patient::query()
            ->search($query)
            ->orderBy('full_name')
            ->limit(15)
            ->get(['id', 'tc_no', 'full_name', 'phone', 'birth_date']);

        return response()->json([
            'data' => $patients->map(fn (Patient $patient) => [
                'id' => $patient->id,
                'tc_no' => $patient->tc_no,
                'full_name' => $patient->full_name,
                'phone' => $patient->phone,
                'birth_date' => $patient->birth_date?->format('d.m.Y'),
                'url' => route('patients.show', $patient),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validatePatient($request);

        $patient = Patient::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hasta kaydı başarıyla oluşturuldu.',
                'patient' => $patient,
                'redirect_url' => route('patients.show', $patient),
            ], 201);
        }

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Hasta kaydı başarıyla oluşturuldu.');
    }

    public function show(Patient $patient): View
    {
        $patient->load([
            'teeth',
            'payments' => fn ($query) => $query->orderByDesc('payment_date'),
            'appointments' => fn ($query) => $query->orderByDesc('appointment_date'),
        ]);

        $odontogram = $patient->odontogramMap();
        $statusMeta = PatientTooth::STATUS_META;
        $treatmentPrices = TreatmentPrice::orderBy('name')->get();
        $paymentMethods = Payment::METHODS;
        $financeSummary = $patient->financeSummary();
        $ledger = $patient->financialLedger();

        $toothHistory = $patient->teeth
            ->filter(fn (PatientTooth $tooth) => $tooth->status !== PatientTooth::STATUS_HEALTHY || filled($tooth->treatment_type) || filled($tooth->notes))
            ->sortByDesc(fn (PatientTooth $tooth) => $tooth->treated_at?->timestamp ?? $tooth->updated_at?->timestamp ?? 0)
            ->values();

        return view('patients.show', compact(
            'patient',
            'odontogram',
            'statusMeta',
            'toothHistory',
            'treatmentPrices',
            'paymentMethods',
            'financeSummary',
            'ledger'
        ));
    }

    public function update(Request $request, Patient $patient): JsonResponse|RedirectResponse
    {
        $validated = $this->validatePatient($request, $patient);

        $patient->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hasta bilgileri güncellendi.',
                'patient' => $patient->fresh(),
            ]);
        }

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Hasta bilgileri güncellendi.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Hasta kaydı silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePatient(Request $request, ?Patient $patient = null): array
    {
        return $request->validate([
            'tc_no' => [
                'nullable',
                'string',
                'size:11',
                Rule::unique('patients', 'tc_no')->ignore($patient?->id),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'medical_notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
