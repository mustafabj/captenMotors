<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsuranceExpiryNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'user_id',
        'notification_type',
        'status',
        'message',
        'days_until_expiry',
        'expiry_date',
        'read_at'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Get notification message based on type and days
     */
    public function getNotificationMessage()
    {
        $carModel = $this->car->model;
        $days = abs($this->days_until_expiry);
        
        switch ($this->notification_type) {
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