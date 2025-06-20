<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarEquipmentCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'description',
        'amount',
        'cost_date',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cost_date' => 'date'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
} 