<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Zoho\Inventory;
use App\Models\PurchaseOrder;
use App\Models\ZohoStatus;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get purchase orders from Zoho Inventory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Getting purchase orders from Zoho Inventory...');
            
            $inventoryService = new Inventory();
            
            $result = $inventoryService->getItems(1, 200);
            $result = $result['data']['items'];

            foreach ($result as $item) {
               $itemData = $inventoryService->getItem($item['item_id']);
               $this->info(json_encode($itemData));
            }
            
            // $this->info(json_encode($result));
        } catch (\Exception $e) {
            $this->error('Get Purchase Orders Task Error: ' . $e->getMessage());
            Log::error('Get Purchase Orders Task Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
}
