<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientTooth extends Model
{
    use HasFactory;

    protected $table = 'patient_teeth';

    public const STATUS_HEALTHY = 'healthy';
    public const STATUS_TREATED = 'treated';
    public const STATUS_PLANNED = 'planned';
    public const STATUS_MISSING = 'missing';

    public const FDI_TEETH = [
        // Üst Sağ (Q1)
        11, 12, 13, 14, 15, 16, 17, 18,
        // Üst Sol (Q2)
        21, 22, 23, 24, 25, 26, 27, 28,
        // Alt Sol (Q3)
        31, 32, 33, 34, 35, 36, 37, 38,
        // Alt Sağ (Q4)
        41, 42, 43, 44, 45, 46, 47, 48,
    ];

    public const STATUS_META = [
        self::STATUS_HEALTHY => [
            'label' => 'Sağlam',
            'color' => '#FACC15',
        ],
        self::STATUS_TREATED => [
            'label' => 'İşlem Yapıldı',
            'color' => '#EF4444',
        ],
        self::STATUS_PLANNED => [
            'label' => 'Tedavi Planlandı',
            'color' => '#3B82F6',
        ],
        self::STATUS_MISSING => [
            'label' => 'Çekilmiş / Eksik',
            'color' => '#6B7280',
        ],
    ];

    protected $fillable = [
        'patient_id',
        'tooth_number',
        'status',
        'treatment_type',
        'price',
        'notes',
        'treated_at',
    ];

    protected $casts = [
        'tooth_number' => 'integer',
        'price' => 'decimal:2',
        'treated_at' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function getColorAttribute(): string
    {
        return self::STATUS_META[$this->status]['color']
            ?? self::STATUS_META[self::STATUS_HEALTHY]['color'];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_META[$this->status]['label']
            ?? self::STATUS_META[self::STATUS_HEALTHY]['label'];
    }
}
