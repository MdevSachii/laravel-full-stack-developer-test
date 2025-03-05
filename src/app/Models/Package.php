<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'available_seats'];
    public function users()
    {
        return $this->belongsToMany(User::class, 'bookings')->withTimestamps();
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_seats', '>', 0);
    }
}
