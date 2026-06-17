<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramFeed extends Model
{
    use HasFactory;

    protected $table = 'instagram_feeds';

    protected $fillable = [
        'media_id',
        'media_url',
        'permalink',
        'caption',
        'like_count',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'like_count' => 'integer',
    ];

    public function scopeRecent($query, $limit = 12)
    {
        return $query->orderBy('timestamp', 'desc')->limit($limit);
    }

    public function getShortCaptionAttribute()
    {
        if (!$this->caption) return null;
        return strlen($this->caption) > 100 ? substr($this->caption, 0, 100) . '...' : $this->caption;
    }
}
