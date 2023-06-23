<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'invoice_number',
        'amount',
        'vat',
        'issue_date',
        'due_date',
        'status'
    ];

    /**
     * Get the company associated with the invoice.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user associated with the invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
