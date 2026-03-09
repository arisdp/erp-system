<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::where('is_active', true)
            ->with(['category', 'unit'])
            ->paginate($request->get('limit', 15));

        return $this->success($products);
    }

    public function show($id): JsonResponse
    {
        $product = Product::with(['category', 'unit'])->find($id);

        if (!$product) {
            return $this->error('Product not found', 404);
        }

        return $this->success($product);
    }
}
