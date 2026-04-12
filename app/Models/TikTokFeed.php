<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TikTokFeed extends Model
{
    /**
     * Explicit table name to avoid Laravel guessing `tik_tok_feeds`.
     *
     * @var string
     */
    protected $table = 'tiktok_feeds';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'category',
        'title',
        'author_name',
        'thumbnail_url',
        'video_id',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only active feed entries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }
}
