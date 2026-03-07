<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\Customer;
use App\Models\Product;
use App\Models\TaxRate;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SalesOrderController extends Controller
{
    protected $docService;

    public function __construct(DocumentNumberService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sos = SalesOrder::with(['customer'])->select(['sales_orders.*']);
            return DataTables::of($sos)
                ->editColumn('order_date', function($row){ return $row->order_date->format('d/m/Y'); })
                ->editColumn('net_amount', function($row){ return number_format($row->net_amount, 2); })
                ->addColumn('customer_name', function($row){ return $row->customer->name ?? '-'; })
                ->editColumn('status', function($row){
                    $class = match($row->status) {
                        'Draft' => 'secondary',
                        'Approved' => 'info',
                        'Delivered' => 'success',
                        'Completed' => 'primary',
                        'Cancelled' => 'danger',
                        default => 'light'
                    };
                    return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('sales-orders.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('sales.sales_orders.index');
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with(['unit', 'taxRate'])->get();
        $taxRates = TaxRate::where('is_active', true)->get();
        $marketplaces = \App\Models\Marketplace::where('is_active', true)->get();
        $nextNumber = $this->docService->generate('SO', auth()->user()->company_id, 'SO');

        return view('sales.sales_orders.create', compact('customers', 'products', 'taxRates', 'marketplaces', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'transaction_type' => 'required|in:Offline,Online',
            'marketplace_id' => 'required_if:transaction_type,Online|nullable|exists:marketplaces,id',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity' => 'required|numeric|min:0.000001',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $soNumber = $this->docService->generate('SO', auth()->user()->company_id, 'SO');
            
            $so = SalesOrder::create([
                'company_id' => auth()->user()->company_id,
                'customer_id' => $request->customer_id,
                'marketplace_id' => $request->marketplace_id,
                'so_number' => $soNumber,
                'transaction_type' => $request->transaction_type,
                'order_date' => $request->order_date,
                'status' => 'Draft',
                'platform_fee' => $request->platform_fee ?? 0,
                'platform_discount' => $request->platform_discount ?? 0,
                'platform_voucher' => $request->platform_voucher ?? 0,
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;
            $taxAmount = 0;

            foreach ($request->lines as $lineData) {
                $subtotal = $lineData['quantity'] * $lineData['unit_price'];
                
                $lineTax = 0;
                if (!empty($lineData['tax_rate_id'])) {
                    $tr = TaxRate::find($lineData['tax_rate_id']);
                    $lineTax = ($subtotal * $tr->rate) / 100;
                }

                SalesOrderLine::create([
                    'sales_order_id' => $so->id,
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

            $so->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'net_amount' => ($totalAmount + $taxAmount) - ($request->platform_fee ?? 0) - ($request->platform_discount ?? 0) + ($request->platform_voucher ?? 0),
            ]);

            return redirect()->route('sales-orders.index')->with('success', 'Sales Order ' . $soNumber . ' created successfully');
        });
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['lines.product', 'lines.unit', 'lines.taxRate', 'customer', 'company']);
        return view('sales.sales_orders.show', compact('salesOrder'));
    }
}
