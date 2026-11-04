<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('VetPets Comercial');
            $table->string('business_subtitle')->default('PetShop & Suministros');
            $table->string('nit', 40)->default('901.452.890-1');
            $table->string('address')->nullable()->default('Calle 25 # 14-32, Cartago, Valle del Cauca');
            $table->string('phone', 80)->nullable()->default('(602) 211-4500 · 315 890 1234');
            $table->string('email', 120)->nullable()->default('facturacion@vetpets.co');
            $table->decimal('tax_rate', 5, 2)->default(19.00);
            $table->string('currency', 10)->default('COP');
            $table->string('invoice_prefix', 15)->default('FAC');
            $table->string('purchase_prefix', 15)->default('OC');
            $table->string('primary_color', 25)->default('emerald');
            $table->string('icon_class', 60)->default('fa-solid fa-paw');
            $table->text('invoice_footer')->nullable();
            $table->timestamps();
        });

        Schema::create('system_modules', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->string('icon', 60)->default('fa-solid fa-cube');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_core')->default(false);
            $table->json('depends_on')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->default(0)->after('name');
            }
            if (! Schema::hasColumn('products', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(19.00)->after('sale_price');
            }
            if (! Schema::hasColumn('products', 'batch_code')) {
                $table->string('batch_code', 50)->nullable()->after('minimum_stock');
            }
            if (! Schema::hasColumn('products', 'expiration_date')) {
                $table->date('expiration_date')->nullable()->after('batch_code');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $cols = array_filter(
                    ['cost_price', 'tax_rate', 'batch_code', 'expiration_date'],
                    fn ($col) => Schema::hasColumn('products', $col)
                );
                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
        Schema::dropIfExists('system_modules');
        Schema::dropIfExists('company_settings');
    }
};
