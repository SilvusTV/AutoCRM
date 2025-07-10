<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'siret',
        'tva_number',
        'naf_code',
        'country',
    ];

    /**
     * Get the clients associated with the company.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get the invoices directly associated with the company.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
