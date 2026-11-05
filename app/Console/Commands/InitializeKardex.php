<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Console\Command;

class InitializeKardex extends Command
{
    protected $signature = 'kardex:inicializar';
    protected $description = 'Registra el stock actual como inventario inicial para productos sin movimientos en el Kardex';

    public function handle(InventoryService $inventory): int
    {
        $count = 0;
        Product::withTrashed()->whereDoesntHave('movements')->where('current_stock', '>', 0)
            ->each(function (Product $product) use ($inventory, &$count) {
                if ($inventory->opening($product)) {
                    $count++;
                }
            });

        $this->info("Productos inicializados en el Kardex: {$count}");
        return self::SUCCESS;
    }
}
