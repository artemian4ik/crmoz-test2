<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('contact_id')->nullable();
            $table->string('contact_name');
            $table->string('customer_name')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('website')->nullable();
            $table->string('language_code')->nullable();
            $table->string('language_code_formatted')->nullable();
            $table->enum('contact_type', ['customer', 'vendor', 'both'])->default('customer');
            $table->string('contact_type_formatted')->nullable();
            $table->enum('status', ['active', 'inactive', 'crm'])->default('active');
            $table->string('customer_sub_type')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_linked_with_zohocrm')->default(false);
            $table->integer('payment_terms')->default(0);
            $table->string('payment_terms_label')->nullable();
            $table->string('currency_id')->nullable();
            $table->string('currency_code')->default('UAH');
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            
            // Financial amounts
            $table->decimal('outstanding_receivable_amount', 15, 2)->default(0);
            $table->decimal('outstanding_receivable_amount_bcy', 15, 2)->default(0);
            $table->decimal('outstanding_payable_amount', 15, 2)->default(0);
            $table->decimal('outstanding_payable_amount_bcy', 15, 2)->default(0);
            $table->decimal('unused_credits_receivable_amount', 15, 2)->default(0);
            $table->decimal('unused_credits_receivable_amount_bcy', 15, 2)->default(0);
            $table->decimal('unused_credits_payable_amount', 15, 2)->default(0);
            $table->decimal('unused_credits_payable_amount_bcy', 15, 2)->default(0);
            
            // Contact person info
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            
            // Portal info
            $table->string('portal_status')->default('disabled');
            $table->string('portal_status_formatted')->nullable();
            
            // Custom fields and tags
            $table->json('custom_fields')->nullable();
            $table->json('custom_field_hash')->nullable();
            $table->json('tags')->nullable();
            
            // Additional flags
            $table->boolean('ach_supported')->default(false);
            $table->boolean('has_attachment')->default(false);
            
            // Zoho timestamps
            $table->timestamp('zoho_created_time')->nullable();
            $table->timestamp('zoho_last_modified_time')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
