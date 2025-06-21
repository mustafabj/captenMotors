<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulkDeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'total_value',
        'status'
    ];

    protected $casts = [
        'total_value' => 'decimal:2'
    ];

    protected $appends = ['calculated_total_value'];

    public function getCalculatedTotalValueAttribute()
    {
        // Always calculate from cars for dynamic total
        return $this->cars()->sum('purchase_price');
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
