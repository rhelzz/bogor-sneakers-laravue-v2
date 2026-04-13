<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $catalog_id
 * @property int $size_eu
 */
class CatalogSize extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'catalog_id',
        'size_eu',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'catalog_id' => 'integer',
        'size_eu' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }
}
