<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Patient;
use App\Models\PatientTooth;
use App\Models\Payment;
use App\Models\TreatmentPrice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinanceController extends Controller
{
    private const TURKISH_MONTHS = [
        1 => 'Ocak',
        2 => 'Şubat',
        3 => 'Mart',
        4 => 'Nisan',
        5 => 'Mayıs',
        6 => 'Haziran',
        7 => 'Temmuz',
        8 => 'Ağustos',
        9 => 'Eylül',
        10 => 'Ekim',
        11 => 'Kasım',
        12 => 'Aralık',
    ];

    /**
     * Klinik finans, gelir-gider ve ay sonu muhasebe ekranını gösterir.
     */
    public function index(Request $request): View
    {
        $monthInput = (string) $request->query('month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
            $monthInput = now()->format('Y-m');
        }

        try {
            $selectedMonth = Carbon::createFromFormat('Y-m-d', $monthInput . '-01')->startOfMonth();
        } catch (\Throwable) {
            $selectedMonth = now()->startOfMonth();
            $monthInput = $selectedMonth->format('Y-m');
        }

        $start = $selectedMonth->copy()->startOfMonth();
        $end = $selectedMonth->copy()->endOfMonth();

        $payments = Payment::with('patient:id,full_name,phone')
            ->whereBetween('payment_date', [$start, $end])
            ->orderByDesc('payment_date')
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$start, $end])
            ->orderByDesc('expense_date')
            ->get();

        $monthlyIncome = round((float) $payments->sum('amount'), 2);
        $monthlyExpense = round((float) $expenses->sum('amount'), 2);
        $monthlyNet = round($monthlyIncome - $monthlyExpense, 2);
        $profitMargin = $monthlyIncome > 0
            ? round(($monthlyNet / $monthlyIncome) * 100, 1)
            : 0.0;

        $monthlyBilled = round((float) PatientTooth::where('price', '>', 0)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('treated_at', [$start->toDateString(), $end->toDateString()])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->whereNull('treated_at')
                            ->whereBetween('updated_at', [$start, $end]);
                    });
            })
            ->sum('price'), 2);

        // Ödeme yöntemi bazlı kasa özeti (Gelir - Gider)
        $methodBreakdown = [];
        foreach (Payment::METHODS as $method) {
            $in = round((float) $payments->where('payment_method', $method)->sum('amount'), 2);
            $out = round((float) $expenses->where('payment_method', $method)->sum('amount'), 2);
            $methodBreakdown[$method] = [
                'income' => $in,
                'expense' => $out,
                'net' => round($in - $out, 2),
            ];
        }

        // Gider kategorisi dağılımı
        $categoryTotals = $expenses
            ->groupBy('category')
            ->map(function ($items, $category) use ($monthlyExpense) {
                $total = round((float) $items->sum('amount'), 2);
                $percent = $monthlyExpense > 0 ? round(($total / $monthlyExpense) * 100, 1) : 0.0;

                return [
                    'category' => $category,
                    'count' => $items->count(),
                    'total' => $total,
                    'percent' => $percent,
                ];
            })
            ->sortByDesc('total')
            ->values();

        // Tüm hastalar üzerinden açık alacaklar (cari bakiyeler)
        $allPatients = Patient::with([
            'teeth:id,patient_id,price',
            'payments:id,patient_id,amount',
        ])->orderBy('full_name')->get();

        $debtorPatients = $allPatients
            ->filter(fn (Patient $patient) => $patient->balance > 0)
            ->sortByDesc('balance')
            ->values();

        $totalReceivable = round((float) $debtorPatients->sum('balance'), 2);

        // Son 6 aylık ay sonu karşılaştırması (seçili ay bitiş noktası)
        $historyStart = $selectedMonth->copy()->subMonths(5)->startOfMonth();
        $rangePayments = Payment::whereBetween('payment_date', [$historyStart, $end])->get(['amount', 'payment_date']);
        $rangeExpenses = Expense::whereBetween('expense_date', [$historyStart, $end])->get(['amount', 'expense_date']);

        $paymentsByMonth = $rangePayments->groupBy(fn (Payment $p) => $p->payment_date->format('Y-m'));
        $expensesByMonth = $rangeExpenses->groupBy(fn (Expense $e) => $e->expense_date->format('Y-m'));

        $monthlyHistory = [];
        for ($i = 5; $i >= 0; $i--) {
            $cursor = $selectedMonth->copy()->subMonths($i);
            $key = $cursor->format('Y-m');
            $inc = round((float) ($paymentsByMonth->get($key)?->sum('amount') ?? 0), 2);
            $exp = round((float) ($expensesByMonth->get($key)?->sum('amount') ?? 0), 2);
            $monthlyHistory[] = [
                'month' => $key,
                'label' => $this->formatTurkishMonth($cursor),
                'income' => $inc,
                'expense' => $exp,
                'net' => round($inc - $exp, 2),
                'is_selected' => $key === $monthInput,
            ];
        }

        // Birleşik gelir + gider hareket listesi
        $incomeEntries = $payments->map(function (Payment $payment) {
            return [
                'id' => $payment->id,
                'type' => 'income',
                'title' => $payment->patient?->full_name ?? 'Hasta Tahsilatı',
                'patient_id' => $payment->patient_id,
                'subtitle' => $payment->notes ?: 'Hasta Tahsilatı',
                'category' => 'Hasta Tahsilatı',
                'method' => $payment->payment_method,
                'amount' => round((float) $payment->amount, 2),
                'date_formatted' => $payment->payment_date?->format('d.m.Y H:i') ?? '—',
                'sort_at' => $payment->payment_date?->timestamp ?? 0,
                'delete_url' => route('finance.payments.destroy', $payment),
            ];
        });

        $expenseEntries = $expenses->map(function (Expense $expense) {
            return [
                'id' => $expense->id,
                'type' => 'expense',
                'title' => $expense->title,
                'patient_id' => null,
                'subtitle' => $expense->notes ?: $expense->category,
                'category' => $expense->category,
                'method' => $expense->payment_method,
                'amount' => round((float) $expense->amount, 2),
                'date_formatted' => $expense->expense_date?->format('d.m.Y H:i') ?? '—',
                'sort_at' => $expense->expense_date?->timestamp ?? 0,
                'delete_url' => route('finance.expenses.destroy', $expense),
            ];
        });

        $ledger = $incomeEntries
            ->concat($expenseEntries)
            ->sortByDesc('sort_at')
            ->values();

        return view('finance.index', [
            'monthInput' => $monthInput,
            'monthLabel' => $this->formatTurkishMonth($selectedMonth),
            'prevMonth' => $selectedMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $selectedMonth->copy()->addMonth()->format('Y-m'),
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
            'monthlyNet' => $monthlyNet,
            'profitMargin' => $profitMargin,
            'monthlyBilled' => $monthlyBilled,
            'totalReceivable' => $totalReceivable,
            'incomeCount' => $payments->count(),
            'expenseCount' => $expenses->count(),
            'methodBreakdown' => $methodBreakdown,
            'categoryTotals' => $categoryTotals,
            'debtorPatients' => $debtorPatients,
            'allPatients' => $allPatients,
            'monthlyHistory' => $monthlyHistory,
            'ledger' => $ledger,
            'treatmentPrices' => TreatmentPrice::orderBy('name')->get(),
        ]);
    }

    /**
     * Yeni klinik gideri kaydeder.
     */
    public function storeExpense(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'payment_method' => ['required', 'string', Rule::in(Payment::METHODS)],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $expense = Expense::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'amount' => round((float) $validated['amount'], 2),
            'payment_method' => $validated['payment_method'],
            'expense_date' => Carbon::parse($validated['expense_date']),
            'notes' => $validated['notes'] ?? null,
        ]);

        $message = number_format((float) $expense->amount, 2, ',', '.') . ' ₺ gider kaydedildi.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'expense' => $expense,
            ], 201);
        }

        return redirect()
            ->route('finance.index', ['month' => $expense->expense_date->format('Y-m')])
            ->with('success', $message);
    }

    /**
     * Klinik gider kaydını siler.
     */
    public function destroyExpense(Request $request, Expense $expense): RedirectResponse|JsonResponse
    {
        $month = $expense->expense_date?->format('Y-m') ?? now()->format('Y-m');
        $title = $expense->title;
        $expense->delete();

        $message = "\"{$title}\" gider kaydı silindi.";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()
            ->route('finance.index', ['month' => $month])
            ->with('success', $message);
    }

    /**
     * Finans ekranından hızlı hasta tahsilatı (gelir) ekler.
     */
    public function storeQuickPayment(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'payment_method' => ['required', 'string', Rule::in(Payment::METHODS)],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);
        $payment = $patient->payments()->create([
            'amount' => round((float) $validated['amount'], 2),
            'payment_method' => $validated['payment_method'],
            'payment_date' => Carbon::parse($validated['payment_date']),
            'notes' => $validated['notes'] ?? null,
        ]);

        $message = $patient->full_name . ' için ' . number_format((float) $payment->amount, 2, ',', '.') . ' ₺ tahsilat kaydedildi.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'payment' => $payment,
            ], 201);
        }

        return redirect()
            ->route('finance.index', ['month' => $payment->payment_date->format('Y-m')])
            ->with('success', $message);
    }

    /**
     * Yanlış girilen bir tahsilat kaydını siler.
     */
    public function destroyPayment(Request $request, Payment $payment): RedirectResponse|JsonResponse
    {
        $month = $payment->payment_date?->format('Y-m') ?? now()->format('Y-m');
        $payment->delete();

        $message = 'Tahsilat kaydı silindi.';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()
            ->route('finance.index', ['month' => $month])
            ->with('success', $message);
    }

    /**
     * Hastaya yeni tahsilat (ödeme) ekler ve güncel cari özeti döndürür (AJAX).
     */
    public function addPayment(Request $request, Patient $patient): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'payment_method' => ['required', 'string', Rule::in(Payment::METHODS)],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $payment = $patient->payments()->create([
            'amount' => round((float) $validated['amount'], 2),
            'payment_method' => $validated['payment_method'],
            'payment_date' => Carbon::parse($validated['payment_date']),
            'notes' => $validated['notes'] ?? null,
        ]);

        $patient->load(['teeth', 'payments']);

        return response()->json([
            'success' => true,
            'message' => number_format((float) $payment->amount, 2, ',', '.') . ' ₺ tahsilat kaydedildi.',
            'payment' => [
                'id' => $payment->id,
                'amount' => (float) $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_date' => $payment->payment_date->format('d.m.Y H:i'),
                'notes' => $payment->notes ?? '',
            ],
            'finance' => $patient->financeSummary(),
            'ledger' => $patient->financialLedger(),
        ], 201);
    }

    /**
     * Klinik fiyat listesine yeni işlem tipi ve varsayılan ücret ekler.
     */
    public function storeTreatmentPrice(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('treatment_prices', 'name')],
            'default_price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
        ]);

        $treatmentPrice = TreatmentPrice::create([
            'name' => trim($validated['name']),
            'default_price' => round((float) $validated['default_price'], 2),
        ]);

        $message = "\"{$treatmentPrice->name}\" işlem tipi fiyat listesine eklendi.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'treatment_price' => [
                    'id' => $treatmentPrice->id,
                    'name' => $treatmentPrice->name,
                    'default_price' => (float) $treatmentPrice->default_price,
                ],
            ], 201);
        }

        return redirect()
            ->to(route('finance.index', array_filter(['month' => $request->input('month')])) . '#fiyat-listesi')
            ->with('success', $message);
    }

    /**
     * Klinik fiyat listesindeki bir işlemin adını ve varsayılan ücretini günceller.
     */
    public function updateTreatmentPrice(Request $request, TreatmentPrice $treatmentPrice): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('treatment_prices', 'name')->ignore($treatmentPrice->id)],
            'default_price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
        ]);

        $treatmentPrice->update([
            'name' => trim($validated['name']),
            'default_price' => round((float) $validated['default_price'], 2),
        ]);

        $message = "\"{$treatmentPrice->name}\" fiyatı " . number_format((float) $treatmentPrice->default_price, 2, ',', '.') . ' ₺ olarak güncellendi.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'treatment_price' => [
                    'id' => $treatmentPrice->id,
                    'name' => $treatmentPrice->name,
                    'default_price' => (float) $treatmentPrice->default_price,
                ],
            ]);
        }

        return redirect()
            ->to(route('finance.index', array_filter(['month' => $request->input('month')])) . '#fiyat-listesi')
            ->with('success', $message);
    }

    /**
     * Klinik fiyat listesinden bir işlem tipini siler.
     */
    public function destroyTreatmentPrice(Request $request, TreatmentPrice $treatmentPrice): RedirectResponse|JsonResponse
    {
        $name = $treatmentPrice->name;
        $treatmentPrice->delete();

        $message = "\"{$name}\" işlem tipi fiyat listesinden kaldırıldı.";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()
            ->to(route('finance.index', array_filter(['month' => $request->input('month')])) . '#fiyat-listesi')
            ->with('success', $message);
    }

    private function formatTurkishMonth(Carbon $date): string
    {
        $monthName = self::TURKISH_MONTHS[(int) $date->format('n')] ?? $date->format('F');

        return $monthName . ' ' . $date->format('Y');
    }
}
