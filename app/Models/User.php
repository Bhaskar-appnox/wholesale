<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'isCommercial',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship with Order model.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relationship with Invoice model.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the payment terms for the user.
     */
    public function getPaymentTermsAttribute($value)
    {
        // This can be modified to return a more human-readable format or value
        return ucfirst($value);
    }

    /**
     * Update user's outstanding balance.
     */
    public function updateOutstandingBalance($amount)
    {
        $this->outstanding_balance += $amount;
        $this->save();
    }

    /**
     * Set user's credit limit.
     */
    public function setCreditLimit($limit)
    {
        $this->credit_limit = $limit;
        $this->save();
    }
}
