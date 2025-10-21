<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'contact_id',
        'contact_name',
        'customer_name',
        'vendor_name',
        'company_name',
        'website',
        'language_code',
        'language_code_formatted',
        'contact_type',
        'contact_type_formatted',
        'status',
        'customer_sub_type',
        'source',
        'is_linked_with_zohocrm',
        'payment_terms',
        'payment_terms_label',
        'currency_id',
        'currency_code',
        'twitter',
        'facebook',
        'outstanding_receivable_amount',
        'outstanding_receivable_amount_bcy',
        'outstanding_payable_amount',
        'outstanding_payable_amount_bcy',
        'unused_credits_receivable_amount',
        'unused_credits_receivable_amount_bcy',
        'unused_credits_payable_amount',
        'unused_credits_payable_amount_bcy',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'portal_status',
        'portal_status_formatted',
        'custom_fields',
        'custom_field_hash',
        'tags',
        'ach_supported',
        'has_attachment',
        'zoho_created_time',
        'zoho_last_modified_time',
    ];

    protected $casts = [
        'is_linked_with_zohocrm' => 'boolean',
        'payment_terms' => 'integer',
        'outstanding_receivable_amount' => 'decimal:2',
        'outstanding_receivable_amount_bcy' => 'decimal:2',
        'outstanding_payable_amount' => 'decimal:2',
        'outstanding_payable_amount_bcy' => 'decimal:2',
        'unused_credits_receivable_amount' => 'decimal:2',
        'unused_credits_receivable_amount_bcy' => 'decimal:2',
        'unused_credits_payable_amount' => 'decimal:2',
        'unused_credits_payable_amount_bcy' => 'decimal:2',
        'custom_fields' => 'array',
        'custom_field_hash' => 'array',
        'tags' => 'array',
        'ach_supported' => 'boolean',
        'has_attachment' => 'boolean',
        'zoho_created_time' => 'datetime',
        'zoho_last_modified_time' => 'datetime',
    ];
}

