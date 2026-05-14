<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'status',
        'ip_address',
        'user_agent',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'shipping_cost' => 'integer',
        'total_amount' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_UNVERIFIED = 'unverified';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_UNVERIFIED => 'Menunggu Verifikasi',
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_PROCESSING => 'Sedang Diproses',
            self::STATUS_SHIPPED => 'Dalam Pengiriman',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusLabels()[$this->status] ?? $this->status;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'BGS-' . date('Ym');
        $count = self::where('order_number', 'like', $prefix . '%')->count();
        $nextNumber = str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
        
        $number = $prefix . '-' . $nextNumber;
        
        // Ensure uniqueness even if concurrent
        while (self::where('order_number', $number)->exists()) {
            $count++;
            $nextNumber = str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
            $number = $prefix . '-' . $nextNumber;
        }

        return $number;
    }
}
