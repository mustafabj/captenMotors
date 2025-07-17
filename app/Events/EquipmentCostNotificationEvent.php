<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\EquipmentCostNotification;

class EquipmentCostNotificationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(EquipmentCostNotification $notification, $userId)
    {
        $this->notification = $notification;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channel = new PrivateChannel('user.' . $this->userId . '.equipment-cost-notifications');
        
        // Debug logging
        Log::info('Broadcasting event', [
            'channel' => 'user.' . $this->userId . '.equipment-cost-notifications',
            'notification_id' => $this->notification->id,
            'user_id' => $this->userId
        ]);
        
        return [$channel];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'notification' => [
                'id' => $this->notification->id,
                'notification_type' => $this->notification->notification_type,
                'message' => $this->notification->message,
                'status' => $this->notification->status,
                'created_at' => $this->notification->created_at,
                'car_id' => $this->notification->car_id,
                'car_equipment_cost_id' => $this->notification->car_equipment_cost_id,
                'additional_data' => $this->notification->additional_data,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'equipment-cost-notification';
    }
}
