<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public function __construct(
        protected ?InventoryService $inventory = null
    ) {
        $this->inventory = $inventory ?? app(InventoryService::class);
    }

    public function model(array $row): Model|array|null
    {
        $category = Category::firstOrCreate(
            ['name' => trim((string) $row['categoria'])],
            ['description' => 'Categoría creada automáticamente desde importación masiva']
        );

        $initialStock = isset($row['stock_inicial']) && $row['stock_inicial'] !== '' ? (int) $row['stock_inicial'] : 0;

        $product = Product::create([
            'barcode' => ! empty($row['codigo_barras']) ? trim((string) $row['codigo_barras']) : null,
            'name' => trim((string) $row['nombre']),
            'category_id' => $category->id,
            'cost_price' => (float) $row['precio_costo'],
            'sale_price' => (float) $row['precio_venta'],
            'tax_rate' => 19.00,
            'current_stock' => 0,
            'minimum_stock' => isset($row['stock_minimo']) && $row['stock_minimo'] !== '' ? (int) $row['stock_minimo'] : 5,
            'batch_code' => ! empty($row['lote']) ? trim((string) $row['lote']) : null,
            'expiration_date' => ! empty($row['fecha_vencimiento']) ? $row['fecha_vencimiento'] : null,
            'active' => true,
        ]);

        if ($initialStock > 0 && $this->inventory) {
            $this->inventory->entry(
                product: $product,
                quantity: $initialStock,
                concept: 'inventario_inicial',
                reference: null,
                unitCost: (float) $row['precio_costo'],
                notes: 'Saldo inicial registrado mediante importación masiva de Excel'
            );
        }

        return $product;
    }

    public function prepareForValidation(array $data): array
    {
        foreach (['codigo_barras', 'nombre', 'categoria', 'lote'] as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $data[$field] = trim((string) $data[$field]);
            }
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'categoria' => 'required|string|max:100',
            'precio_costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_inicial' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'codigo_barras' => 'nullable|string|max:50|unique:products,barcode',
        ];
    }
}
