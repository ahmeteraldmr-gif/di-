<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const METHOD_CASH = 'Nakit';
    public const METHOD_CARD = 'Kredi Kartı';
    public const METHOD_TRANSFER = 'Havale/EFT';

    public const METHODS = [
        self::METHOD_CASH,
        self::METHOD_CARD,
        self::METHOD_TRANSFER,
    ];

    protected $fillable = [
        'patient_id',
        'amount',
        'payment_method',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
