<?php

namespace App\Models;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'building_id', 'image_path'];

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

    public function storeImage($newImage): string
    {
        $image = Image::make($newImage);
        $image->resize(1280, 1280, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $path = 'room-images/' . $this->id . '.jpg';

        Storage::put($path, (string) $image->encode('jpg'));

        $this->update(['image_path' => $path]);

        return $path;
    }
}
