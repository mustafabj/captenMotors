<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Events\EquipmentCostNotificationEvent;

class EquipmentCostNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'car_equipment_cost_id',
        'requested_by_user_id',
        'notified_user_id',
        'notification_type',
        'status',
        'message',
        'additional_data',
        'read_at'
    ];

    protected $casts = [
        'additional_data' => 'array',
        'read_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($notification) {
            // Broadcast the notification to the user
            event(new EquipmentCostNotificationEvent($notification, $notification->notified_user_id));
        });
    }

    // Relationships
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function carEquipmentCost(): BelongsTo
    {
        return $this->belongsTo(CarEquipmentCost::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function notifiedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'notified_user_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('notified_user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now()
        ]);
    }

    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }

    public function isRead(): bool
    {
        return $this->status === 'read';
    }

    // Static methods for creating notifications
    public static function createApprovalRequest($car, $equipmentCost, $requestedByUser, $notifiedUser)
    {
        return self::create([
            'car_id' => $car->id,
            'car_equipment_cost_id' => $equipmentCost->id,
            'requested_by_user_id' => $requestedByUser->id,
            'notified_user_id' => $notifiedUser->id,
            'notification_type' => 'approval_requested',
            'message' => "{$requestedByUser->name} has requested approval for equipment cost: {$equipmentCost->description} - \${$equipmentCost->amount}",
            'additional_data' => [
                'car_model' => $car->model,
                'cost_description' => $equipmentCost->description,
                'cost_amount' => $equipmentCost->amount,
                'requested_by_name' => $requestedByUser->name,
                'timestamp' => now()->toISOString()
            ]
        ]);
    }

    public static function createApprovalResponse($car, $equipmentCost, $requestedByUser, $notifiedUser, $action, $adminName)
    {
        $messages = [
            'approved' => "Your equipment cost request '{$equipmentCost->description}' has been approved by {$adminName}",
            'rejected' => "Your equipment cost request '{$equipmentCost->description}' has been rejected by {$adminName}",
            'transferred' => "Your equipment cost request '{$equipmentCost->description}' has been transferred to other costs by {$adminName}"
        ];

        return self::create([
            'car_id' => $car->id,
            'car_equipment_cost_id' => $equipmentCost->id,
            'requested_by_user_id' => $requestedByUser->id,
            'notified_user_id' => $notifiedUser->id,
            'notification_type' => $action,
            'message' => $messages[$action] ?? "Your equipment cost request has been {$action}",
            'additional_data' => [
                'car_model' => $car->model,
                'cost_description' => $equipmentCost->description,
                'cost_amount' => $equipmentCost->amount,
                'admin_name' => $adminName,
                'action' => $action,
                'timestamp' => now()->toISOString()
            ]
        ]);
    }

    public static function createTransferNotification($car, $equipmentCost, $requestedByUser, $adminUser)
    {
        return self::create([
            'car_id' => $car->id,
            'car_equipment_cost_id' => $equipmentCost->id,
            'requested_by_user_id' => $requestedByUser->id,
            'notified_user_id' => $requestedByUser->id,
            'notification_type' => 'transferred',
            'message' => "Your equipment cost request '{$equipmentCost->description}' has been transferred to other costs by {$adminUser->name}",
            'additional_data' => [
                'car_model' => $car->model,
                'cost_description' => $equipmentCost->description,
                'cost_amount' => $equipmentCost->amount,
                'admin_name' => $adminUser->name,
                'action' => 'transferred',
                'timestamp' => now()->toISOString()
            ]
        ]);
    }
}
