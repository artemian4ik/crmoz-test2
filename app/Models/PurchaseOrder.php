<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'purchaseorder_id',
        'purchaseorder_number',
        'so_id',
        'vendor_id',
        'vendor_name',
        'salesorder_id',
        'date',
        'delivery_date',
        'reference_number',
        'ship_via',
        'attention',
        'is_drop_shipment',
        'is_inclusive_tax',
        'is_backorder',
        'template_id',
        'delivery_org_address_id',
        'delivery_customer_id',
        'notes',
        'terms',
        'exchange_rate',
        'custom_fields',
        'location_id',
        'gst_treatment',
        'tax_treatment',
        'gst_no',
        'source_of_supply',
        'destination_of_supply',
        'currency_id',
        'currency_code',
        'total',
        'bcy_total',
        'quantity',
        'quantity_received',
        'status',
        'order_status',
        'source',
        'is_emailed',
        'has_attachment',
        'tags',
        'color_code',
        'zoho_created_time',
        'zoho_last_modified_time',
    ];

    protected $casts = [
        'date' => 'date',
        'delivery_date' => 'date',
        'exchange_rate' => 'decimal:6',
        'custom_fields' => 'array',
        'total' => 'decimal:2',
        'bcy_total' => 'decimal:2',
        'quantity' => 'integer',
        'quantity_received' => 'integer',
        'is_drop_shipment' => 'boolean',
        'is_inclusive_tax' => 'boolean',
        'is_backorder' => 'boolean',
        'is_emailed' => 'boolean',
        'has_attachment' => 'boolean',
        'tags' => 'array',
        'zoho_created_time' => 'datetime',
        'zoho_last_modified_time' => 'datetime',
    ];

    /**
     * Get the line items for the purchase order.
     */
    public function lineItems()
    {
        return $this->hasMany(PurchaseOrderLineItem::class, 'purchase_order_id');
    }

    /**
     * Search scope for filtering purchase orders
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('purchaseorder_number', 'like', "%{$search}%")
              ->orWhere('vendor_name', 'like', "%{$search}%")
              ->orWhere('reference_number', 'like', "%{$search}%");
        });
    }

    /**
     * Filter draft purchase orders
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Filter active purchase orders
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'issued', 'open']);
    }
}

