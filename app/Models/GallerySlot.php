<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function scopeOrdered($query)
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

        return asset('storage/' . $this->image_path);
    }
}
