<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->string('email', 100)->nullable()->after('phone');
            $table->string('address', 200)->nullable()->after('email');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['email', 'address']);
        });
    }
};
