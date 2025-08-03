<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
        'order',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getDisplayName()
    {
        return $this->abbreviation ?: $this->name;
    }
}
