<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Zoho\Inventory;
use App\Models\SalesOrder;
use App\Models\ZohoStatus;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GetSalesOrderTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-sales-order-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get sales orders from Zoho Inventory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Getting sales orders from Zoho Inventory...');
            
            $inventoryService = new Inventory();
            
            $result = $inventoryService->getSalesOrders(1, 200);
            $removeOldSalesOrders = [];
            
            if ($result['success']) {
                $salesOrders = $result['data']['salesorders'] ?? [];
                
                $this->info('Found ' . count($salesOrders) . ' sales orders');
                
                foreach ($salesOrders as $order) {
                    $removeOldSalesOrders[] = $order['salesorder_id'];
                    
                    $orderData = [
                        'salesorder_id' => $order['salesorder_id'],
                        'zcrm_potential_id' => $order['zcrm_potential_id'] ?? null,
                        'zcrm_potential_name' => $order['zcrm_potential_name'] ?? null,
                        'customer_name' => $order['customer_name'],
                        'customer_id' => $order['customer_id'],
                        'email' => $order['email'] ?? null,
                        'company_name' => $order['company_name'] ?? null,
                        'salesorder_number' => $order['salesorder_number'],
                        'reference_number' => $order['reference_number'] ?? null,
                        'date' => $order['date'] ? Carbon::parse($order['date'])->format('Y-m-d') : null,
                        'shipment_date' => $order['shipment_date'] ? Carbon::parse($order['shipment_date'])->format('Y-m-d') : null,
                        'delivery_date' => $order['delivery_date'] ? Carbon::parse($order['delivery_date'])->format('Y-m-d') : null,
                        'shipment_days' => $order['shipment_days'] ?? null,
                        'due_by_days' => $order['due_by_days'] ?? null,
                        'due_in_days' => $order['due_in_days'] ?? null,
                        'currency_id' => $order['currency_id'],
                        'currency_code' => $order['currency_code'] ?? 'UAH',
                        'total' => $order['total'] ?? 0,
                        'bcy_total' => $order['bcy_total'] ?? 0,
                        'total_invoiced_amount' => $order['total_invoiced_amount'] ?? 0,
                        'balance' => $order['balance'] ?? 0,
                        'quantity' => $order['quantity'] ?? 0,
                        'quantity_invoiced' => $order['quantity_invoiced'] ?? 0,
                        'quantity_packed' => $order['quantity_packed'] ?? 0,
                        'quantity_shipped' => $order['quantity_shipped'] ?? 0,
                        'status' => $order['status'] ?? 'draft',
                        'order_status' => $order['order_status'] ?? 'draft',
                        'invoiced_status' => $order['invoiced_status'] ?? null,
                        'paid_status' => $order['paid_status'] ?? null,
                        'shipped_status' => $order['shipped_status'] ?? null,
                        'current_sub_status' => $order['current_sub_status'] ?? null,
                        'current_sub_status_id' => $order['current_sub_status_id'] ?? null,
                        'source' => $order['source'] ?? null,
                        'sales_channel' => $order['sales_channel'] ?? null,
                        'sales_channel_formatted' => $order['sales_channel_formatted'] ?? null,
                        'order_fulfillment_type' => $order['order_fulfillment_type'] ?? null,
                        'is_drop_shipment' => $order['is_drop_shipment'] ?? false,
                        'is_backorder' => $order['is_backorder'] ?? false,
                        'is_manually_fulfilled' => $order['is_manually_fulfilled'] ?? false,
                        'delivery_method' => $order['delivery_method'] ?? null,
                        'delivery_method_id' => $order['delivery_method_id'] ?? null,
                        'pickup_location_id' => $order['pickup_location_id'] ?? null,
                        'is_emailed' => $order['is_emailed'] ?? false,
                        'is_viewed_in_mail' => $order['is_viewed_in_mail'] ?? false,
                        'mail_first_viewed_time' => isset($order['mail_first_viewed_time']) && $order['mail_first_viewed_time'] ? Carbon::parse($order['mail_first_viewed_time']) : null,
                        'mail_last_viewed_time' => isset($order['mail_last_viewed_time']) && $order['mail_last_viewed_time'] ? Carbon::parse($order['mail_last_viewed_time']) : null,
                        'salesperson_name' => $order['salesperson_name'] ?? null,
                        'has_attachment' => $order['has_attachment'] ?? false,
                        'tags' => $order['tags'] ?? [],
                        'color_code' => $order['color_code'] ?? null,
                        'is_scheduled_for_quick_shipment_create' => $order['is_scheduled_for_quick_shipment_create'] ?? false,
                        'zoho_created_time' => isset($order['created_time']) ? Carbon::parse($order['created_time']) : null,
                        'zoho_last_modified_time' => isset($order['last_modified_time']) ? Carbon::parse($order['last_modified_time']) : null,
                        'deleted_at' => null,
                    ];
                    
                    SalesOrder::updateOrCreate(
                        ['salesorder_id' => $order['salesorder_id']],
                        $orderData
                    );
                    
                    $this->info('Processed: ' . $order['salesorder_number']);
                }

                SalesOrder::whereNotIn('salesorder_id', $removeOldSalesOrders)->delete();
                
                $this->info('Successfully synced ' . count($salesOrders) . ' sales orders');
                ZohoStatus::createStatus('get-sales-orders', 'success', 'Sales orders fetched successfully');
                
                return Command::SUCCESS;
            } else {
                $errorMessage = $result['error'] ?? 'Unknown error';
                $this->error('Failed to get sales orders: ' . $errorMessage);
                ZohoStatus::createStatus('get-sales-orders', 'error', $errorMessage);
                
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Get Sales Orders Task Error: ' . $e->getMessage());
            Log::error('Get Sales Orders Task Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            ZohoStatus::createStatus('get-sales-orders', 'error', $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
