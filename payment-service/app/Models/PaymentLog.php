<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'message',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}