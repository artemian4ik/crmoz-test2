<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_orders';

    protected $fillable = [
        'salesorder_id',
        'zcrm_potential_id',
        'zcrm_potential_name',
        'customer_name',
        'customer_id',
        'email',
        'company_name',
        'salesorder_number',
        'reference_number',
        'date',
        'shipment_date',
        'delivery_date',
        'shipment_days',
        'due_by_days',
        'due_in_days',
        'currency_id',
        'currency_code',
        'total',
        'bcy_total',
        'total_invoiced_amount',
        'balance',
        'quantity',
        'quantity_invoiced',
        'quantity_packed',
        'quantity_shipped',
        'status',
        'order_status',
        'invoiced_status',
        'paid_status',
        'shipped_status',
        'current_sub_status',
        'current_sub_status_id',
        'source',
        'sales_channel',
        'sales_channel_formatted',
        'order_fulfillment_type',
        'is_drop_shipment',
        'is_backorder',
        'is_manually_fulfilled',
        'delivery_method',
        'delivery_method_id',
        'pickup_location_id',
        'is_emailed',
        'is_viewed_in_mail',
        'mail_first_viewed_time',
        'mail_last_viewed_time',
        'salesperson_name',
        'has_attachment',
        'tags',
        'color_code',
        'is_scheduled_for_quick_shipment_create',
        'zoho_created_time',
        'zoho_last_modified_time',
    ];

    protected $casts = [
        'date' => 'date',
        'shipment_date' => 'date',
        'delivery_date' => 'date',
        'shipment_days' => 'integer',
        'due_by_days' => 'integer',
        'due_in_days' => 'integer',
        'total' => 'decimal:2',
        'bcy_total' => 'decimal:2',
        'total_invoiced_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'quantity' => 'integer',
        'quantity_invoiced' => 'integer',
        'quantity_packed' => 'integer',
        'quantity_shipped' => 'integer',
        'is_drop_shipment' => 'boolean',
        'is_backorder' => 'boolean',
        'is_manually_fulfilled' => 'boolean',
        'is_emailed' => 'boolean',
        'is_viewed_in_mail' => 'boolean',
        'has_attachment' => 'boolean',
        'is_scheduled_for_quick_shipment_create' => 'boolean',
        'tags' => 'array',
        'mail_first_viewed_time' => 'datetime',
        'mail_last_viewed_time' => 'datetime',
        'zoho_created_time' => 'datetime',
        'zoho_last_modified_time' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'contact_id');
    }

    public function lineItems()
    {
        return $this->hasMany(SalesOrderLineItem::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('salesorder_number', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('reference_number', 'like', "%{$search}%");
        });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'confirmed', 'open']);
    }
}

