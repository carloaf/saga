<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_date',
        'meal_type',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public const MEAL_TYPES = [
        'breakfast' => 'Café da Manhã',
        'lunch' => 'Almoço',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMealTypeLabel()
    {
        return self::MEAL_TYPES[$this->meal_type] ?? $this->meal_type;
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('booking_date', $date);
    }

    public function scopeForMeal($query, $mealType)
    {
        return $query->where('meal_type', $mealType);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('booking_date', [$startDate, $endDate]);
    }
}
