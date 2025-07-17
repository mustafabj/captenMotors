<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCapital extends Model
{
    protected $fillable = [
        'amount',
        'description',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Get the current total capital
    public static function currentTotal()
    {
        return static::sum('amount');
    }
}
