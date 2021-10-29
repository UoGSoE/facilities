<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function desks()
    {
        return $this->hasMany(Desk::class);
    }

    public function lockers()
    {
        return $this->hasMany(Locker::class);
    }
}
