<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'amount',
        'status',
        'payment_method',
    ];

    public function logs()
    {
        return $this->hasMany(PaymentLog::class);
    }
}