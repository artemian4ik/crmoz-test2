<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLineItem extends Model
{
    protected $table = 'purchase_order_line_items';

    protected $fillable = [
        'purchase_order_id',
        'line_item_id',
        'item_id',
        'account_id',
        'name',
        'description',
        'item_order',
        'bcy_rate',
        'purchase_rate',
        'quantity',
        'quantity_received',
        'unit',
        'item_total',
        'tax_id',
        'tds_tax_id',
        'tax_name',
        'tax_type',
        'tax_percentage',
        'image_id',
        'image_name',
        'image_type',
        'reverse_charge_tax_id',
        'hsn_or_sac',
        'tax_exemption_code',
        'location_id',
        'tax_exemption_id',
        'salesorder_item_id',
    ];

    protected $casts = [
        'item_order' => 'integer',
        'bcy_rate' => 'decimal:2',
        'purchase_rate' => 'decimal:2',
        'quantity' => 'integer',
        'quantity_received' => 'integer',
        'item_total' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
    ];

    /**
     * Get the purchase order that owns the line item.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}

