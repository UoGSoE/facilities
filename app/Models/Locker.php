<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'room_id', 'people_id'];

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

    public function scopeRecentlyAllocated($query, int $numberOfDays = 28)
    {
        return $query->where('allocated_at', '>=', now()->subDays($numberOfDays));
    }

    public function scopeUnallocated($query)
    {
        return $query->whereNull('people_id');
    }

    public function scopeAllocated($query)
    {
        return $query->whereNotNull('people_id');
    }

    public function allocateTo(People $person)
    {
        $this->allocateToId($person->id);
    }

    public function allocateToId(int $personId)
    {
        $this->update(['people_id' => $personId]);
    }

    public function deallocate()
    {
        $this->update(['people_id' => null]);
    }

    public function isAllocated(): bool
    {
        return $this->people_id != null;
    }

    public function isUnallocated(): bool
    {
        return $this->people_id == null;
    }

    public function getPrettyName(): string
    {
        return "Locker {$this->name}";
    }
}
