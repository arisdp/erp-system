<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'transaction_type' => 'required|in:Offline,Online',
            'marketplace_id' => 'required_if:transaction_type,Online|nullable|exists:marketplaces,id',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity' => 'required|numeric|min:0.000001',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}
