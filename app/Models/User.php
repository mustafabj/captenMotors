<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all notifications for the user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get equipment cost notifications where user is notified
     */
    public function equipmentCostNotifications()
    {
        return $this->hasMany(EquipmentCostNotification::class, 'notified_user_id');
    }

    /**
     * Get equipment cost notifications where user requested
     */
    public function requestedEquipmentCostNotifications()
    {
        return $this->hasMany(EquipmentCostNotification::class, 'requested_by_user_id');
    }

    /**
     * Get insurance expiry notifications for the user
     */
    public function insuranceExpiryNotifications()
    {
        return $this->hasMany(InsuranceExpiryNotification::class);
    }

    /**
     * Get unread notifications for the user
     */
    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    /**
     * Get unread equipment cost notifications for the user
     */
    public function unreadEquipmentCostNotifications()
    {
        return $this->equipmentCostNotifications()->unread();
    }

    /**
     * Get unread insurance expiry notifications for the user
     */
    public function unreadInsuranceExpiryNotifications()
    {
        return $this->insuranceExpiryNotifications()->unread();
    }

    /**
     * Get read notifications for the user
     */
    public function readNotifications()
    {
        return $this->notifications()->read();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Get all admin users
     */
    public static function getAdmins()
    {
        return static::role('admin')->get();
    }
}
