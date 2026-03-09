<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreDeliveryOrderRequest;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderLine;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Services\DocumentNumberService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DeliveryOrderController extends Controller
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
            $dos = DeliveryOrder::with(['salesOrder.customer', 'warehouse'])->select(['delivery_orders.*']);
            return DataTables::of($dos)
                ->editColumn('delivery_date', function($row){ return $row->delivery_date->format('d/m/Y'); })
                ->addColumn('customer_name', function($row){ return $row->salesOrder->customer->name ?? '-'; })
                ->addColumn('so_number', function($row){ return $row->salesOrder->so_number ?? '-'; })
                ->addColumn('warehouse_name', function($row){ return $row->warehouse->name ?? '-'; })
                ->editColumn('status', function($row){
                    $class = match($row->status) {
                        'Draft' => 'secondary',
                        'Shipped' => 'success',
                        'Cancelled' => 'danger',
                        default => 'light'
                    };
                    return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('delivery-orders.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('sales.delivery_orders.index');
    }

    public function create(Request $request)
    {
        $salesOrder = null;
        if ($request->has('sales_order_id')) {
            $salesOrder = SalesOrder::with(['lines.product', 'lines.unit', 'customer'])->findOrFail($request->sales_order_id);
        }

        $salesOrders = SalesOrder::where('status', 'Approved')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $nextNumber = $this->docService->generate('DO', auth()->user()->company_id, 'DO');

        return view('sales.delivery_orders.create', compact('salesOrder', 'salesOrders', 'warehouses', 'nextNumber'));
    }

    public function store(StoreDeliveryOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $doNumber = $this->docService->generate('DO', auth()->user()->company_id, 'DO');
            
            $do = DeliveryOrder::create([
                'company_id' => auth()->user()->company_id,
                'sales_order_id' => $request->sales_order_id,
                'warehouse_id' => $request->warehouse_id,
                'do_number' => $doNumber,
                'delivery_date' => $request->delivery_date,
                'status' => 'Shipped', // Changed to shipped when processed
                'shipped_by' => $request->shipped_by,
                'notes' => $request->notes,
            ]);

            foreach ($request->lines as $lineData) {
                if ($lineData['quantity_shipped'] <= 0) continue;

                DeliveryOrderLine::create([
                    'delivery_order_id' => $do->id,
                    'sales_order_line_id' => $lineData['sales_order_line_id'] ?? null,
                    'product_id' => $lineData['product_id'],
                    'unit_id' => $lineData['unit_id'],
                    'quantity_ordered' => $lineData['quantity_ordered'] ?? 0,
                    'quantity_shipped' => $lineData['quantity_shipped'],
                ]);

                // Update stock levels
                $unitPrice = 0;
                if (!empty($lineData['sales_order_line_id'])) {
                    $soLine = \App\Models\SalesOrderLine::find($lineData['sales_order_line_id']);
                    $unitPrice = $soLine ? $soLine->unit_price : 0;
                }

                $this->invService->recordTransaction(
                    auth()->user()->company_id,
                    $request->warehouse_id,
                    $lineData['product_id'],
                    'DO',
                    -$lineData['quantity_shipped'], // Negative for out
                    $unitPrice,
                    DeliveryOrder::class,
                    $do->id,
                    'Shipped via DO ' . $doNumber,
                    $request->delivery_date
                );
            }

            // Update SO status
            if ($request->sales_order_id) {
                SalesOrder::find($request->sales_order_id)->update(['status' => 'Delivered']);
            }

            return redirect()->route('delivery-orders.index')->with('success', 'Delivery Order ' . $doNumber . ' created successfully');
        });
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['lines.product', 'lines.unit', 'salesOrder.customer', 'warehouse', 'company']);
        return view('sales.delivery_orders.show', compact('deliveryOrder'));
    }
}
