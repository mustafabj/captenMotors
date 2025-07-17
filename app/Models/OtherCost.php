<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtherCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'user_id',
        'description',
        'amount',
        'cost_date',
        'category',
        'notes'
    ];

    protected $casts = [
        'cost_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
