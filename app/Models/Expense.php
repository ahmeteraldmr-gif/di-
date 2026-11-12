<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'Malzeme & Sarf',
        'Laboratuvar',
        'Kira & Aidat',
        'Personel & Maaş',
        'Fatura (Elektrik / Su / İnternet)',
        'Vergi & Muhasebe',
        'Cihaz & Bakım',
        'Diğer',
    ];

    protected $fillable = [
        'title',
        'category',
        'amount',
        'payment_method',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'datetime',
    ];
}
