<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sri_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();

            // Tipo de comunicación
            $table->enum('tipo', [
                'envio',           // Envío inicial al SRI
                'autorizacion',     // Consulta de autorización
                'consulta_estado', // Estado del documento
                'notificacion',    // Respuesta del SRI
            ]);

            // Estado de la comunicación
            $table->enum('estado', [
                'pendiente',
                'exitoso',
                'fallido',
                'error_sri',
            ])->default('pendiente');

            // Detalles de la comunicación
            $table->text('request_xml')->nullable(); // XML enviado
            $table->text('response_xml')->nullable(); // Respuesta del SRI
            $table->string('codigo_error', 20)->nullable(); // Código de error SRI
            $table->text('mensaje_error')->nullable(); // Mensaje de error

            // Tiempos
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->integer('tiempo_respuesta_ms')->nullable(); // Latencia

            // Trail
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index('document_id');
            $table->index('tipo');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sri_documents');
    }
};
