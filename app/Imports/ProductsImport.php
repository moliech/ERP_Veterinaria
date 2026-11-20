<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use SkipsFailures;

    public function __construct(
        protected ?InventoryService $inventory = null
    ) {
        $this->inventory = $inventory ?? app(InventoryService::class);
    }

    public function model(array $row)
    {
        $category = Category::firstOrCreate(
            ['name' => trim($row['categoria'])],
            ['description' => 'Categoría creada automáticamente desde importación Excel']
        );

        $initialStock = isset($row['stock_inicial']) ? (int) $row['stock_inicial'] : 0;

        $product = Product::create([
            'barcode' => ! empty($row['codigo_barras']) ? trim($row['codigo_barras']) : null,
            'name' => trim($row['nombre']),
            'category_id' => $category->id,
            'cost_price' => (float) $row['precio_costo'],
            'sale_price' => (float) $row['precio_venta'],
            'current_stock' => 0,
            'min_stock' => isset($row['stock_minimo']) ? (int) $row['stock_minimo'] : 5,
            'lot_number' => ! empty($row['lote']) ? trim($row['lote']) : null,
            'expiration_date' => ! empty($row['fecha_vencimiento']) ? $row['fecha_vencimiento'] : null,
            'is_active' => true,
        ]);

        if ($initialStock > 0 && $this->inventory) {
            $this->inventory->opening(
                product: $product,
                quantity: $initialStock,
                unitCost: (float) $row['precio_costo'],
                notes: 'Saldo inicial registrado mediante importación masiva de Excel'
            );
        }

        return $product;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'precio_costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_inicial' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'codigo_barras' => 'nullable|string|max:100|unique:products,barcode',
        ];
    }
}
