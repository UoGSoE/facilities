<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Desk extends Model
{
    use GenericAllocationLogic;
    use HasFactory;

    protected $fillable = ['name', 'room_id', 'people_id', 'avanti_ticket_id'];

    protected $casts = [
        'allocated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function ($desk) {
            if ($desk->isDirty('people_id')) {
                if ($desk->people_id) {
                    $desk->allocated_at = now();
                } else {
                    $desk->allocated_at = null;
                }
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function getHtmlIdAttribute(): string
    {
        return Str::slug($this::class) . '-' . $this->id;
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getPrettyName(): string
    {
        return "Desk {$this->name}";
    }
}
