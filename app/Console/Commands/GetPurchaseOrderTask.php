<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Zoho\Inventory;
use App\Models\PurchaseOrder;
use App\Models\ZohoStatus;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GetPurchaseOrderTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-purchase-order-task';

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
            
            $result = $inventoryService->getPurchaseOrders(1, 200);
            $removeOldPurchaseOrders = [];
            
            if ($result['success']) {
                $purchaseOrders = $result['data']['purchaseorders'] ?? [];
                
                $this->info('Found ' . count($purchaseOrders) . ' purchase orders');
                
                foreach ($purchaseOrders as $order) {
                    $removeOldPurchaseOrders[] = $order['purchaseorder_id'];
                    
                    $orderData = [
                        'purchaseorder_id' => $order['purchaseorder_id'],
                        'purchaseorder_number' => $order['purchaseorder_number'],
                        'vendor_id' => $order['vendor_id'],
                        'vendor_name' => $order['vendor_name'] ?? null,
                        'salesorder_id' => $order['salesorder_id'] ?? null,
                        'date' => $order['date'] ? Carbon::parse($order['date'])->format('Y-m-d') : null,
                        'delivery_date' => $order['delivery_date'] ? Carbon::parse($order['delivery_date'])->format('Y-m-d') : null,
                        'reference_number' => $order['reference_number'] ?? null,
                        'ship_via' => $order['ship_via'] ?? null,
                        'attention' => $order['attention'] ?? null,
                        'is_drop_shipment' => $order['is_drop_shipment'] ?? false,
                        'is_inclusive_tax' => $order['is_inclusive_tax'] ?? false,
                        'is_backorder' => $order['is_backorder'] ?? false,
                        'template_id' => $order['template_id'] ?? null,
                        'delivery_org_address_id' => $order['delivery_org_address_id'] ?? null,
                        'delivery_customer_id' => $order['delivery_customer_id'] ?? null,
                        'notes' => $order['notes'] ?? null,
                        'terms' => $order['terms'] ?? null,
                        'exchange_rate' => $order['exchange_rate'] ?? 1,
                        'custom_fields' => $order['custom_fields'] ?? null,
                        'location_id' => $order['location_id'] ?? null,
                        'gst_treatment' => $order['gst_treatment'] ?? null,
                        'tax_treatment' => $order['tax_treatment'] ?? null,
                        'gst_no' => $order['gst_no'] ?? null,
                        'source_of_supply' => $order['source_of_supply'] ?? null,
                        'destination_of_supply' => $order['destination_of_supply'] ?? null,
                        'currency_id' => $order['currency_id'] ?? null,
                        'currency_code' => $order['currency_code'] ?? 'UAH',
                        'total' => $order['total'] ?? 0,
                        'bcy_total' => $order['bcy_total'] ?? 0,
                        'quantity' => $order['quantity'] ?? 0,
                        'quantity_received' => $order['quantity_received'] ?? 0,
                        'status' => $order['status'] ?? 'draft',
                        'order_status' => $order['order_status'] ?? null,
                        'source' => $order['source'] ?? null,
                        'is_emailed' => $order['is_emailed'] ?? false,
                        'has_attachment' => $order['has_attachment'] ?? false,
                        'tags' => $order['tags'] ?? [],
                        'color_code' => $order['color_code'] ?? null,
                        'zoho_created_time' => isset($order['created_time']) ? Carbon::parse($order['created_time']) : null,
                        'zoho_last_modified_time' => isset($order['last_modified_time']) ? Carbon::parse($order['last_modified_time']) : null,
                        'deleted_at' => null,
                    ];
                    
                    PurchaseOrder::updateOrCreate(
                        ['purchaseorder_id' => $order['purchaseorder_id']],
                        $orderData
                    );
                    
                    $this->info('Processed: ' . $order['purchaseorder_number']);
                }

                PurchaseOrder::whereNotIn('purchaseorder_id', $removeOldPurchaseOrders)->delete();
                
                $this->info('Successfully synced ' . count($purchaseOrders) . ' purchase orders');
                ZohoStatus::createStatus('get-purchase-orders', 'success', 'Purchase orders fetched successfully');
                
                return Command::SUCCESS;
            } else {
                $errorMessage = $result['error'] ?? 'Unknown error';
                $this->error('Failed to get purchase orders: ' . $errorMessage);
                ZohoStatus::createStatus('get-purchase-orders', 'error', $errorMessage);
                
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Get Purchase Orders Task Error: ' . $e->getMessage());
            Log::error('Get Purchase Orders Task Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            ZohoStatus::createStatus('get-purchase-orders', 'error', $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
