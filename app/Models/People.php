<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class People extends Model
{
    use HasFactory;

    const TYPE_PGR = 'PGR';
    const TYPE_ACADEMIC = 'Academic';
    const TYPE_PDRA = 'PDRA';
    const TYPE_MPATECH = 'MPA/Techs';

    protected $fillable = [
        'username',
        'email',
        'surname',
        'forenames',
        'start_at',
        'end_at',
        'supervisor_id',
        'type',
        'has_new_request',
    ];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'has_new_request' => 'boolean',
    ];

    public function desks()
    {
        return $this->hasMany(Desk::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function ivantiNotes()
    {
        return $this->notes()->ivanti()->latest();
    }

    public function getHtmlIdAttribute(): string
    {
        return Str::slug($this::class) . '-' . $this->id;
    }

    public function lockers()
    {
        return $this->hasMany(Locker::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(People::class);
    }

    public function itAssets()
    {
        return $this->hasMany(ItAsset::class);
    }

    public function supervisees()
    {
        return $this->hasMany(People::class, 'supervisor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('end_at', '>=', now());
    }

    public function scopePending($query)
    {
        return $query->where('start_at', '>=', now());
    }

    public function scopeNoFacilities($query)
    {
        return $query->whereDoesntHave('desks')->whereDoesntHave('lockers');
    }

    public function scopeHasOutstandingRequest($query)
    {
        return $query->where('has_new_request', '=', true);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->forenames} {$this->surname}";
    }

    public function getNameAndTypeAttribute(): string
    {
        return $this->full_name . '(' . $this->type . ')';
    }

    public function isLeavingSoon(): bool
    {
        if (! $this->end_at) {
            return false;
        }
        return (! $this->end_at->isPast()) && ($this->end_at->diffInDays(now()) <= 28);
    }

    public function hasLeft(): bool
    {
        if (! $this->end_at) {
            return false;
        }
        return $this->end_at->isPast();
    }

    public function getLatestIvantiNumber(): ?string
    {
        return $this->ivantiNotes->last()->ivanti_number ?? null;
    }

    public function flagNewRequest()
    {
        $this->has_new_request = true;
        $this->save();
    }

    public function flagRequestFilled()
    {
        $this->has_new_request = false;
        $this->save();
    }

    public function hasNewRequest(): bool
    {
        return (bool) $this->has_new_request;
    }
}
