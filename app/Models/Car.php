<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\InsuranceExpiryNotification;
use App\Models\User;

class Car extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'model',
        'vehicle_category',
        'color',
        'odometer',
        'manufacturing_year',
        'place_of_manufacture',
        'number_of_keys',
        'plate_number',
        'engine_capacity',
        'engine_type',
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

    public function insuranceExpiryNotifications()
    {
        return $this->hasMany(InsuranceExpiryNotification::class);
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

    /**
     * Check if insurance is expired
     */
    public function isInsuranceExpired()
    {
        if (!$this->insurance_expiry_date) {
            return false;
        }
        
        return $this->insurance_expiry_date->isPast();
    }

    /**
     * Check if insurance is expiring soon (within specified days)
     */
    public function isInsuranceExpiringSoon($days = 30)
    {
        if (!$this->insurance_expiry_date) {
            return false;
        }
        
        $days = (int) $days;
        $expiryDate = $this->insurance_expiry_date;
        $now = \Carbon\Carbon::now();
        
        return $expiryDate->isFuture() && $expiryDate->diffInDays($now) <= $days;
    }

    /**
     * Get days until insurance expires (negative if expired)
     */
    public function getDaysUntilInsuranceExpiry()
    {
        if (!$this->insurance_expiry_date) {
            return null;
        }
        
        return $this->insurance_expiry_date->diffInDays(\Carbon\Carbon::now(), false);
    }

    /**
     * Get insurance status for display
     */
    public function getInsuranceStatus()
    {
        if (!$this->insurance_expiry_date) {
            return [
                'status' => 'not_set',
                'class' => 'kt-badge-secondary',
                'text' => 'Not Set',
                'days' => null
            ];
        }

        $daysUntilExpiry = $this->getDaysUntilInsuranceExpiry();

        if ($daysUntilExpiry > 0) {
            // Expired
            return [
                'status' => 'expired',
                'class' => 'kt-badge-danger',
                'text' => 'Expired',
                'days' => $daysUntilExpiry
            ];
        } elseif ($daysUntilExpiry >= -7) {
            // Expiring within 7 days
            return [
                'status' => 'critical',
                'class' => 'kt-badge-danger',
                'text' => 'Critical',
                'days' => abs($daysUntilExpiry)
            ];
        } elseif ($daysUntilExpiry >= -30) {
            // Expiring within 30 days
            return [
                'status' => 'warning',
                'class' => 'kt-badge-warning',
                'text' => 'Warning',
                'days' => abs($daysUntilExpiry)
            ];
        } else {
            // Valid
            return [
                'status' => 'valid',
                'class' => 'kt-badge-success',
                'text' => 'Valid',
                'days' => abs($daysUntilExpiry)
            ];
        }
    }

    /**
     * Scope to get cars with expiring insurance
     */
    public function scopeInsuranceExpiringSoon($query, $days = 30)
    {
        $days = (int) $days;
        return $query->whereNotNull('insurance_expiry_date')
                    ->where('insurance_expiry_date', '<=', \Carbon\Carbon::now()->addDays($days))
                    ->where('insurance_expiry_date', '>=', \Carbon\Carbon::now());
    }

    /**
     * Scope to get cars with expired insurance
     */
    public function scopeInsuranceExpired($query)
    {
        return $query->whereNotNull('insurance_expiry_date')
                    ->where('insurance_expiry_date', '<', \Carbon\Carbon::now());
    }

    /**
     * Create a test insurance notification for this car
     */
    public function createTestInsuranceNotification($type = 'warning')
    {
        $adminUsers = User::getAdmins();
        $daysUntilExpiry = $this->getDaysUntilInsuranceExpiry();
        
        foreach ($adminUsers as $user) {
            $message = $this->getNotificationMessage($this, $type, $daysUntilExpiry);
            
            return InsuranceExpiryNotification::create([
                'car_id' => $this->id,
                'user_id' => $user->id,
                'notification_type' => $type,
                'status' => 'unread',
                'message' => $message,
                'days_until_expiry' => $daysUntilExpiry,
                'expiry_date' => $this->insurance_expiry_date,
            ]);
        }
    }

    /**
     * Get notification message based on type and days
     */
    private function getNotificationMessage($car, $type, $daysUntilExpiry)
    {
        $carModel = $car->model;
        $days = abs($daysUntilExpiry);
        
        switch ($type) {
            case 'expired':
                return "Insurance for {$carModel} has expired {$days} days ago";
            case 'critical':
                return "Insurance for {$carModel} expires in {$days} days (CRITICAL)";
            case 'warning':
                return "Insurance for {$carModel} expires in {$days} days";
            default:
                return "Insurance notification for {$carModel}";
        }
    }
    
}
