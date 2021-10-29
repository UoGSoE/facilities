<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    use HasFactory;

    const TYPE_PGR = 'PGR';
    const TYPE_ACADEMIC = 'Academic';

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
    ];

    public function desks()
    {
        return $this->hasMany(Desk::class);
    }

    public function lockers()
    {
        return $this->hasMany(Locker::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(People::class);
    }

    public function scopeActive($query)
    {
        return $query->where('start_at', '<=', now())->where('end_at', '>=', now());
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->forenames} {$this->surname}";
    }

    public function isLeavingSoon(): bool
    {
        return (! $this->end_at->isPast()) && ($this->end_at->diffInDays(now()) <= 28);
    }

    public function hasLeft(): bool
    {
        return $this->end_at->isPast();
    }
}
