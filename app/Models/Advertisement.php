<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'user_id',
        'expiration_date',
        'offer_price',
        'sale_price',
        'status',
        'description'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'offer_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    // Relationships
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expiration_date', '>=', now()->toDateString());
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expiration_date->isPast();
    }

    public function isActive()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function markAsExpired()
    {
        if ($this->isExpired() && $this->status === 'active') {
            $this->update(['status' => 'expired']);
        }
    }

    public function markAsSold()
    {
        $this->update(['status' => 'sold']);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function getDaysUntilExpiration()
    {
        return $this->expiration_date->diffInDays(now(), false);
    }

    public function getProfit()
    {
        return $this->sale_price - $this->offer_price;
    }

    public function getProfitPercentage()
    {
        if ($this->offer_price > 0) {
            return (($this->sale_price - $this->offer_price) / $this->offer_price) * 100;
        }
        return 0;
    }
}
