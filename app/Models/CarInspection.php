<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'chassis_inspection',
        'transmission',
        'motor',
        'body_notes'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
