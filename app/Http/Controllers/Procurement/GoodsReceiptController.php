<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Procurement\StoreGoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptLine;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Services\DocumentNumberService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GoodsReceiptController extends Controller
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
            $grns = GoodsReceipt::with(['purchaseOrder', 'warehouse'])->select(['goods_receipts.*']);
            return DataTables::of($grns)
                ->editColumn('received_date', function ($row) {
                    return $row->received_date->format('d/m/Y');
                })
                ->addColumn('po_number', function ($row) {
                    return $row->purchaseOrder->po_number ?? '-';
                })
                ->addColumn('warehouse_name', function ($row) {
                    return $row->warehouse->name ?? '-';
                })
                ->editColumn('status', function ($row) {
                    $class = match ($row->status) {
                        'Draft' => 'secondary',
                        'Received' => 'success',
                        'Cancelled' => 'danger',
                        default => 'light'
                    };
                    return '<span class="badge badge-' . $class . '">' . $row->status . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('goods-receipts.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('procurement.goods_receipts.index');
    }

    public function create(Request $request)
    {
        $poId = $request->query('purchase_order_id');
        $purchaseOrder = null;
        if ($poId) {
            $purchaseOrder = PurchaseOrder::with('lines.product.unit')->findOrFail($poId);
        }

        $purchaseOrders = PurchaseOrder::whereIn('status', ['Approved', 'Open'])->get();
        $warehouses = Warehouse::all();
        $nextNumber = $this->docService->generate('GRN', auth()->user()->company_id, 'GRN');

        return view('procurement.goods_receipts.create', compact('purchaseOrders', 'warehouses', 'purchaseOrder', 'nextNumber'));
    }

    public function store(StoreGoodsReceiptRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $grnNumber = $this->docService->generate('GRN', auth()->user()->company_id, 'GRN');

            $grn = GoodsReceipt::create([
                'company_id' => auth()->user()->company_id,
                'purchase_order_id' => $request->purchase_order_id,
                'warehouse_id' => $request->warehouse_id,
                'grn_number' => $grnNumber,
                'received_date' => $request->received_date,
                'received_by' => auth()->user()->name,
                'notes' => $request->notes,
                'status' => 'Received',
            ]);

            foreach ($request->lines as $lineData) {
                if ($lineData['quantity_received'] <= 0) continue;

                $product = \App\Models\Product::find($lineData['product_id']);
                $poNumber = $request->purchase_order_id ? PurchaseOrder::find($request->purchase_order_id)->po_number : 'NA';
                $dateStr = now()->format('Ymd');
                $generatedBatch = strtoupper($poNumber . '-' . $dateStr . '-' . ($product->sku ?? 'UNIT'));

                $grnLine = GoodsReceiptLine::create([
                    'goods_receipt_id' => $grn->id,
                    'purchase_order_line_id' => $lineData['purchase_order_line_id'] ?? null,
                    'product_id' => $lineData['product_id'],
                    'unit_id' => $lineData['unit_id'],
                    'quantity_ordered' => $lineData['quantity_ordered'] ?? 0,
                    'quantity_received' => $lineData['quantity_received'],
                    'batch_number' => $lineData['batch_number'] ?? $generatedBatch,
                    'expiry_date' => $lineData['expiry_date'] ?? null,
                ]);

                // Update stock levels
                $unitPrice = 0;
                if (!empty($lineData['purchase_order_line_id'])) {
                    $poLine = \App\Models\PurchaseOrderLine::find($lineData['purchase_order_line_id']);
                    $unitPrice = $poLine ? $poLine->unit_price : 0;
                }

                $this->invService->recordTransaction(
                    auth()->user()->company_id,
                    $request->warehouse_id,
                    $lineData['product_id'],
                    'GRN',
                    $lineData['quantity_received'], // Positive for in
                    $unitPrice,
                    GoodsReceipt::class,
                    $grn->id,
                    'Received via GRN ' . $grnNumber,
                    $request->received_date
                );
            }

            // Update PO status if all received
            if ($request->purchase_order_id) {
                // Simplified logic for now
                PurchaseOrder::find($request->purchase_order_id)->update(['status' => 'Open']);
            }

            return redirect()->route('goods-receipts.index')->with('success', 'Goods Receipt ' . $grnNumber . ' recorded successfully');
        });
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load(['lines.product', 'lines.unit', 'purchaseOrder', 'warehouse', 'company']);
        return view('procurement.goods_receipts.show', compact('goodsReceipt'));
    }

    public function printQr(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load(['lines.product', 'company']);
        return view('procurement.goods_receipts.print_qr', compact('goodsReceipt'));
    }
}
