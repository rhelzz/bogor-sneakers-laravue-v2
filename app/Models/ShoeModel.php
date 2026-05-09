<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoeModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(ShoeVariant::class);
    }

    public function types(): HasMany
    {
        return $this->hasMany(ShoeType::class);
    }

    public function catalogs(): HasMany
    {
        return $this->hasMany(Catalog::class);
    }
}
