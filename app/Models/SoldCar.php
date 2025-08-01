<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoldCar extends Model
{
    protected $fillable = [
        'car_id',
        'sale_price',
        'payment_method',
        'paid_amount',
        'remaining_amount',
        'attachment',
        'sold_by_user_id',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function soldByUser()
    {
        return $this->belongsTo(User::class, 'sold_by_user_id');
    }
}
