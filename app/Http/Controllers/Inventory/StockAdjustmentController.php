<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreStockAdjustmentRequest;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentLine;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\DocumentNumberService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockAdjustmentController extends Controller
{
    protected $docService;
    protected $invService;

    public function __construct(DocumentNumberService $docService, InventoryService $invService)
    {
        $this->docService = $docService;
        $this->invService = $invService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $adjustments = StockAdjustment::with(['warehouse'])->select(['stock_adjustments.*']);
            return DataTables::of($adjustments)
                ->editColumn('adjustment_date', function($row){ return $row->adjustment_date->format('d/m/Y'); })
                ->addColumn('warehouse_name', function($row){ return $row->warehouse->name ?? '-'; })
                ->editColumn('status', function($row){
                    $class = match($row->status) {
                        'Draft' => 'secondary',
                        'Approved' => 'success',
                        'Cancelled' => 'danger',
                        default => 'light'
                    };
                    return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('stock-adjustments.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    if ($row->status === 'Draft') {
                        $btn .= ' <form action="'.route('stock-adjustments.update', $row->id).'" method="POST" class="d-inline">
                                    '.csrf_field().'
                                    '.method_field('PUT').'
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm(\'Approve this adjustment?\')"><i class="fas fa-check"></i></button>
                                  </form>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('inventory.stock_adjustments.index');
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $nextNumber = $this->docService->generate('ADJ', auth()->user()->company_id, 'ADJ');
        return view('inventory.stock_adjustments.create', compact('warehouses', 'nextNumber'));
    }

    public function store(StoreStockAdjustmentRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $adjNumber = $this->docService->generate('ADJ', auth()->user()->company_id, 'ADJ');
            
            $adjustment = StockAdjustment::create([
                'company_id' => auth()->user()->company_id,
                'warehouse_id' => $request->warehouse_id,
                'adjustment_number' => $adjNumber,
                'adjustment_date' => $request->adjustment_date,
                'status' => 'Draft',
                'reason' => $request->reason,
            ]);

            foreach ($request->lines as $lineData) {
                $diff = $lineData['actual_quantity'] - $lineData['system_quantity'];
                
                StockAdjustmentLine::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $lineData['product_id'],
                    'system_quantity' => $lineData['system_quantity'],
                    'actual_quantity' => $lineData['actual_quantity'],
                    'difference' => $diff,
                    'unit_price' => 0, // Simplified
                ]);
            }

            return redirect()->route('stock-adjustments.index')->with('success', 'Stock Adjustment ' . $adjNumber . ' created successfully');
        });
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['lines.product.unit', 'warehouse', 'company']);
        return view('inventory.stock_adjustments.show', compact('stockAdjustment'));
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status !== 'Draft') {
            return back()->with('error', 'Only Draft adjustments can be modified');
        }

        if ($request->action === 'approve') {
            return DB::transaction(function () use ($stockAdjustment) {
                $stockAdjustment->update(['status' => 'Approved']);

                foreach ($stockAdjustment->lines as $line) {
                    if ($line->difference != 0) {
                        $this->invService->recordTransaction(
                            $stockAdjustment->company_id,
                            $stockAdjustment->warehouse_id,
                            $line->product_id,
                            'Adjustment',
                            $line->difference,
                            $line->unit_price,
                            StockAdjustment::class,
                            $stockAdjustment->id,
                            $stockAdjustment->reason,
                            $stockAdjustment->adjustment_date
                        );
                    }
                }

                return redirect()->route('stock-adjustments.index')->with('success', 'Stock Adjustment Approved successfully. Inventory updated.');
            });
        }

        return redirect()->route('stock-adjustments.index');
    }
}
