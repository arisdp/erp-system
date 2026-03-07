<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class GoodsReceipt extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'purchase_order_id',
        'warehouse_id',
        'grn_number',
        'received_date',
        'status',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function lines()
    {
        return $this->hasMany(GoodsReceiptLine::class);
    }
}
