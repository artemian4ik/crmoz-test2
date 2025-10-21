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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('purchaseorder_id')->nullable();
            $table->string('purchaseorder_number')->nullable();
            $table->string('vendor_id');
            $table->string('vendor_name')->nullable();
            $table->bigInteger('so_id')->nullable();
            $table->string('salesorder_id')->nullable();
            $table->date('date');
            $table->date('delivery_date')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('ship_via')->nullable();
            $table->string('attention')->nullable();
            $table->boolean('is_drop_shipment')->default(false);
            $table->boolean('is_inclusive_tax')->default(false);
            $table->boolean('is_backorder')->default(false);
            $table->string('template_id')->nullable();
            $table->string('delivery_org_address_id')->nullable();
            $table->string('delivery_customer_id')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->json('custom_fields')->nullable();
            $table->string('location_id')->nullable();
            $table->string('gst_treatment')->nullable();
            $table->string('tax_treatment')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('source_of_supply')->nullable();
            $table->string('destination_of_supply')->nullable();
            $table->string('currency_id')->nullable();
            $table->string('currency_code')->default('UAH');
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('bcy_total', 15, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('quantity_received')->default(0);
            $table->string('status')->default('draft');
            $table->string('order_status')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_emailed')->default(false);
            $table->boolean('has_attachment')->default(false);
            $table->json('tags')->nullable();
            $table->string('color_code')->nullable();
            $table->timestamp('zoho_created_time')->nullable();
            $table->timestamp('zoho_last_modified_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('vendor_id');
            $table->index('purchaseorder_number');
            $table->index('status');
            $table->index('date');
            $table->index('salesorder_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
