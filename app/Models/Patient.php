<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'tc_no',
        'full_name',
        'phone',
        'email',
        'birth_date',
        'gender',
        'medical_notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function teeth(): HasMany
    {
        return $this->hasMany(PatientTooth::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalDebtAttribute(): float
    {
        $total = $this->relationLoaded('teeth')
            ? $this->teeth->sum(fn (PatientTooth $t) => (float) ($t->price ?? 0))
            : (float) $this->teeth()->sum('price');

        return round($total, 2);
    }

    public function getTotalPaidAttribute(): float
    {
        $total = $this->relationLoaded('payments')
            ? $this->payments->sum(fn (Payment $p) => (float) $p->amount)
            : (float) $this->payments()->sum('amount');

        return round($total, 2);
    }

    public function getBalanceAttribute(): float
    {
        return round($this->total_debt - $this->total_paid, 2);
    }

    /**
     * Bakiye özeti dizisi döndürür.
     *
     * @return array<string, float>
     */
    public function financeSummary(): array
    {
        return [
            'total_debt' => $this->total_debt,
            'total_paid' => $this->total_paid,
            'balance' => $this->balance,
        ];
    }

    /**
     * Borçlandırmalar (ücretli diş işlemleri) ve tahsilatları kronolojik tek bir cari ekstrede birleştirir.
     *
     * @return array<int, array<string, mixed>>
     */
    public function financialLedger(): array
    {
        $teeth = $this->relationLoaded('teeth') ? $this->teeth : $this->teeth()->get();
        $payments = $this->relationLoaded('payments') ? $this->payments : $this->payments()->get();

        $charges = $teeth
            ->filter(fn (PatientTooth $tooth) => (float) ($tooth->price ?? 0) > 0)
            ->map(function (PatientTooth $tooth) {
                $date = $tooth->treated_at ?? $tooth->updated_at ?? $tooth->created_at;

                return [
                    'type' => 'charge',
                    'type_label' => 'Tedavi / Borç',
                    'title' => "Diş #{$tooth->tooth_number} • " . ($tooth->treatment_type ?: $tooth->status_label),
                    'method' => 'Tedavi Kaydı',
                    'notes' => $tooth->notes ?? '',
                    'amount' => round((float) $tooth->price, 2),
                    'date_formatted' => $date?->format('d.m.Y') ?? '—',
                    'sort_at' => $date?->timestamp ?? 0,
                ];
            });

        $credits = $payments->map(function (Payment $payment) {
            return [
                'type' => 'payment',
                'type_label' => 'Tahsilat',
                'title' => "Ödeme ({$payment->payment_method})",
                'method' => $payment->payment_method,
                'notes' => $payment->notes ?? '',
                'amount' => round((float) $payment->amount, 2),
                'date_formatted' => $payment->payment_date?->format('d.m.Y H:i') ?? '—',
                'sort_at' => $payment->payment_date?->timestamp ?? 0,
            ];
        });

        return $charges
            ->concat($credits)
            ->sortByDesc('sort_at')
            ->values()
            ->all();
    }

    /**
     * Ad Soyad, telefon veya TC Kimlik No ile hızlı arama.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('full_name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('tc_no', 'like', "%{$term}%");
        });
    }

    /**
     * 32 FDI dişi için varsayılan 'healthy' durumlarıyla birleştirilmiş harita döndürür.
     *
     * @return array<int, array<string, mixed>>
     */
    public function odontogramMap(): array
    {
        $existing = $this->relationLoaded('teeth')
            ? $this->teeth->keyBy('tooth_number')
            : $this->teeth()->get()->keyBy('tooth_number');

        $map = [];

        foreach (PatientTooth::FDI_TEETH as $number) {
            /** @var PatientTooth|null $tooth */
            $tooth = $existing->get($number);
            $status = $tooth?->status ?? PatientTooth::STATUS_HEALTHY;
            $meta = PatientTooth::STATUS_META[$status] ?? PatientTooth::STATUS_META[PatientTooth::STATUS_HEALTHY];

            $map[$number] = [
                'tooth_number' => $number,
                'status' => $status,
                'status_label' => $meta['label'],
                'color' => $meta['color'],
                'treatment_type' => $tooth?->treatment_type ?? '',
                'price' => $tooth?->price !== null ? (float) $tooth->price : null,
                'notes' => $tooth?->notes ?? '',
                'treated_at' => $tooth?->treated_at?->format('Y-m-d') ?? '',
                'treated_at_formatted' => $tooth?->treated_at?->format('d.m.Y') ?? '',
            ];
        }

        return $map;
    }
}
