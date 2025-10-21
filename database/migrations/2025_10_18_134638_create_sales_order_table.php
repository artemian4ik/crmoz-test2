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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('salesorder_id')->nullable();
            $table->string('zcrm_potential_id')->nullable();
            $table->string('zcrm_potential_name')->nullable();
            $table->string('customer_name');
            $table->string('customer_id');
            $table->string('email')->nullable();
            $table->string('company_name')->nullable();
            $table->string('salesorder_number')->nullable();
            $table->string('reference_number')->nullable();
            $table->date('date');
            $table->date('shipment_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('shipment_days')->nullable();
            $table->string('due_by_days')->nullable();
            $table->string('due_in_days')->nullable();
            $table->string('currency_id');
            $table->string('currency_code')->default('UAH');
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('bcy_total', 15, 2)->default(0);
            $table->decimal('total_invoiced_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('quantity_invoiced')->default(0);
            $table->integer('quantity_packed')->default(0);
            $table->integer('quantity_shipped')->default(0);
            $table->string('status')->default('draft');
            $table->string('order_status')->default('draft');
            $table->string('invoiced_status')->nullable();
            $table->string('paid_status')->nullable();
            $table->string('shipped_status')->nullable();
            $table->string('current_sub_status')->nullable();
            $table->string('current_sub_status_id')->nullable();
            $table->string('source')->nullable();
            $table->string('sales_channel')->nullable();
            $table->string('sales_channel_formatted')->nullable();
            $table->string('order_fulfillment_type')->nullable();
            $table->boolean('is_drop_shipment')->default(false);
            $table->boolean('is_backorder')->default(false);
            $table->boolean('is_manually_fulfilled')->default(false);
            $table->string('delivery_method')->nullable();
            $table->string('delivery_method_id')->nullable();
            $table->string('pickup_location_id')->nullable();
            $table->boolean('is_emailed')->default(false);
            $table->boolean('is_viewed_in_mail')->default(false);
            $table->timestamp('mail_first_viewed_time')->nullable();
            $table->timestamp('mail_last_viewed_time')->nullable();
            $table->string('salesperson_name')->nullable();
            $table->boolean('has_attachment')->default(false);
            $table->json('tags')->nullable();
            $table->string('color_code')->nullable();
            $table->boolean('is_scheduled_for_quick_shipment_create')->default(false);
            $table->timestamp('zoho_created_time')->nullable();
            $table->timestamp('zoho_last_modified_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('customer_id');
            $table->index('salesorder_number');
            $table->index('status');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
