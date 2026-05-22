<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoeVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'shoe_model_id',
        'shoe_type_id',
        'name',
        'studio_config',
    ];

    protected $casts = [
        'studio_config' => 'array',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(ShoeModel::class, 'shoe_model_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ShoeType::class, 'shoe_type_id');
    }

    public function svgs(): HasMany
    {
        return $this->hasMany(ShoeVariantSvg::class);
    }
}
