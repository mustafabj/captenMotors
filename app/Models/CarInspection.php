<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'hood',
        'front_right_fender',
        'front_left_fender',
        'rear_right_fender',
        'rear_left_fender',
        'trunk_door',
        'front_right_door',
        'rear_right_door',
        'front_left_door',
        'rear_left_door',
        'transmission',
        'motor',
        'body_notes'
    ];

    protected $casts = [
        'hood' => 'string',
        'front_right_fender' => 'string',
        'front_left_fender' => 'string',
        'rear_right_fender' => 'string',
        'rear_left_fender' => 'string',
        'trunk_door' => 'string',
        'front_right_door' => 'string',
        'rear_right_door' => 'string',
        'front_left_door' => 'string',
        'rear_left_door' => 'string',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Get all available inspection options
     */
    public static function getInspectionOptions(): array
    {
        return [
            'clean_and_free_of_filler' => 'Clean and free of filler',
            'painted' => 'Painted',
            'fully_repainted' => 'Fully repainted'
        ];
    }

    /**
     * Get the display name for an inspection value in Arabic
     */
    public static function getInspectionDisplayName(string $value): string
    {
        $arabicOptions = [
            'clean_and_free_of_filler' => 'سليم وخالي من المعجون',
            'painted' => 'مصبوغ',
            'fully_repainted' => 'مطلي بالكامل'
        ];
        
        return $arabicOptions[$value] ?? $value;
    }

    /**
     * Get all car parts that can be inspected
     */
    public static function getCarParts(): array
    {
        return [
            'hood' => 'Hood',
            'front_right_fender' => 'Front Right Fender',
            'front_left_fender' => 'Front Left Fender',
            'rear_right_fender' => 'Rear Right Fender',
            'rear_left_fender' => 'Rear Left Fender',
            'trunk_door' => 'Trunk Door',
            'front_right_door' => 'Front Right Door',
            'rear_right_door' => 'Rear Right Door',
            'front_left_door' => 'Front Left Door',
            'rear_left_door' => 'Rear Left Door'
        ];
    }
}
