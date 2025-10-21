<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderLineItem extends Model
{
    protected $table = 'sales_order_line_items';

    protected $fillable = [
        'sales_order_id',
        'line_item_id',
        'item_id',
        'name',
        'description',
        'sku',
        'quantity',
        'rate',
        'discount',
        'discount_type',
        'tax_id',
        'tax_name',
        'tax_percentage',
        'item_total',
        'unit',
        'item_order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'rate' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'item_total' => 'decimal:2',
        'item_order' => 'integer',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id', 'item_id');
    }
}
