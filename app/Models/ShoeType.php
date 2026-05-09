<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'shoe_model_id',
        'name',
        'slug',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(ShoeModel::class, 'shoe_model_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ShoeVariant::class);
    }
}
