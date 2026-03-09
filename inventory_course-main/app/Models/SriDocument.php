<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SriDocument extends Model
{
    use HasFactory;

    protected $table = 'sri_documents';

    protected $fillable = [
        'document_id',
        'tipo',
        'estado',
        'request_xml',
        'response_xml',
        'codigo_error',
        'mensaje_error',
        'fecha_envio',
        'fecha_respuesta',
        'tiempo_respuesta_ms',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_respuesta' => 'datetime',
        'tiempo_respuesta_ms' => 'integer',
    ];

    public const TIPO_ENVIO = 'envio';

    public const TIPO_AUTORIZACION = 'autorizacion';

    public const TIPO_CONSULTA_ESTADO = 'consulta_estado';

    public const TIPO_NOTIFICACION = 'notificacion';

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_EXITOSO = 'exitoso';

    public const ESTADO_FALLIDO = 'fallido';

    public const ESTADO_ERROR_SRI = 'error_sri';

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTipoLabel(): string
    {
        return match ($this->tipo) {
            self::TIPO_ENVIO => 'Envío',
            self::TIPO_AUTORIZACION => 'Autorización',
            self::TIPO_CONSULTA_ESTADO => 'Consulta Estado',
            self::TIPO_NOTIFICACION => 'Notificación',
            default => 'Desconocido',
        };
    }

    public function getEstadoLabel(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_EXITOSO => 'Exitoso',
            self::ESTADO_FALLIDO => 'Fallido',
            self::ESTADO_ERROR_SRI => 'Error SRI',
            default => 'Desconocido',
        };
    }

    public function isExitoso(): bool
    {
        return $this->estado === self::ESTADO_EXITOSO;
    }
}
