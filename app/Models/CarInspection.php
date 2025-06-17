<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'front_chassis_right',
        'front_chassis_left',
        'rear_chassis_right',
        'rear_chassis_left',
        'transmission',
        'motor',
        'body_notes'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
