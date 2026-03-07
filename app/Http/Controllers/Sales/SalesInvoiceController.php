<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Models\SalesOrder;
use App\Models\DeliveryOrder;
use App\Models\Customer;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SalesInvoiceController extends Controller
{
    protected $docNumberService;

    public function __construct(DocumentNumberService $docNumberService)
    {
        $this->docNumberService = $docNumberService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SalesInvoice::with(['customer'])->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('invoice_date', function($row){ return $row->invoice_date->format('d/m/Y'); })
                ->editColumn('net_amount', function($row){ return number_format($row->net_amount, 2); })
                ->addColumn('customer_name', function($row){ return $row->customer->name ?? '-'; })
                ->editColumn('status', function($row){
                    $class = match($row->status) {
                        'Draft' => 'secondary',
                        'Unpaid' => 'warning',
                        'Partial' => 'info',
                        'Paid' => 'success',
                        'Cancelled' => 'danger',
                        default => 'light'
                    };
                    return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="'.route('sales-invoices.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('sales.sales_invoices.index');
    }

    public function create(Request $request)
    {
        $customers = Customer::where('is_active', true)->get();
        $sourceType = null;
        $sourceData = null;

        if ($request->has('sales_order_id')) {
            $sourceType = 'SO';
            $sourceData = SalesOrder::with(['lines.product', 'lines.unit', 'lines.taxRate', 'customer'])->findOrFail($request->sales_order_id);
        } elseif ($request->has('delivery_order_id')) {
            $sourceType = 'DO';
            $sourceData = DeliveryOrder::with(['lines.product', 'lines.unit', 'salesOrder.customer'])->findOrFail($request->delivery_order_id);
        }

        $nextNumber = $this->docNumberService->generate('SI', date('Y-m-d'));

        return view('sales.sales_invoices.create', compact('customers', 'sourceType', 'sourceData', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $invoiceNumber = $this->docNumberService->generate('SI', $request->invoice_date);
            
            $invoice = SalesInvoice::create([
                'company_id' => auth()->user()->company_id,
                'customer_id' => $request->customer_id,
                'sales_order_id' => $request->sales_order_id,
                'delivery_order_id' => $request->delivery_order_id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'status' => 'Unpaid',
                'notes' => $request->notes,
                'platform_fee' => $request->platform_fee ?? 0,
            ]);

            $totalAmount = 0;
            $taxAmount = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $lineTax = ($subtotal * ($item['tax_rate'] ?? 0)) / 100;

                SalesInvoiceLine::create([
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'tax_rate_id' => $item['tax_rate_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_amount' => $lineTax,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
                $taxAmount += $lineTax;
            }

            $invoice->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'net_amount' => ($totalAmount + $taxAmount) - ($request->platform_fee ?? 0),
            ]);

            return redirect()->route('sales-invoices.index')->with('success', 'Invoice ' . $invoiceNumber . ' created successfully');
        });
    }

    public function show(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['customer', 'lines.product', 'lines.unit', 'lines.taxRate', 'salesOrder']);
        return view('sales.sales_invoices.show', compact('salesInvoice'));
    }
}
