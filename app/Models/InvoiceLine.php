<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'item_type',
        'is_expense',
        'description',
        'quantity',
        'unit_price',
        'discount_percent',
        'tva_rate',
        'total_ht',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_expense' => 'boolean',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'tva_rate' => 'decimal:2',
        'total_ht' => 'decimal:2',
    ];

    /**
     * Get the invoice that owns the invoice line.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the user that owns the invoice line.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the total amount excluding tax.
     */
    public function calculateTotalHT(): float
    {
        $subtotal = $this->quantity * $this->unit_price;

        // Apply discount if any
        if ($this->discount_percent > 0) {
            $subtotal = $subtotal * (1 - ($this->discount_percent / 100));
        }

        return $subtotal;
    }

    /**
     * Calculate the total amount including tax.
     */
    public function calculateTotalTTC(): float
    {
        $totalHT = $this->calculateTotalHT();

        // Use line-specific TVA rate if set, otherwise use invoice TVA rate
        $tvaRate = $this->tva_rate ?? $this->invoice->tva_rate;

        return $totalHT * (1 + ($tvaRate / 100));
    }
}
