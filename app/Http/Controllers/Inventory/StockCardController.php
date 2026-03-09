<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockCardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stocks = WarehouseStock::with(['warehouse', 'product.unit'])->select(['warehouse_stocks.*']);
            return DataTables::of($stocks)
                ->addColumn('warehouse_name', function($row){ return $row->warehouse->name ?? '-'; })
                ->addColumn('product_name', function($row){ return $row->product->name ?? '-'; })
                ->addColumn('unit_name', function($row){ return $row->product->unit->name ?? '-'; })
                ->editColumn('quantity', function($row){ return number_format($row->quantity, 2); })
                ->addColumn('action', function ($row) {
                    return '<a href="'.route('stock-cards.show', ['product_id' => $row->product_id, 'warehouse_id' => $row->warehouse_id]).'" class="btn btn-sm btn-info" data-toggle="tooltip" title="View Stock Card"><i class="fas fa-history"></i> Log</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('inventory.stock_cards.index', compact('warehouses'));
    }

    public function show($product_id, $warehouse_id, Request $request)
    {
        $product = Product::with('unit')->findOrFail($product_id);
        $warehouse = Warehouse::findOrFail($warehouse_id);
        $currentStock = WarehouseStock::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->first();

        if ($request->ajax()) {
            $transactions = InventoryTransaction::with('reference')
                ->where('product_id', $product_id)
                ->where('warehouse_id', $warehouse_id)
                ->orderBy('transaction_date', 'asc')
                ->orderBy('created_at', 'asc');

            // Running balance calculation in code if needed, but for datatables we can just show the list
            // For a true running balance in datatables we'd need to fetch all and map, or use DB variables.
            // Simplified here: just show the tx.

            return DataTables::of($transactions)
                ->editColumn('transaction_date', function($row){ return $row->transaction_date->format('d/m/Y H:i'); })
                ->editColumn('quantity', function($row){ 
                    $color = $row->quantity > 0 ? 'success' : ($row->quantity < 0 ? 'danger' : 'secondary');
                    $prefix = $row->quantity > 0 ? '+' : '';
                    return '<span class="text-'.$color.'"><strong>'.$prefix . number_format($row->quantity, 2).'</strong></span>';
                })
                ->addColumn('reference_doc', function($row){
                    return class_basename($row->reference_type) . ' #' . ($row->reference->id ?? 'Unknown');
                })
                ->rawColumns(['quantity'])
                ->make(true);
        }

        return view('inventory.stock_cards.show', compact('product', 'warehouse', 'currentStock'));
    }
}
