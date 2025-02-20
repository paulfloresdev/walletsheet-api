<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'concept',
        'transaction_date',
        'accounting_date',
        'category_id',
        'place',
        'note',
        'account_id'
    ];

    // Relación con la categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación con la cuenta
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
