<?php

namespace App\Models;

use Database\Factories\WhatsappAdminFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappAdmin extends Model
{
    /** @use HasFactory<WhatsappAdminFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    public const COLOR_CLASS_MAP = [
        'matcha' => 'bg-matcha/20 text-matcha',
        'indigo' => 'bg-indigo/20 text-indigo',
        'sakura' => 'bg-sakura/30 text-sakura',
        'amber' => 'bg-amber-100 text-amber-700',
        'sky' => 'bg-sky-100 text-sky-700',
        'slate' => 'bg-slate-200 text-slate-700',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role',
        'phone',
        'initial',
        'color',
        'is_active',
        'sort_order',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * @return list<string>
     */
    public static function availableColorKeys(): array
    {
        return array_keys(self::COLOR_CLASS_MAP);
    }

    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    public function resolveColorClass(): string
    {
        $key = (string) ($this->color ?? 'matcha');

        return self::COLOR_CLASS_MAP[$key] ?? self::COLOR_CLASS_MAP['matcha'];
    }

    /**
     * @return array{id: int, name: string, role: string, phone: string, initial: string, color: string}
     */
    public function toFloatingContact(): array
    {
        $initial = trim((string) ($this->initial ?? ''));

        if ($initial === '') {
            $name = trim((string) $this->name);
            $initial = $name === '' ? 'A' : mb_substr($name, 0, 1);
        }

        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'role' => (string) $this->role,
            'phone' => (string) $this->phone,
            'initial' => mb_strtoupper($initial),
            'color' => $this->resolveColorClass(),
        ];
    }
}
