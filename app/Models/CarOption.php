<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'name'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
