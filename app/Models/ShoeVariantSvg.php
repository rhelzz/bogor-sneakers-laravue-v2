<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoeVariantSvg extends Model
{
    use HasFactory;

    protected $fillable = [
        'shoe_variant_id',
        'file_name',
        'file_path',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ShoeVariant::class, 'shoe_variant_id');
    }
}
