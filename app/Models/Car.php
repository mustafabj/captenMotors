<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'vehicle_category',
        'manufacturing_year',
        'place_of_manufacture',
        'number_of_keys',
        'chassis_number',
        'plate_number',
        'engine_capacity',
        'engine_type',
        'purchase_date',
        'purchase_price',
        'insurance_expiry_date',
        'expected_sale_price',
        'status'
    ];

    protected $casts = [
        'manufacturing_year' => 'integer',
        'number_of_keys' => 'integer',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'insurance_expiry_date' => 'date',
        'expected_sale_price' => 'decimal:2'
    ];

    protected $appends = ['slug'];

    public function getSlugAttribute()
    {
        return Str::slug($this->model . '-' . $this->manufacturing_year . '-' . $this->id);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Custom route model binding to only allow full slug
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Only allow full slug format, not just ID
        if (is_numeric($value)) {
            abort(404);
        }
        
        // Extract ID from slug and find the car
        $parts = explode('-', $value);
        $id = (int) end($parts);
        
        return $this->where('id', $id)->firstOrFail();
    }

    public function options()
    {
        return $this->hasMany(CarOption::class);
    }

    public function inspection()
    {
        return $this->hasOne(CarInspection::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(CarStatusHistory::class);
    }

    public function equipmentCosts()
    {
        return $this->hasMany(CarEquipmentCost::class);
    }
}
