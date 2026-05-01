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
        'name',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(ShoeModel::class, 'shoe_model_id');
    }

    public function svgs(): HasMany
    {
        return $this->hasMany(ShoeVariantSvg::class);
    }
}
