<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'manufacturing_year',
        'number_of_keys',
        'chassis_number',
        'plate_number',
        'engine_capacity',
        'purchase_date',
        'insurance_expiry_date',
        'expected_sale_price',
        'status'
    ];

    protected $casts = [
        'manufacturing_year' => 'integer',
        'number_of_keys' => 'integer',
        'purchase_date' => 'date',
        'insurance_expiry_date' => 'date',
        'expected_sale_price' => 'decimal:2'
    ];

    public function options()
    {
        return $this->hasMany(CarOption::class);
    }

    public function inspection()
    {
        return $this->hasOne(CarInspection::class);
    }
}
