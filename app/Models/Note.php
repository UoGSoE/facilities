<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'user_id', 'noteable_id', 'noteable_type', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function noteable()
    {
        return $this->morphTo();
    }

    public function scopeIvanti($query)
    {
        return $query->where('body', 'like', 'IVANTI %');
    }

    public function getHtmlIdAttribute(): string
    {
        return Str::slug($this->noteable_type) . '-' . $this->noteable_id . '-note-' . $this->id;
    }

    public function getIvantiNumberAttribute(): string
    {
        if (preg_match('/IVANTI (\d+)/', $this->body) !== 1) {
            return '';
        }

        return preg_replace('/IVANTI (\d+) :.*/', '$1', $this->body);
    }
}
