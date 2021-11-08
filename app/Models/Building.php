<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Building extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function getUnallocatedDesks(): Collection
    {
        return $this->rooms->flatMap(fn ($room) => $room->desks()->unallocated()->get());
    }

    public function getUnallocatedLockers(): Collection
    {
        return $this->rooms->flatMap(fn ($room) => $room->lockers()->unallocated()->get());
    }

    public function getUnallocatedLockerCountAttribute()
    {
        return $this->rooms->map(fn ($room) => $room->lockers->filter(fn ($locker) => $locker->isUnallocated())->count())->sum();
    }

    public function getUnallocatedDeskCountAttribute()
    {
        return $this->rooms->map(fn ($room) => $room->desks->filter(fn ($locker) => $locker->isUnallocated())->count())->sum();
    }
}
