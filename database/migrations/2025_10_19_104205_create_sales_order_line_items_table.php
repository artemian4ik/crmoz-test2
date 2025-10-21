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
        Schema::create('sales_order_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->onDelete('cascade');
            $table->string('line_item_id')->nullable();
            $table->string('item_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->string('discount_type')->default('fixed');
            $table->string('tax_id')->nullable();
            $table->string('tax_name')->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('item_total', 15, 2)->default(0);
            $table->string('unit')->nullable();
            $table->integer('item_order')->default(0);
            $table->timestamps();
            
            $table->index('sales_order_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_line_items');
    }
};
