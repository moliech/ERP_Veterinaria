<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('owner_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->string('phone_number', 20);
            $table->string('phone_type', 20)->default('Móvil');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_phones');
    }
};
