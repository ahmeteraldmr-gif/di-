<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NO_SHOW = 'no_show';
    public const STATUS_POSTPONED = 'postponed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_META = [
        self::STATUS_PENDING => [
            'label' => 'Bekliyor',
            'color' => '#EAB308',
        ],
        self::STATUS_COMPLETED => [
            'label' => 'Geldi',
            'color' => '#10B981',
        ],
        self::STATUS_NO_SHOW => [
            'label' => 'Gelmedi',
            'color' => '#EF4444',
        ],
        self::STATUS_POSTPONED => [
            'label' => 'Ertelendi',
            'color' => '#8B5CF6',
        ],
        self::STATUS_CANCELLED => [
            'label' => 'İptal Edildi',
            'color' => '#6B7280',
        ],
    ];

    protected $fillable = [
        'patient_id',
        'appointment_date',
        'status',
        'description',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_META[$this->status]['label']
            ?? self::STATUS_META[self::STATUS_PENDING]['label'];
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_META[$this->status]['color']
            ?? self::STATUS_META[self::STATUS_PENDING]['color'];
    }
}
