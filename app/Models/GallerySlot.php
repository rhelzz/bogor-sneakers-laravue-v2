<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $slot
 * @property string|null $image_path
 * @property Carbon|null $updated_at
 * @property-read string|null $image_url
 */
class GallerySlot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'slot',
        'image_path',
    ];

    /**
     * Scope a query to order gallery slots by slot number.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('slot', 'asc');
    }

    /**
     * Get the public image URL for the slot.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return asset('storage/'.$this->image_path);
    }
}
