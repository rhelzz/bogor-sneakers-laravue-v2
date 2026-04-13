<?php

namespace App\Models;

use App\Models\Catalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $catalog_id
 * @property string $image_path
 * @property int $position
 * @property-read string $image_url
 */
class CatalogImage extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'catalog_id',
        'image_path',
        'position',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'catalog_id' => 'integer',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/'.$this->image_path);
    }
}
