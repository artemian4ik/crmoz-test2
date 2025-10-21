<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Zoho\Inventory;
use App\Models\Items;
use App\Models\ZohoStatus;
use Illuminate\Support\Facades\Log;

class GetItemsTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-items-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get items from Zoho Inventory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Getting items from Zoho Inventory...');
            
            $inventoryService = new Inventory();
            
            $result = $inventoryService->getItems(1, 200);
            $removeOldItems = [];
            
            if ($result['success']) {
                $items = $result['data']['items'] ?? [];
                foreach ($items as $item) {
                    $removeOldItems[] = $item['item_id'];
                    
                    Items::updateOrCreate([
                        'item_id' => $item['item_id']
                    ], [
                        'item_id' => $item['item_id'],
                        'name' => $item['name'],
                        'item_name' => $item['item_name'],
                        'unit' => $item['unit'],
                        'status' => $item['status'],
                        'source' => 'zoho',
                        'is_combo_product' => $item['is_combo_product'],
                        'is_linked_with_zohocrm' => $item['is_linked_with_zohocrm'],
                        'zcrm_product_id' => $item['zcrm_product_id'],
                        'description' => $item['description'],
                        'brand' => $item['brand'],
                        'manufacturer' => $item['manufacturer'],
                        'rate' => $item['rate'],
                        'tax_id' => $item['tax_id'],
                        'tax_name' => $item['tax_name'],
                        'tax_percentage' => $item['tax_percentage'],
                        'purchase_account_id' => $item['purchase_account_id'],
                        'purchase_account_name' => $item['purchase_account_name'],
                        'account_id' => $item['account_id'],
                        'account_name' => $item['account_name'],
                        'purchase_description' => $item['purchase_description'],
                        'purchase_rate' => $item['purchase_rate'],
                        'image_type' => $item['image_type'],
                        'image_document_id' => $item['image_document_id'],
                        'length' => $item['length'],
                        'width' => $item['width'],
                        'height' => $item['height'],
                        'weight' => $item['weight'],
                        'weight_unit' => $item['weight_unit'],
                        'dimension_unit' => $item['dimension_unit'],
                        'tags' => $item['tags'],
                        'stock_on_hand' => $item['stock_on_hand'] ?? 0,
                        'sku' => $item['sku'],
                        'deleted_at' => null,
                    ]);
                }

                Items::whereNotIn('item_id', $removeOldItems)->delete();
                $this->info('Successfully got ' . count($items) . ' items');
                ZohoStatus::createStatus('get-items', 'success', 'Items fetched successfully');
            }
        } catch (\Exception $e) {
            Log::error('Get Items Task Error: ' . $e->getMessage());
            ZohoStatus::createStatus('get-items', 'error', $e->getMessage());
        }
    }
}
