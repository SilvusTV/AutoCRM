<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class URSSAFDeclaration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'urssaf_declarations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'declared_revenue',
        'charge_rate',
        'charges_amount',
        'is_paid',
        'payment_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'declared_revenue' => 'decimal:2',
        'charge_rate' => 'decimal:2',
        'charges_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'payment_date' => 'date',
    ];

    /**
     * Get the user that owns the URSSAF declaration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the charges amount based on the declared revenue and charge rate.
     */
    public function calculateChargesAmount(): float
    {
        return $this->declared_revenue * ($this->charge_rate / 100);
    }
}
