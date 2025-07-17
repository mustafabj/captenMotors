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
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
