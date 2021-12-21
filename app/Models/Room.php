<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'building_id'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function getHtmlIdAttribute(): string
    {
        return Str::slug($this::class) . '-' . $this->id;
    }

    public function desks()
    {
        return $this->hasMany(Desk::class);
    }

    public function lockers()
    {
        return $this->hasMany(Locker::class);
    }

    public function getUnallocatedDeskCountAttribute()
    {
        return $this->desks->filter(fn ($desk) => $desk->isUnallocated())->count();
    }

    public function getUnallocatedLockerCountAttribute()
    {
        return $this->lockers->filter(fn ($locker) => $locker->isUnallocated())->count();
    }
}
