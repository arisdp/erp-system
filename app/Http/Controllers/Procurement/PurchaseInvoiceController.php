<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Procurement\StorePurchaseInvoiceRequest;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceLine;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\TaxRate;
use App\Services\DocumentNumberService;
use App\Traits\HasTaxCalculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseInvoiceController extends Controller
{
    use HasTaxCalculation;

    protected $docService;

    public function __construct(DocumentNumberService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoices = PurchaseInvoice::with(['supplier', 'purchaseOrder'])->select(['purchase_invoices.*']);
            return DataTables::of($invoices)
                ->editColumn('invoice_date', function($row){ return $row->invoice_date->format('d/m/Y'); })
                ->editColumn('net_amount', function($row){ return number_format($row->net_amount, 2); })
                ->addColumn('supplier_name', function($row){ return $row->supplier->name ?? '-'; })
                ->editColumn('status', function($row){
                    $class = match($row->status) {
                        'Draft' => 'secondary',
                        'Open' => 'info',
                        'Paid' => 'success',
                        'Cancelled' => 'danger',
                        default => 'light'
                    };
                    return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('purchase-invoices.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('procurement.purchase_invoices.index');
    }

    public function create(Request $request)
    {
        $grnId = $request->query('goods_receipt_id');
        $goodsReceipt = null;
        if ($grnId) {
            $goodsReceipt = GoodsReceipt::with(['lines.product.unit', 'purchaseOrder'])->findOrFail($grnId);
        }

        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::where('status', 'Open')->get();
        $nextNumber = $this->docService->generate('PINV', auth()->user()->company_id, 'INV');

        return view('procurement.purchase_invoices.create', compact('suppliers', 'purchaseOrders', 'goodsReceipt', 'nextNumber'));
    }

    public function store(StorePurchaseInvoiceRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $invoiceNumber = $this->docService->generate('PINV', auth()->user()->company_id, 'INV');
            
            $invoice = PurchaseInvoice::create([
                'company_id' => auth()->user()->company_id,
                'supplier_id' => $request->supplier_id,
                'purchase_order_id' => $request->purchase_order_id,
                'invoice_number' => $invoiceNumber,
                'vendor_invoice_number' => $request->vendor_invoice_number,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'status' => 'Open',
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;
            $taxAmount = 0;

            foreach ($request->lines as $lineData) {
                $subtotal = $lineData['quantity'] * $lineData['unit_price'];
                $lineTax = $this->calculateTax($subtotal, $lineData['tax_rate_id'] ?? null);

                PurchaseInvoiceLine::create([
                    'purchase_invoice_id' => $invoice->id,
                    'goods_receipt_line_id' => $lineData['goods_receipt_line_id'] ?? null,
                    'product_id' => $lineData['product_id'],
                    'unit_id' => $lineData['unit_id'],
                    'tax_rate_id' => $lineData['tax_rate_id'] ?? null,
                    'quantity' => $lineData['quantity'],
                    'unit_price' => $lineData['unit_price'],
                    'tax_amount' => $lineTax,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
                $taxAmount += $lineTax;
            }

            $invoice->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'net_amount' => $this->calculateNet($totalAmount, $taxAmount),
            ]);

            return redirect()->route('purchase-invoices.index')->with('success', 'Purchase Invoice ' . $invoiceNumber . ' created successfully');
        });
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['lines.product', 'lines.unit', 'lines.taxRate', 'supplier', 'purchaseOrder', 'company']);
        return view('procurement.purchase_invoices.show', compact('purchaseInvoice'));
    }
}
