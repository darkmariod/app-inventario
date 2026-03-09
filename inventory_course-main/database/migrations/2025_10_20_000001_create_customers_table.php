<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_identificacion', 2); // 04=RUC, 05=CEDULA, 06=PASAPORTE
            $table->string('identificacion', 20)->unique(); // 13 dígitos para RUC
            $table->string('razon_social', 300);
            $table->string('nombre_comercial', 300)->nullable();
            $table->string('direccion', 500)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('pais', 100)->default('Ecuador');
            $table->string('provincia', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->boolean('contribuyente_especial')->default(false);
            $table->string('obligado_contabilidad')->default('NO'); // SI/NO
            $table->string('codigo_establecimiento', 3)->default('001'); // para SRI
            $table->string('codigo_punto_emision', 3)->default('001'); // para SRI
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index('identificacion');
            $table->index('razon_social');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
