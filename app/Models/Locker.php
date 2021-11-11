<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
    use GenericAllocationLogic;
    use HasFactory;

    protected $fillable = ['name', 'room_id', 'people_id', 'avanti_ticket_id'];

    protected $casts = [
        'allocated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function ($locker) {
            if ($locker->isDirty('people_id')) {
                $locker->allocated_at = now();
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getPrettyName(): string
    {
        return "Locker {$this->name}";
    }
}
