<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Update fillable to include the new fields
    protected $fillable = [
        'order_id',
        'user_id',
        'total_amount', // Change 'amount' to 'total_amount'
        'amount_paid',
        'remaining_due', // Add remaining_due field
        'method'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
