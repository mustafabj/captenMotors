<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarEquipmentCostDescriptionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_equipment_cost_id',
        'old_description',
        'new_description',
        'changed_by_user_id',
        'change_reason'
    ];

    public function carEquipmentCost()
    {
        return $this->belongsTo(CarEquipmentCost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
