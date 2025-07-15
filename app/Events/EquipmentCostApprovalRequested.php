<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Car;
use App\Models\CarEquipmentCost;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EquipmentCostApprovalRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $car;
    public $cost;
    public $requestedBy;
    public $adminUsers;

    /**
     * Create a new event instance.
     */
    public function __construct(Car $car, CarEquipmentCost $cost, User $requestedBy)
    {
        $this->car = $car;
        $this->cost = $cost;
        $this->requestedBy = $requestedBy;
        $this->adminUsers = User::getAdmins();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];
        
        // Broadcast to all admin users
        foreach ($this->adminUsers as $admin) {
            $channels[] = new PrivateChannel('notifications.' . $admin->id);
        }
        
        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $data = [
            'type' => 'equipment_cost_approval_requested',
            'title' => 'Equipment Cost Approval Requested',
            'message' => "{$this->requestedBy->name} has requested approval for equipment cost: {$this->cost->description} - \${$this->cost->amount}",
            'data' => [
                'car_id' => $this->car->id,
                'car_model' => $this->car->model,
                'cost_id' => $this->cost->id,
                'cost_description' => $this->cost->description,
                'cost_amount' => $this->cost->amount,
                'requested_by_id' => $this->requestedBy->id,
                'requested_by_name' => $this->requestedBy->name,
                'timestamp' => now()->toISOString()
            ]
        ];
        
        Log::info('Broadcasting equipment cost approval request', $data);
        
        return $data;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'equipment.cost.approval.requested';
    }
}
