<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'chassis_inspection',
        'transmission',
        'motor',
        'body_notes',
        'hood',
        'front_right_fender',
        'front_left_fender',
        'rear_right_fender',
        'rear_left_fender',
        'trunk_door',
        'front_right_door',
        'rear_right_door',
        'front_left_door',
        'rear_left_door'
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
        'rear_left_door' => 'string'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public static function getInspectionOptions(): array
    {
        return [
            'clean_and_free_of_filler' => 'Clean and free of filler',
            'painted' => 'Painted',
            'fully_repainted' => 'Fully repainted'
        ];
    }

    public static function getInspectionDisplayName(string $value): string
    {
        $arabicOptions = [
            'clean_and_free_of_filler' => 'سليم وخالي من المعجون',
            'painted' => 'مصبوغ',
            'fully_repainted' => 'مصبوغ بالكامل'
        ];
        return $arabicOptions[$value] ?? $value;
    }

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

    public static function getCarPartsArabic(): array
    {
        return [
            'hood' => 'كبوت',
            'front_right_fender' => 'مدكر امامي يمين',
            'front_left_fender' => 'مدكر امامي يسار',
            'rear_right_fender' => 'مدكر خلفي يمين',
            'rear_left_fender' => 'مدكر خلفي يسار',
            'trunk_door' => 'باب الدبة',
            'front_right_door' => 'باب امامي يمين',
            'rear_right_door' => 'باب خلفي يمين',
            'front_left_door' => 'باب امامي يسار',
            'rear_left_door' => 'باب خلفي يسار'
        ];
    }

    /**
     * Get the Arabic translation for inspection status
     */
    public function getInspectionStatusArabic(string $status): string
    {
        return self::getInspectionDisplayName($status);
    }

    /**
     * Get all body part inspections as an array
     */
    public function getBodyInspections(): array
    {
        return [
            'hood' => $this->hood,
            'front_right_fender' => $this->front_right_fender,
            'front_left_fender' => $this->front_left_fender,
            'rear_right_fender' => $this->rear_right_fender,
            'rear_left_fender' => $this->rear_left_fender,
            'trunk_door' => $this->trunk_door,
            'front_right_door' => $this->front_right_door,
            'rear_right_door' => $this->rear_right_door,
            'front_left_door' => $this->front_left_door,
            'rear_left_door' => $this->rear_left_door
        ];
    }
}
