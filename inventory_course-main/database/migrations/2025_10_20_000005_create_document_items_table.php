<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();

            // Código SRI del producto (para facturación)
            $table->string('codigo_principal', 50)->nullable();
            $table->string('codigo_auxiliar', 50)->nullable();

            // Descripción según SRI
            $table->string('descripcion', 300);

            // Cantidad
            $table->decimal('cantidad', 12, 4)->default(1);
            $table->string('unidad_medida', 20)->default('UNIDAD');

            // Precios
            $table->decimal('precio_unitario', 12, 4);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_valor', 12, 2)->default(0);

            // ICE (Impuesto a los Consumos Especiales)
            $table->decimal('ice_porcentaje', 5, 2)->default(0);
            $table->decimal('ice_valor', 12, 2)->default(0);

            // IVA
            $table->decimal('iva_porcentaje', 5, 2)->default(12);
            $table->decimal('iva_valor', 12, 2)->default(0);

            // IRBPNR (Impuesto Redimible Botellas Plásticas)
            $table->decimal('irbpnr_valor', 12, 2)->default(0);

            // Totales
            $table->decimal('precio_total_sin_impuestos', 12, 2);
            $table->decimal('precio_total', 12, 2);

            // metadata
            $table->integer('orden')->default(0); // порядок

            $table->timestamps();

            $table->index('document_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_items');
    }
};
