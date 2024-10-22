<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'payment_terms',
        'order_id',  // If you have a foreign key relationship with orders
        'status',     // Assuming 'status' is also mass assignable
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Invoice


    // Relationship with Payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
