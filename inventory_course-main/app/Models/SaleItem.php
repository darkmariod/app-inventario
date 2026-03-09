<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'cantidad',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_valor',
        'iva_porcentaje',
        'iva_valor',
        'subtotal',
        'total',
        'producto_nombre',
        'producto_sku',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_valor' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_valor' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalConDescuento(): float
    {
        $subtotal = $this->cantidad * $this->precio_unitario;

        return $subtotal - $this->descuento_valor;
    }

    public function getIvaCalculado(): float
    {
        return $this->getSubtotalConDescuento() * ($this->iva_porcentaje / 100);
    }

    public function getTotalCalculado(): float
    {
        return $this->getSubtotalConDescuento() + $this->getIvaCalculado();
    }
}
