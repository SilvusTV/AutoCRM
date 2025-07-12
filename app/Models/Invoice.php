<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'company_id',
        'project_id',
        'invoice_number',
        'type',
        'status',
        'is_validated',
        'total_ht',
        'tva_rate',
        'total_ttc',
        'issue_date',
        'due_date',
        'payment_date',
        'payment_terms',
        'payment_method',
        'late_fees',
        'bank_account',
        'intro_text',
        'conclusion_text',
        'footer_text',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'is_validated' => 'boolean',
        'total_ht' => 'decimal:2',
        'tva_rate' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    /**
     * Get the client that owns the invoice.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the project that owns the invoice.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the company that owns the invoice.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the invoice lines for the invoice.
     */
    public function invoiceLines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * Calculate the total amount including tax.
     */
    public function calculateTotalTTC(): float
    {
        return $this->total_ht * (1 + ($this->tva_rate / 100));
    }

    /**
     * Generate a new invoice number with the format FA{YEAR}_XXX.
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = "FA{$year}_";

        // Find the highest invoice number for the current year
        $highestNumber = self::where('invoice_number', 'like', $prefix.'%')
            ->orderBy('invoice_number', 'desc')
            ->value('invoice_number');

        if ($highestNumber) {
            // Extract the numeric part and increment
            $number = (int) substr($highestNumber, -3);
            $number++;
        } else {
            $number = 1;
        }

        // Format with leading zeros
        return $prefix.str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Check if this is a quote.
     */
    public function isQuote(): bool
    {
        return $this->type === 'quote';
    }

    /**
     * Check if this is an invoice.
     */
    public function isInvoice(): bool
    {
        return $this->type === 'invoice';
    }

    /**
     * Calculate the total from invoice lines.
     */
    public function calculateTotalFromLines(): float
    {
        return $this->invoiceLines->sum('total_ht');
    }

    /**
     * Update the total based on invoice lines.
     */
    public function updateTotalFromLines(): void
    {
        $this->total_ht = $this->calculateTotalFromLines();
        $this->total_ttc = $this->calculateTotalTTC();
        $this->save();
    }

    /**
     * Check if the invoice is validated.
     */
    public function isValidated(): bool
    {
        return $this->is_validated;
    }

    /**
     * Validate the invoice.
     */
    public function validate(): void
    {
        $this->is_validated = true;
        $this->save();
    }

    /**
     * Get regular (non-expense) invoice lines.
     */
    public function regularLines()
    {
        return $this->invoiceLines()->where('is_expense', false);
    }

    /**
     * Get expense invoice lines.
     */
    public function expenseLines()
    {
        return $this->invoiceLines()->where('is_expense', true);
    }
}
