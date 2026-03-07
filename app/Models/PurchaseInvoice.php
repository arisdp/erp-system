<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class PurchaseInvoice extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'supplier_id',
        'purchase_order_id',
        'invoice_number',
        'vendor_invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'total_amount',
        'tax_amount',
        'net_amount',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function lines()
    {
        return $this->hasMany(PurchaseInvoiceLine::class);
    }
}
