<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreStockTransferRequest;
use App\Models\StockTransfer;
use App\Models\StockTransferLine;
use App\Models\Warehouse;
use App\Services\DocumentNumberService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockTransferController extends Controller
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
            $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse'])->select(['stock_transfers.*']);
            return DataTables::of($transfers)
                ->editColumn('transfer_date', function($row){ return $row->transfer_date->format('d/m/Y'); })
                ->addColumn('from_warehouse_name', function($row){ return $row->fromWarehouse->name ?? '-'; })
                ->addColumn('to_warehouse_name', function($row){ return $row->toWarehouse->name ?? '-'; })
                ->editColumn('status', function($row){
                    $class = match($row->status) {
                        'Draft' => 'secondary',
                        'Completed' => 'success',
                        'Cancelled' => 'danger',
                        default => 'light'
                    };
                    return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('stock-transfers.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    if ($row->status === 'Draft') {
                        $btn .= ' <form action="'.route('stock-transfers.update', $row->id).'" method="POST" class="d-inline">
                                    '.csrf_field().'
                                    '.method_field('PUT').'
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm(\'Approve this transfer?\')"><i class="fas fa-check"></i></button>
                                  </form>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('inventory.stock_transfers.index');
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $nextNumber = $this->docService->generate('TRF', auth()->user()->company_id, 'TRF');
        return view('inventory.stock_transfers.create', compact('warehouses', 'nextNumber'));
    }

    public function store(StoreStockTransferRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $trfNumber = $this->docService->generate('TRF', auth()->user()->company_id, 'TRF');
            
            $transfer = StockTransfer::create([
                'company_id' => auth()->user()->company_id,
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'transfer_number' => $trfNumber,
                'transfer_date' => $request->transfer_date,
                'status' => 'Draft',
                'notes' => $request->notes,
            ]);

            foreach ($request->lines as $lineData) {
                StockTransferLine::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $lineData['product_id'],
                    'quantity' => $lineData['quantity'],
                ]);
            }

            return redirect()->route('stock-transfers.index')->with('success', 'Stock Transfer ' . $trfNumber . ' created successfully');
        });
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['lines.product.unit', 'fromWarehouse', 'toWarehouse', 'company']);
        return view('inventory.stock_transfers.show', compact('stockTransfer'));
    }

    public function update(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'Draft') {
            return back()->with('error', 'Only Draft transfers can be modified');
        }

        if ($request->action === 'approve') {
            return DB::transaction(function () use ($stockTransfer) {
                $stockTransfer->update(['status' => 'Completed']);

                foreach ($stockTransfer->lines as $line) {
                    // 1. Transfer Out (From Source Warehouse)
                    $this->invService->recordTransaction(
                        $stockTransfer->company_id,
                        $stockTransfer->from_warehouse_id,
                        $line->product_id,
                        'Transfer Out',
                        -$line->quantity,
                        0, // unit price not strictly needed for internal transfer unless tracking value
                        StockTransfer::class,
                        $stockTransfer->id,
                        'Transfer to ' . $stockTransfer->toWarehouse->name,
                        $stockTransfer->transfer_date
                    );

                    // 2. Transfer In (To Destination Warehouse)
                    $this->invService->recordTransaction(
                        $stockTransfer->company_id,
                        $stockTransfer->to_warehouse_id,
                        $line->product_id,
                        'Transfer In',
                        $line->quantity,
                        0,
                        StockTransfer::class,
                        $stockTransfer->id,
                        'Transfer from ' . $stockTransfer->fromWarehouse->name,
                        $stockTransfer->transfer_date
                    );
                }

                return redirect()->route('stock-transfers.index')->with('success', 'Stock Transfer Approved successfully. Inventory updated.');
            });
        }

        return redirect()->route('stock-transfers.index');
    }
}
