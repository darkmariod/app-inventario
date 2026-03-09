<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'customer_id',
        'user_id',
        'tipo_documento',
        'establecimiento',
        'punto_emision',
        'secuencial',
        'numero_documento',
        'clave_acceso',
        'numero_autorizacion',
        'fecha_autorizacion',
        'estado_sri',
        'fecha_emision',
        'fecha_caducidad',
        'subtotal_0',
        'subtotal_12',
        'subtotal_14',
        'descuento',
        'iva_12',
        'iva_14',
        'total',
        'xml_generado',
        'xml_respuesta',
        'pdf_path',
        'errores',
        'documento_modificado_id',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_caducidad' => 'date',
        'fecha_autorizacion' => 'datetime',
        'subtotal_0' => 'decimal:2',
        'subtotal_12' => 'decimal:2',
        'subtotal_14' => 'decimal:2',
        'descuento' => 'decimal:2',
        'iva_12' => 'decimal:2',
        'iva_14' => 'decimal:2',
        'total' => 'decimal:2',
        'errores' => 'array',
    ];

    public const TIPO_FACTURA = '01';

    public const TIPO_NOTA_CREDITO = '04';

    public const TIPO_NOTA_DEBITO = '05';

    public const TIPO_GUIA_REMISION = '06';

    public const TIPO_RETENCION = '07';

    public const ESTADO_BORRADOR = 'borrador';

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_ENVIADO = 'enviado';

    public const ESTADO_AUTORIZADO = 'autorizado';

    public const ESTADO_RECHAZADO = 'rechazado';

    public const ESTADO_ANULADO = 'anulado';

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

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
        return $this->hasMany(DocumentItem::class)->orderBy('orden');
    }

    public function sriCommunications(): HasMany
    {
        return $this->hasMany(SriDocument::class, 'document_id')->orderBy('created_at', 'desc');
    }

    public function documentoModificado(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'documento_modificado_id');
    }

    public function getTipoDocumentoLabel(): string
    {
        return match ($this->tipo_documento) {
            self::TIPO_FACTURA => 'Factura',
            self::TIPO_NOTA_CREDITO => 'Nota de Crédito',
            self::TIPO_NOTA_DEBITO => 'Nota de Débito',
            self::TIPO_GUIA_REMISION => 'Guía de Remisión',
            self::TIPO_RETENCION => 'Comprobante de Retención',
            default => 'Desconocido',
        };
    }

    public function getNumeroDocumentoFormateado(): string
    {
        return sprintf(
            '%s-%s-%s',
            $this->establecimiento,
            $this->punto_emision,
            str_pad($this->secuencial, 9, '0', STR_PAD_LEFT)
        );
    }

    public function isAutorizado(): bool
    {
        return $this->estado_sri === self::ESTADO_AUTORIZADO;
    }

    public function isEnviado(): bool
    {
        return in_array($this->estado_sri, [self::ESTADO_ENVIADO, self::ESTADO_PENDIENTE]);
    }
}
