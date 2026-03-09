<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta', 20)->unique(); // Código interno de venta
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Totales
            $table->decimal('subtotal_0', 12, 2)->default(0); // Base iva 0%
            $table->decimal('subtotal_12', 12, 2)->default(0); // Base iva 12%
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('iva_12', 12, 2)->default(0); // 12% del subtotal_12
            $table->decimal('total', 12, 2)->default(0);

            // Estado de la venta
            $table->enum('estado', ['borrador', 'confirmada', 'cancelada', 'anulada'])->default('borrador');

            // Información adicional
            $table->date('fecha_venta');
            $table->date('fecha_confirmacion')->nullable();
            $table->text('notas')->nullable();
            $table->string('observaciones')->nullable();

            // Para auditoría
            $table->timestamps();

            $table->index('numero_venta');
            $table->index('estado');
            $table->index('fecha_venta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
