<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Exports\ProductsTemplateExport;
use App\Exports\SalesExport;
use App\Http\Requests\ImportProductsRequest;
use App\Imports\ProductsImport;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function exportProducts()
    {
        return Excel::download(new ProductsExport, 'productos_vetpets_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new ProductsTemplateExport, 'plantilla_importacion_productos.xlsx');
    }

    public function importProductsForm()
    {
        return view('products.import');
    }

    public function importProducts(ImportProductsRequest $request, InventoryService $inventory)
    {
        $import = new ProductsImport($inventory);
        $import->import($request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            return back()->with('failures', $import->failures());
        }

        return redirect()->route('products.index')
            ->with('status', 'Catálogo de productos importado exitosamente con asentamiento en Kardex.');
    }

    public function exportSales()
    {
        return Excel::download(new SalesExport, 'ventas_vetpets_' . now()->format('Y-m-d') . '.xlsx');
    }
}