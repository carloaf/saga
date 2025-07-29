<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_host',
    ];

    protected $casts = [
        'is_host' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeHost($query)
    {
        return $query->where('is_host', true);
    }

    public function scopeGuest($query)
    {
        return $query->where('is_host', false);
    }
}
