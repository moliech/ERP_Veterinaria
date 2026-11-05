<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->enum('type', ['entrada', 'salida']);
            $table->string('concept', 30); // inventario_inicial, compra, venta, anulacion_venta, ajuste
            $table->unsignedInteger('quantity');
            $table->integer('balance'); // Saldo físico acumulado tras la operación
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->nullableMorphs('reference'); // Sale, Purchase o null en ajuste manual
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
