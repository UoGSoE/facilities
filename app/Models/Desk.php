<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    use HasFactory;

    public function owner()
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function scopeUnallocated($query)
    {
        return $query->whereNull('user_id');
    }
}
