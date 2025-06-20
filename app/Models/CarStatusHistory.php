<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'status',
        'notes'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
