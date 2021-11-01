<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItAsset extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'asset_number', 'people_id'];

    public function owner()
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function scopeUnallocated($query)
    {
        return $query->whereNull('people_id');
    }
}
