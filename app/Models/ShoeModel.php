<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ShoeModel extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'layer_labels',
        'is_active',
    ];

    protected $casts = [
        'layer_labels' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ShoeModel $model): void {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ShoeVariant::class, 'shoe_model_id')->orderBy('sort_order');
    }

    public function catalogs(): HasMany
    {
        return $this->hasMany(Catalog::class);
    }
}
