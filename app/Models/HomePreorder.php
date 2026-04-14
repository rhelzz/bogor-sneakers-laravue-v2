<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $catalog_id
 * @property 'buka'|'hampir_habis'|'habis' $status
 * @property bool $is_visible
 * @property int $max_slots
 * @property int $filled_slots
 * @property \Illuminate\Support\Carbon|null $countdown_ends_at
 * @property string|null $batch_label
 * @property int $urutan
 */
class HomePreorder extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'catalog_id',
        'status',
        'is_visible',
        'max_slots',
        'filled_slots',
        'countdown_ends_at',
        'batch_label',
        'urutan',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_visible' => 'boolean',
        'max_slots' => 'integer',
        'filled_slots' => 'integer',
        'countdown_ends_at' => 'datetime',
        'urutan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function scopeVisibleForHome(Builder $query): Builder
    {
        return $query
            ->where('is_visible', true)
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('countdown_ends_at')
                    ->orWhere('countdown_ends_at', '>', now());
            });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('urutan', 'asc')->orderBy('id', 'asc');
    }

    public static function expireVisibleItems(): int
    {
        return static::query()
            ->where('is_visible', true)
            ->whereNotNull('countdown_ends_at')
            ->where('countdown_ends_at', '<=', now())
            ->update([
                'is_visible' => false,
                'updated_at' => now(),
            ]);
    }
}
