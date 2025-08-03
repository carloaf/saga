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
        'password',
        'avatar_url',
        'rank_id',
        'organization_id',
        'subunit',
        'armed_force',
        'gender',
        'ready_at_om_date',
        'is_active',
        'role', // Sistema simples de roles
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'ready_at_om_date' => 'date',
        'is_active' => 'boolean',
        'password' => 'hashed',
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

    public function getArmedForceFullName()
    {
        $forces = [
            'FAB' => 'Força Aérea Brasileira',
            'MB' => 'Marinha do Brasil',
            'EB' => 'Exército Brasileiro'
        ];
        
        return $forces[$this->armed_force] ?? null;
    }

    public function getArmedForceColor()
    {
        $colors = [
            'FAB' => 'text-blue-600',
            'MB' => 'text-indigo-600',
            'EB' => 'text-green-600'
        ];
        
        return $colors[$this->armed_force] ?? 'text-gray-600';
    }
}
