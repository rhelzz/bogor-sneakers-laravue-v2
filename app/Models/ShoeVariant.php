<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoeVariant extends Model
{
    protected $fillable = [
        'shoe_model_id',
        'name',
        'full_preview_path',
        'svg_data',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'svg_data' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function shoeModel(): BelongsTo
    {
        return $this->belongsTo(ShoeModel::class);
    }
}
