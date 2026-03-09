<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'codigo_principal',
        'codigo_auxiliar',
        'descripcion',
        'cantidad',
        'unidad_medida',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_valor',
        'ice_porcentaje',
        'ice_valor',
        'iva_porcentaje',
        'iva_valor',
        'irbpnr_valor',
        'precio_total_sin_impuestos',
        'precio_total',
        'orden',
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
        'precio_unitario' => 'decimal:4',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_valor' => 'decimal:2',
        'ice_porcentaje' => 'decimal:2',
        'ice_valor' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_valor' => 'decimal:2',
        'irbpnr_valor' => 'decimal:2',
        'precio_total_sin_impuestos' => 'decimal:2',
        'precio_total' => 'decimal:2',
        'orden' => 'integer',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
