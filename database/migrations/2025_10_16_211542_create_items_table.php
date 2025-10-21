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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_id')->unique();
            $table->string('name');
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('source');
            $table->boolean('is_combo_product')->default(false);
            $table->boolean('is_linked_with_zohocrm')->default(false);
            $table->string('zcrm_product_id')->nullable();
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('manufacturer')->nullable();
            $table->decimal('rate', 8, 2)->default(0);
            $table->string('tax_id')->nullable();
            $table->string('tax_name')->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->string('purchase_account_id')->nullable();
            $table->string('purchase_account_name')->nullable();
            $table->string('account_id');
            $table->string('account_name');
            $table->text('purchase_description')->nullable();
            $table->decimal('purchase_rate', 8, 2)->default(0);
            $table->boolean('can_be_sold')->default(true);
            $table->boolean('can_be_purchased')->default(false);
            $table->boolean('track_inventory')->default(false);
            $table->enum('item_type', ['sales', 'purchase'])->default('sales');
            $table->enum('product_type', ['goods', 'services'])->default('goods');
            $table->boolean('has_attachment')->default(false);
            $table->boolean('is_returnable')->default(false);
            $table->string('sku')->nullable();
            $table->string('upc')->nullable();
            $table->string('ean')->nullable();
            $table->string('isbn')->nullable();
            $table->string('part_number')->nullable();
            $table->boolean('is_storage_location_enabled')->default(false);
            $table->string('image_name')->nullable();
            $table->string('image_type')->nullable();
            $table->string('image_document_id')->nullable();
            $table->string('length')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('weight_unit')->default('kg');
            $table->string('dimension_unit')->default('cm');
            $table->json('tags')->nullable();
            $table->integer('stock_on_hand')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
