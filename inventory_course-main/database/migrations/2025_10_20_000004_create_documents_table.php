<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Tipo de documento SRI
            $table->enum('tipo_documento', [
                '01', // Factura
                '04', // Nota de Crédito
                '05', // Nota de Débito
                '06', // Guía de Remisión
                '07', // Retención
            ])->default('01');

            // Secuencial SRI (para generar clave de acceso)
            $table->string('establecimiento', 3)->default('001');
            $table->string('punto_emision', 3)->default('001');
            $table->string('secuencial', 9)->default('000000001'); // 9 dígitos
            $table->string('numero_documento', 20)->unique(); // 001-001-000000001

            // Clave de acceso SRI (49 dígitos)
            $table->string('clave_acceso', 49)->nullable()->unique();

            // Información SRI
            $table->string('numero_autorizacion', 100)->nullable();
            $table->datetime('fecha_autorizacion')->nullable();
            $table->enum('estado_sri', [
                'borrador',
                'pendiente',
                'enviado',
                'autorizado',
                'rechazado',
                'anulado',
            ])->default('borrador');

            // Fechas
            $table->date('fecha_emision');
            $table->date('fecha_caducidad')->nullable();

            // Totales documento
            $table->decimal('subtotal_0', 12, 2)->default(0);
            $table->decimal('subtotal_12', 12, 2)->default(0);
            $table->decimal('subtotal_14', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('iva_12', 12, 2)->default(0);
            $table->decimal('iva_14', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Archivos
            $table->text('xml_generado')->nullable();
            $table->text('xml_respuesta')->nullable();
            $table->string('pdf_path', 500)->nullable();

            // Errores SRI
            $table->json('errores')->nullable();

            // Referencia para notas crédito/débito
            $table->foreignId('documento_modificado_id')->nullable()->constrained('documents')->nullOnDelete();

            $table->timestamps();

            $table->index('clave_acceso');
            $table->index('numero_documento');
            $table->index('estado_sri');
            $table->index('fecha_emision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
