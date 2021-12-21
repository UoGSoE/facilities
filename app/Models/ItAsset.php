<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItAsset extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'asset_number', 'people_id'];

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

    public function scopeUnallocated($query)
    {
        return $query->whereNull('people_id');
    }
}
