<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreSalesOrderRequest;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\Customer;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\ApprovalRequest;
use App\Mail\ApprovalRequestMail;
use App\Services\DocumentNumberService;
use App\Traits\HasTaxCalculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class SalesOrderController extends Controller
{
    use HasTaxCalculation;

    protected $docService;
    protected $currencyService;

    public function __construct(DocumentNumberService $docService, \App\Services\CurrencyService $currencyService)
    {
        $this->docService = $docService;
        $this->currencyService = $currencyService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sos = SalesOrder::with(['customer'])->select(['sales_orders.*']);
            return DataTables::of($sos)
                ->editColumn('order_date', function ($row) {
                    return $row->order_date->format('d/m/Y');
                })
                ->editColumn('net_amount', function ($row) {
                    return number_format($row->net_amount, 2);
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->name ?? '-';
                })
                ->editColumn('status', function ($row) {
                    $class = match ($row->status) {
                        'Draft' => 'secondary',
                        'Approved' => 'info',
                        'Delivered' => 'success',
                        'Completed' => 'primary',
                        'Cancelled' => 'danger',
                        default => 'light'
                    };
                    return '<span class="badge badge-' . $class . '">' . $row->status . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('sales-orders.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
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
        $paymentTerms = \App\Models\PaymentTerm::all();
        $currencies = \App\Models\Currency::all();
        $nextNumber = $this->docService->generate('SO', auth()->user()->company_id, 'SO');

        return view('sales.sales_orders.create', compact('customers', 'products', 'taxRates', 'marketplaces', 'nextNumber', 'paymentTerms', 'currencies'));
    }

    public function store(StoreSalesOrderRequest $request)
    {
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
                'currency_id' => $request->currency_id,
                'exchange_rate' => $this->currencyService->getExchangeRate($request->currency_id, auth()->user()->company_id),
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;
            $taxAmount = 0;

            foreach ($request->lines as $lineData) {
                $subtotal = $lineData['quantity'] * $lineData['unit_price'];
                $lineTax = $this->calculateTax($subtotal, $lineData['tax_rate_id'] ?? null);

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
                'net_amount' => $this->calculateNet($totalAmount, $taxAmount, [
                    'fee' => $request->platform_fee ?? 0,
                    'discount' => $request->platform_discount ?? 0,
                    'voucher' => $request->platform_voucher ?? 0,
                ]),
            ]);

            return redirect()->route('sales-orders.index')->with('success', 'Sales Order ' . $soNumber . ' created successfully');
        });
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['lines.product', 'lines.unit', 'lines.taxRate', 'customer', 'company']);
        $approval = ApprovalRequest::where('approvable_type', SalesOrder::class)
            ->where('approvable_id', $salesOrder->id)
            ->latest()
            ->first();

        return view('sales.sales_orders.show', compact('salesOrder', 'approval'));
    }

    public function submit($id)
    {
        $so = SalesOrder::findOrFail($id);

        if ($so->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only Draft orders can be submitted for approval.');
        }

        DB::beginTransaction();
        try {
            $so->update(['status' => 'Pending']);

            $approval = ApprovalRequest::create([
                'company_id' => $so->company_id,
                'approvable_type' => SalesOrder::class,
                'approvable_id' => $so->id,
                'requested_by' => auth()->id(),
                'status' => 'Pending',
            ]);

            // In a real scenario, you'd send this to specific approvers.
            // For now, we'll try to send to anyone with an 'Admin' role or similar.
            // Simplified: Send to the user who created the company or similar logic.
            // Let's just send to a hardcoded email for now or the first admin found.
            $admin = \App\Models\User::role('Admin')->first();
            if ($admin && $admin->email) {
                Mail::to($admin->email)->send(new ApprovalRequestMail($approval));
            }

            DB::commit();
            return redirect()->route('sales-orders.show', $so->id)->with('success', 'Sales Order submitted for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit for approval: ' . $e->getMessage());
        }
    }
}
