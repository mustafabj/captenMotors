<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Car extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'model',
        'vehicle_category',
        'color',
        'mileage',
        'manufacturing_year',
        'place_of_manufacture',
        'number_of_keys',
        'plate_number',
        'engine_capacity',
        'purchase_date',
        'purchase_price',
        'insurance_expiry_date',
        'expected_sale_price',
        'status',
        'bulk_deal_id'
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
        if (is_numeric($value)) {
            abort(404);
        }
        
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

    public function equipmentCostNotifications()
    {
        return $this->hasMany(EquipmentCostNotification::class);
    }

    public function otherCosts()
    {
        return $this->hasMany(OtherCost::class);
    }

    public function bulkDeal()
    {
        return $this->belongsTo(BulkDeal::class);
    }

    public function soldCar()
    {
        return $this->hasOne(SoldCar::class);
    }

    public function isSold()
    {
        return $this->status === 'sold' || $this->soldCar()->exists();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('car_license')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('car_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);
    }
    
}
