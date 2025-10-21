<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Items extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'items';

    protected $fillable = [
        'item_id',
        'name',
        'item_name',
        'unit',
        'status',
        'source',
        'is_combo_product',
        'is_linked_with_zohocrm',
        'zcrm_product_id',
        'description',
        'brand',
        'manufacturer',
        'rate',
        'tax_id',
        'tax_name',
        'tax_percentage',
        'purchase_account_id',
        'purchase_account_name',
        'account_id',
        'account_name',
        'purchase_description',
        'purchase_rate',
        'can_be_sold',
        'can_be_purchased',
        'track_inventory',
        'item_type',
        'product_type',
        'has_attachment',
        'is_returnable',
        'sku',
        'upc',
        'ean',
        'isbn',
        'part_number',
        'is_storage_location_enabled',
        'image_name',
        'image_type',
        'image_document_id',
        'length',
        'width',
        'height',
        'weight',
        'weight_unit',
        'dimension_unit',
        'tags',
        'stock_on_hand'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_combo_product' => 'boolean',
        'is_linked_with_zohocrm' => 'boolean',
        'rate' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'purchase_rate' => 'decimal:2',
        'can_be_sold' => 'boolean',
        'can_be_purchased' => 'boolean',
        'track_inventory' => 'boolean',
        'has_attachment' => 'boolean',
        'is_returnable' => 'boolean',
        'is_storage_location_enabled' => 'boolean'
    ];
}