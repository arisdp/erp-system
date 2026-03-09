<?php

namespace App\Http\Controllers\Api;

use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\Marketplace;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends BaseApiController
{
    /**
     * Handle incoming orders from external marketplaces
     */
    public function handleMarketplace(Request $request)
    {
        Log::info('Marketplace Webhook received', $request->all());

        $payload = $request->all();

        // This is a generic implementation. 
        // Real implementations would have drivers for Shopee, Tokopedia, etc.

        try {
            DB::beginTransaction();

            // 1. Identify Marketplace
            $marketplace = Marketplace::where('code', $payload['marketplace_code'] ?? 'GENERIC')->first();
            if (!$marketplace) {
                return $this->error('Marketplace not found', 404);
            }

            // 2. Identify or Create Customer
            $customer = Customer::where('email', $payload['customer_email'] ?? '')
                ->orWhere('phone', $payload['customer_phone'] ?? '')
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'company_id' => $marketplace->company_id,
                    'name' => $payload['customer_name'] ?? 'Marketplace Customer',
                    'email' => $payload['customer_email'] ?? null,
                    'phone' => $payload['customer_phone'] ?? null,
                    'type' => 'Online',
                    'code' => 'CUST-WEB-' . time(),
                ]);
            }

            // 3. Create Sales Order
            $soNumber = 'SO-WEB-' . ($payload['order_id'] ?? time());

            $salesOrder = SalesOrder::create([
                'company_id' => $marketplace->company_id,
                'customer_id' => $customer->id,
                'marketplace_id' => $marketplace->id,
                'so_number' => $soNumber,
                'transaction_type' => 'Online',
                'order_date' => now(),
                'status' => 'Pending',
                'total_amount' => $payload['total_amount'] ?? 0,
                'notes' => 'Imported via Webhook. Ext Ref: ' . ($payload['order_id'] ?? 'N/A'),
            ]);

            // 4. Create Lines
            if (isset($payload['items']) && is_array($payload['items'])) {
                foreach ($payload['items'] as $item) {
                    $product = Product::where('sku', $item['sku'])->first();
                    if ($product) {
                        SalesOrderLine::create([
                            'sales_order_id' => $salesOrder->id,
                            'product_id' => $product->id,
                            'unit_id' => $product->unit_id,
                            'quantity' => $item['qty'] ?? 1,
                            'unit_price' => $item['price'] ?? 0,
                            'subtotal' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                        ]);
                    }
                }
            }

            DB::commit();

            return $this->success(['so_number' => $soNumber], 'Order processed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return $this->error('Processing failed: ' . $e->getMessage(), 500);
        }
    }
}
