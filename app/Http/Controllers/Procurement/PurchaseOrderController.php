<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Procurement\StorePurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Unit;
use App\Models\TaxRate;
use App\Models\PaymentTerm;
use App\Models\Currency;
use App\Services\DocumentNumberService;
use App\Traits\HasTaxCalculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderController extends Controller
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
            $pos = PurchaseOrder::with(['supplier', 'currency'])->select(['purchase_orders.*']);
            return DataTables::of($pos)
                ->editColumn('order_date', function($row){ return $row->order_date->format('d/m/Y'); })
                ->editColumn('net_amount', function($row){ return number_format($row->net_amount, 2); })
                ->editColumn('status', function($row){
                    $class = match($row->status) {
                        'Draft' => 'secondary',
                        'Pending' => 'warning',
                        'Approved' => 'info',
                        'Open' => 'success',
                        'Closed' => 'dark',
                        'Cancelled' => 'danger',
                        default => 'light'
                    };
                    return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('purchase-orders.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="'.route('purchase-orders.edit', $row->id).'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('procurement.purchase_orders.index');
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::where('is_active', true)->with(['unit', 'category'])->get();
        $taxRates = TaxRate::where('is_active', true)->get();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        
        $nextNumber = $this->docService->generate('PO', auth()->user()->company_id, 'PUR');

        return view('procurement.purchase_orders.create', compact('suppliers', 'products', 'taxRates', 'paymentTerms', 'currencies', 'nextNumber'));
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $poNumber = $this->docService->generate('PO', auth()->user()->company_id, 'PUR');
            
            $po = PurchaseOrder::create([
                'company_id' => auth()->user()->company_id,
                'supplier_id' => $request->supplier_id,
                'payment_term_id' => $request->payment_term_id,
                'currency_id' => $request->currency_id,
                'po_number' => $poNumber,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
                'status' => 'Draft',
            ]);

            $totalAmount = 0;
            $taxAmount = 0;

            foreach ($request->lines as $lineData) {
                $product = Product::find($lineData['product_id']);
                $subtotal = $lineData['quantity'] * $lineData['unit_price'];
                $lineTax = $this->calculateTax($subtotal, $lineData['tax_rate_id'] ?? null);

                PurchaseOrderLine::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $lineData['product_id'],
                    'unit_id' => $product->unit_id,
                    'tax_rate_id' => $lineData['tax_rate_id'] ?? null,
                    'description' => $lineData['description'] ?? $product->name,
                    'quantity' => $lineData['quantity'],
                    'unit_price' => $lineData['unit_price'],
                    'tax_amount' => $lineTax,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
                $taxAmount += $lineTax;
            }

            $po->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'net_amount' => $this->calculateNet($totalAmount, $taxAmount),
            ]);

            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order ' . $poNumber . ' created successfully');
        });
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['lines.product', 'lines.unit', 'lines.taxRate', 'supplier', 'currency', 'paymentTerm']);
        return view('procurement.purchase_orders.show', compact('purchaseOrder'));
    }
}
