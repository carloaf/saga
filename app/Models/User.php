<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'google_id',
        'full_name',
        'war_name',
        'email',
        'avatar_url',
        'rank_id',
        'organization_id',
        'gender',
        'ready_at_om_date',
        'is_active',
        'role', // Sistema simples de roles
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'ready_at_om_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isSuperuser()
    {
        return $this->role === 'superuser';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function getBookingsForDate($date)
    {
        return $this->bookings()->whereDate('booking_date', $date)->get();
    }

    public function hasBookingForDateAndMeal($date, $mealType)
    {
        return $this->bookings()
            ->whereDate('booking_date', $date)
            ->where('meal_type', $mealType)
            ->exists();
    }
}
