<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg',
        'date',
        'user_id',
        'transaction_id',
        'status', // unpaid|pending|paid|failed|canceled|processing
        'total',
        'paid_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
