<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'user_id', 'noteable_id', 'noteable_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function noteable()
    {
        return $this->morphTo();
    }

    public function getHtmlIdAttribute(): string
    {
        return Str::slug($this->noteable_type) . '-' . $this->noteable_id . '-note-' . $this->id;
    }
}
