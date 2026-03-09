<?php

namespace App\Http\Controllers\Api;

use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SalesOrderController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $sos = SalesOrder::with(['customer', 'lines.product'])
            ->latest()
            ->paginate($request->get('limit', 15));

        return $this->success($sos);
    }

    public function show($id): JsonResponse
    {
        $so = SalesOrder::with(['customer', 'lines.product', 'lines.unit'])->find($id);

        if (!$so) {
            return $this->error('Sales Order not found', 404);
        }

        return $this->success($so);
    }
}
