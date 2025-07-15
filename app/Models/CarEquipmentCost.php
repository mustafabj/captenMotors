<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarEquipmentCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'user_id',
        'description',
        'amount',
        'cost_date',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cost_date' => 'date'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the cost is pending approval
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the cost is approved
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the cost is rejected
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case self::STATUS_APPROVED:
                return 'kt-badge-success';
            case self::STATUS_REJECTED:
                return 'kt-badge-danger';
            case self::STATUS_PENDING:
            default:
                return 'kt-badge-warning';
        }
    }

    /**
     * Get status text
     */
    public function getStatusText()
    {
        switch ($this->status) {
            case self::STATUS_APPROVED:
                return 'Approved';
            case self::STATUS_REJECTED:
                return 'Rejected';
            case self::STATUS_PENDING:
            default:
                return 'Pending';
        }
    }
} 