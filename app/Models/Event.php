<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'title', 'slug', 'description',
        'location', 'event_date', 'event_end_date',
        'ticket_price', 'total_stock', 'available_stock',
        'status', 'organizer_name', 'banner_url'
    ];

    protected $casts = [
        'event_date'     => 'datetime',
        'event_end_date' => 'datetime',
        'ticket_price'   => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function decreaseStock(int $quantity = 1): bool
    {
        if ($this->available_stock < $quantity) {
            return false;
        }
        $this->decrement('available_stock', $quantity);
        return true;
    }
}