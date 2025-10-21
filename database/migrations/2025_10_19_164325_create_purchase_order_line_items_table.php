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
        Schema::create('purchase_order_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->string('line_item_id')->nullable();
            $table->string('item_id');
            $table->string('account_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('item_order')->default(0);
            $table->decimal('bcy_rate', 15, 2)->default(0);
            $table->decimal('purchase_rate', 15, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->integer('quantity_received')->default(0);
            $table->string('unit')->nullable();
            $table->decimal('item_total', 15, 2)->default(0);
            $table->string('tax_id')->nullable();
            $table->string('tds_tax_id')->nullable();
            $table->string('tax_name')->nullable();
            $table->string('tax_type')->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->string('image_id')->nullable();
            $table->string('image_name')->nullable();
            $table->string('image_type')->nullable();
            $table->string('reverse_charge_tax_id')->nullable();
            $table->string('hsn_or_sac')->nullable();
            $table->string('tax_exemption_code')->nullable();
            $table->string('location_id')->nullable();
            $table->string('tax_exemption_id')->nullable();
            $table->string('salesorder_item_id')->nullable();
            $table->timestamps();
            
            $table->index('purchase_order_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_line_items');
    }
};
