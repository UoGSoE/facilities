<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'room_id', 'people_id'];

    public function owner()
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeUnallocated($query)
    {
        return $query->whereNull('people_id');
    }

    public function scopeAllocated($query)
    {
        return $query->whereNotNull('people_id');
    }

    public function isAllocated(): bool
    {
        return $this->people_id != null;
    }

    public function isUnallocated(): bool
    {
        return $this->people_id == null;
    }
}
