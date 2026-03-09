<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Referencia opcional a Sale (para auditoría cuando el movimiento viene de una venta)
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();

            // Referencia opcional a Document (si está relacionado con documento electrónico)
            $table->foreignId('document_id')->nullable()->constrained()->nullOnDelete();

            // Agregar índice para búsquedas
            $table->index('sale_id');
            $table->index('document_id');
        });

        // MySQL no permite modificar enum directamente, usamos raw SQL
        DB::statement("ALTER TABLE stock_movements MODIFY type ENUM('in', 'out', 'adjust', 'sale', 'return', 'transfer') NOT NULL DEFAULT 'in'");
    }

    public function down(): void
    {
        // Revertir el enum primero
        DB::statement("ALTER TABLE stock_movements MODIFY type ENUM('in', 'out', 'adjust') NOT NULL DEFAULT 'in'");

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropForeign(['document_id']);
            $table->dropIndex(['sale_id']);
            $table->dropIndex(['document_id']);
            $table->dropColumn(['sale_id', 'document_id']);
        });
    }
};
