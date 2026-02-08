<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'user_id',
        'order_id',
        'reg',
        'currency',
        'transaction_id',
        'total',
        'discount',
        'vat',
        'payable',
        'pay',
        'due',
    ];

    protected $casts = [
        'date'     => 'date',
        'total'    => 'decimal:2',
        'discount' => 'decimal:2',
        'vat'      => 'decimal:2',
        'payable'  => 'decimal:2',
        'pay'      => 'decimal:2',
        'due'      => 'decimal:2',
    ];
}
