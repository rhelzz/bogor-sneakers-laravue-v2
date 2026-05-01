<?php

namespace App\Models;

use App\Models\CatalogImage;
use App\Models\CatalogSize;
use App\Models\HomePreorder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $public_id
 * @property string $slug
 * @property string $name
 * @property string $collection
 * @property string|null $card_image_path
 * @property string|null $description
 * @property int $price
 * @property 'ready'|'po'|'habis' $status
 * @property int|null $preorder_min_days
 * @property int|null $preorder_max_days
 * @property string|null $discount_type
 * @property int|null $discount_value
 * @property string|null $discount_starts_at
 * @property string|null $discount_ends_at
 * @property int $popularity_score
 * @property bool $is_active
 * @property int $sort_order
 * @property-read string $route_key
 * @property-read string|null $card_image_url
 * @property-read string|null $primary_image_url
 * @property-read HomePreorder|null $homePreorder
 */
class Catalog extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'public_id',
        'slug',
        'name',
        'collection',
        'card_image_path',
        'description',
        'price',
        'status',
        'preorder_min_days',
        'preorder_max_days',
        'discount_type',
        'discount_value',
        'discount_starts_at',
        'discount_ends_at',
        'popularity_score',
        'is_active',
        'sort_order',
        'shoe_model_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'integer',
        'preorder_min_days' => 'integer',
        'preorder_max_days' => 'integer',
        'discount_value' => 'integer',
        'discount_starts_at' => 'datetime',
        'discount_ends_at' => 'datetime',
        'popularity_score' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'shoe_model_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ensure public id and slug always exist.
     */
    protected static function booted(): void
    {
        static::creating(function (Catalog $catalog): void {
            if (! is_string($catalog->public_id) || trim($catalog->public_id) === '') {
                $catalog->public_id = strtolower((string) Str::ulid());
            }

            if (! is_string($catalog->slug) || trim($catalog->slug) === '') {
                $catalog->slug = static::makeSlug((string) $catalog->name);
            }
        });

        static::saving(function (Catalog $catalog): void {
            $catalog->slug = static::makeSlug((string) $catalog->name);
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(CatalogImage::class)->orderBy('position', 'asc');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(CatalogSize::class)->orderBy('size_eu', 'asc');
    }

    public function homePreorder(): HasOne
    {
        return $this->hasOne(HomePreorder::class);
    }

    public function shoeModel(): BelongsTo
    {
        return $this->belongsTo(ShoeModel::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderByDesc('created_at');
    }

    public function getRouteKeyAttribute(): string
    {
        return sprintf('%s-%s', $this->slug, $this->public_id);
    }

    public function getCardImageUrlAttribute(): ?string
    {
        $path = trim((string) ($this->card_image_path ?? ''));

        if ($path === '') {
            return null;
        }

        return asset('storage/'.$path);
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        if ($this->card_image_url !== null) {
            return $this->card_image_url;
        }

        $first = $this->images->first();

        return $first instanceof CatalogImage ? $first->image_url : null;
    }

    private static function makeSlug(string $name): string
    {
        $slug = Str::slug($name);

        if ($slug === '') {
            return 'produk';
        }

        return $slug;
    }
}
