<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_code',
        'status',
        'quantity',
        'total_price',
        'transaction_id',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    /**
     * Generate kode tiket unik (dipanggil saat status confirmed)
     */
    public static function generateTicketCode(): string
    {
        return 'TKT-' . strtoupper(Str::random(4)) . '-' . now()->format('YmdHis');
    }
}
