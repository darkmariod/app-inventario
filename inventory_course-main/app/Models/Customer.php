<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_identificacion',
        'identificacion',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'telefono',
        'email',
        'pais',
        'provincia',
        'ciudad',
        'contribuyente_especial',
        'obligado_contabilidad',
        'codigo_establecimiento',
        'codigo_punto_emision',
        'user_id',
    ];

    protected $casts = [
        'contribuyente_especial' => 'boolean',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTipoIdentificacionLabel(): string
    {
        return match ($this->tipo_identificacion) {
            '04' => 'RUC',
            '05' => 'Cédula',
            '06' => 'Pasaporte',
            '07' => 'Consumidor Final',
            default => 'Otro',
        };
    }

    public function getIdentificacionCompleta(): string
    {
        return "{$this->identificacion} ({$this->getTipoIdentificacionLabel()})";
    }
}
