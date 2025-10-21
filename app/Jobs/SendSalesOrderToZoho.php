<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SalesOrder;
use App\Services\Zoho\Inventory;
use Illuminate\Support\Facades\Storage;
use App\Models\PurchaseOrder;
use App\Jobs\SendPurchaseOrderToZoho;

class SendSalesOrderToZoho implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public SalesOrder $salesOrder, public $purchaseOrder = null)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = $this->prepareSalesOrderData();

        $zohoInventory = new Inventory();
        $response = $zohoInventory->createSalesOrder($data);

        if (isset($response['data']['salesorder']['salesorder_id'])) {
            $updateData = [
                'salesorder_id' => $response['data']['salesorder']['salesorder_id'],
            ];
            
            if (isset($response['data']['salesorder']['salesorder_number'])) {
                $updateData['salesorder_number'] = $response['data']['salesorder']['salesorder_number'];
            }

            if (isset($response['data']['salesorder']['status'])) {
                $updateData['status'] = $response['data']['salesorder']['status'];
            }
            
            $this->salesOrder->update($updateData);

            if ($this->purchaseOrder) {
                SendPurchaseOrderToZoho::dispatch($this->salesOrder, $this->purchaseOrder);
            }
        }
    }

    /**
     * Prepare data for Zoho API
     */
    protected function prepareSalesOrderData(): array
    {
        $data = [
            'customer_id' => $this->salesOrder->customer_id,
        ];

        if ($this->salesOrder->date) {
            $data['date'] = $this->salesOrder->date->format('Y-m-d');
        }

        if ($this->salesOrder->shipment_date) {
            $data['shipment_date'] = $this->salesOrder->shipment_date->format('Y-m-d');
        }

        if ($this->salesOrder->reference_number) {
            $data['reference_number'] = $this->salesOrder->reference_number;
        }

        if ($this->salesOrder->delivery_method) {
            $data['delivery_method'] = $this->salesOrder->delivery_method;
        }

        if ($this->salesOrder->lineItems && $this->salesOrder->lineItems->count() > 0) {
            $data['line_items'] = $this->prepareLineItems();
        }

        return $data;
    }

    /**
     * Prepare line items for Zoho API
     */
    protected function prepareLineItems(): array
    {
        return $this->salesOrder->lineItems->map(function ($lineItem) {
            $item = [
                'item_id' => $lineItem->item_id,
                'name' => $lineItem->name,
                'quantity' => $lineItem->quantity,
                'rate' => $lineItem->rate,
            ];

            if ($lineItem->description) {
                $item['description'] = $lineItem->description;
            }

            if ($lineItem->unit) {
                $item['unit'] = $lineItem->unit;
            }

            if ($lineItem->tax_id) {
                $item['tax_id'] = $lineItem->tax_id;
            }

            if ($lineItem->tax_name) {
                $item['tax_name'] = $lineItem->tax_name;
            }

            if ($lineItem->tax_percentage) {
                $item['tax_percentage'] = $lineItem->tax_percentage;
            }

            if ($lineItem->item_total) {
                $item['item_total'] = $lineItem->item_total;
            }

            return $item;
        })->toArray();
    }
}