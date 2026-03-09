<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Cantidad y precios
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 12, 2); // Precio al momento de la venta
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_valor', 12, 2)->default(0);

            // IVA
            $table->decimal('iva_porcentaje', 5, 2)->default(12); // 0, 12, 14
            $table->decimal('iva_valor', 12, 2)->default(0);

            // Totales
            $table->decimal('subtotal', 12, 2); // (precio * cantidad) - descuento
            $table->decimal('total', 12, 2); // subtotal + iva

            // Información del producto al momento de venta (para auditoría)
            $table->string('producto_nombre')->nullable(); // nombre en ese momento
            $table->string('producto_sku')->nullable(); // sku en ese momento

            $table->timestamps();

            $table->index('sale_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
