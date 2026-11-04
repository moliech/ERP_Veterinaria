<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number', 30)->unique();
            $table->foreignId('provider_id')->constrained('providers')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->date('purchase_date');
            $table->enum('status', ['pendiente', 'parcial', 'recibida', 'anulada'])->default('pendiente');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2);
            $table->decimal('total', 12, 2);
            $table->dateTime('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
