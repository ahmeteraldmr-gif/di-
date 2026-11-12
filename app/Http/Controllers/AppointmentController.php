<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $patients = Patient::orderBy('full_name')->get(['id', 'full_name', 'phone']);
        $statusMeta = Appointment::STATUS_META;

        return view('appointments.index', compact('patients', 'statusMeta'));
    }

    /**
     * FullCalendar için randevuları JSON formatında döndürür.
     */
    public function getEvents(Request $request): JsonResponse
    {
        $query = Appointment::with('patient');

        if ($request->filled('start') && $request->filled('end')) {
            $start = Carbon::parse($request->string('start')->toString())->startOfDay();
            $end = Carbon::parse($request->string('end')->toString())->endOfDay();
            $query->whereBetween('appointment_date', [$start, $end]);
        }

        $events = $query->orderBy('appointment_date')->get()->map(function (Appointment $appointment) {
            return [
                'id' => $appointment->id,
                'title' => $appointment->patient?->full_name ?? "Hasta #{$appointment->patient_id}",
                'start' => $appointment->appointment_date->format('Y-m-d\TH:i:s'),
                'end' => $appointment->appointment_date->copy()->addMinutes(30)->format('Y-m-d\TH:i:s'),
                'color' => $appointment->status_color,
                'textColor' => '#FFFFFF',
                'extendedProps' => [
                    'patient_id' => $appointment->patient_id,
                    'patient_name' => $appointment->patient?->full_name ?? 'Bilinmeyen Hasta',
                    'phone' => $appointment->patient?->phone ?? '',
                    'status' => $appointment->status,
                    'status_label' => $appointment->status_label,
                    'description' => $appointment->description ?: 'Genel Muayene',
                    'formatted_time' => $appointment->appointment_date->format('d.m.Y H:i'),
                    'patient_url' => $appointment->patient ? route('patients.show', $appointment->patient) : null,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Takvim üzerinden yeni randevu oluşturur (AJAX).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'appointment_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $appointment = Appointment::create([
            'patient_id' => $validated['patient_id'],
            'appointment_date' => Carbon::parse($validated['appointment_date']),
            'status' => Appointment::STATUS_PENDING,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Randevu başarıyla oluşturuldu.',
            'appointment' => $appointment,
        ], 201);
    }

    /**
     * Saati geçmiş ancak hala 'Bekliyor' durumundaki randevuları bildirir (AJAX).
     */
    public function overdue(): JsonResponse
    {
        $overdue = Appointment::with('patient')
            ->where('status', Appointment::STATUS_PENDING)
            ->where('appointment_date', '<=', now())
            ->orderByDesc('appointment_date')
            ->limit(10)
            ->get()
            ->map(function (Appointment $appointment) {
                $minutesLate = (int) $appointment->appointment_date->diffInMinutes(now());

                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient?->full_name ?? "Hasta #{$appointment->patient_id}",
                    'phone' => $appointment->patient?->phone ?? '',
                    'appointment_time' => $appointment->appointment_date->format('d.m.Y H:i'),
                    'time_only' => $appointment->appointment_date->format('H:i'),
                    'minutes_late' => $minutesLate,
                    'description' => $appointment->description ?: 'Genel Muayene',
                    'patient_url' => $appointment->patient ? route('patients.show', $appointment->patient) : null,
                    'status_url' => route('appointments.update-status', $appointment),
                    'reschedule_url' => route('appointments.reschedule', $appointment),
                ];
            });

        return response()->json([
            'count' => $overdue->count(),
            'data' => $overdue,
        ]);
    }

    /**
     * Takvimde sürükle-bırak (eventDrop) veya "Ertele" işlemi ile randevu tarih/saatini günceller.
     */
    public function reschedule(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'appointment_date' => ['required', 'date'],
            'status' => ['nullable', 'string', Rule::in(array_keys(Appointment::STATUS_META))],
        ]);

        $newDate = Carbon::parse($validated['appointment_date']);
        $updates = ['appointment_date' => $newDate];

        if (!empty($validated['status'])) {
            $updates['status'] = $validated['status'];
        }

        $appointment->update($updates);

        $actionLabel = ($appointment->status === Appointment::STATUS_POSTPONED)
            ? 'Randevu ertelendi: '
            : 'Randevu saati güncellendi: ';

        return response()->json([
            'success' => true,
            'message' => $actionLabel . $newDate->format('d.m.Y H:i'),
            'appointment' => [
                'id' => $appointment->id,
                'status' => $appointment->status,
                'status_label' => $appointment->status_label,
                'color' => $appointment->status_color,
                'appointment_date' => $appointment->appointment_date->format('Y-m-d\TH:i:s'),
            ],
        ]);
    }

    /**
     * Modal üzerinden seçilen randevu durumunu günceller.
     */
    public function updateStatus(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys(Appointment::STATUS_META))],
        ]);

        $appointment->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Randevu durumu "' . $appointment->status_label . '" olarak güncellendi.',
            'appointment' => [
                'id' => $appointment->id,
                'status' => $appointment->status,
                'status_label' => $appointment->status_label,
                'color' => $appointment->status_color,
            ],
        ]);
    }
}
