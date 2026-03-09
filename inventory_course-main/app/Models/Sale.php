<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_venta',
        'customer_id',
        'user_id',
        'subtotal_0',
        'subtotal_12',
        'descuento',
        'iva_12',
        'total',
        'estado',
        'fecha_venta',
        'fecha_confirmacion',
        'notas',
        'observaciones',
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'fecha_confirmacion' => 'date',
        'subtotal_0' => 'decimal:2',
        'subtotal_12' => 'decimal:2',
        'descuento' => 'decimal:2',
        'iva_12' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const ESTADO_BORRADOR = 'borrador';

    public const ESTADO_CONFIRMADA = 'confirmada';

    public const ESTADO_CANCELADA = 'cancelada';

    public const ESTADO_ANULADA = 'anulada';

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function document(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isConfirmable(): bool
    {
        return $this->estado === self::ESTADO_BORRADOR;
    }

    public function isCancellable(): bool
    {
        return $this->estado === self::ESTADO_CONFIRMADA;
    }

    public function confirm(): bool
    {
        if (! $this->isConfirmable()) {
            return false;
        }

        $this->update([
            'estado' => self::ESTADO_CONFIRMADA,
            'fecha_confirmacion' => now()->toDateString(),
        ]);

        event(new SaleConfirmed($this));

        return true;
    }

    public function cancel(): bool
    {
        if (! $this->isCancellable()) {
            return false;
        }

        $this->update([
            'estado' => self::ESTADO_CANCELADA,
        ]);

        return true;
    }
}
