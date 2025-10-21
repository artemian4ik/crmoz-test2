<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Services\Zoho\Inventory;
use Illuminate\Support\Facades\Log;

class SendPurchaseOrderToZoho implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public SalesOrder $salesOrder, public PurchaseOrder $purchaseOrder)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->purchaseOrder->load('lineItems');
        
        $this->purchaseOrder->salesorder_id = $this->salesOrder->salesorder_id;
        $this->purchaseOrder->save();

        $data = $this->preparePurchaseOrderData();

        $zohoInventory = new Inventory();
        $response = $zohoInventory->createPurchaseOrder($data);
            
        if (isset($response['data']['purchaseorder']['purchaseorder_number'])) {
            $this->purchaseOrder->purchaseorder_id = $response['data']['purchaseorder']['purchaseorder_id'];
            $this->purchaseOrder->purchaseorder_number = $response['data']['purchaseorder']['purchaseorder_number'];
            $this->purchaseOrder->save();
        }
    }

    /**
     * Prepare data for Zoho API
     */
    protected function preparePurchaseOrderData(): array
    {
        $data = [
            'vendor_id' => $this->purchaseOrder->vendor_id,
        ];

        if ($this->purchaseOrder->date) {
            $data['date'] = $this->purchaseOrder->date->format('Y-m-d');
        }

        if ($this->purchaseOrder->delivery_date) {
            $data['delivery_date'] = $this->purchaseOrder->delivery_date->format('Y-m-d');
        }

        if ($this->purchaseOrder->reference_number) {
            $data['reference_number'] = $this->purchaseOrder->reference_number;
        }

        if ($this->purchaseOrder->ship_via) {
            $data['ship_via'] = $this->purchaseOrder->ship_via;
        }

        if ($this->purchaseOrder->salesorder_id) {
            $data['salesorder_id'] = $this->purchaseOrder->salesorder_id;
        }

        // if ($this->purchaseOrder->is_drop_shipment) {
        //     $data['is_drop_shipment'] = $this->purchaseOrder->is_drop_shipment;
        // }

        if ($this->purchaseOrder->is_inclusive_tax !== null) {
            $data['is_inclusive_tax'] = $this->purchaseOrder->is_inclusive_tax;
        }

        if ($this->purchaseOrder->is_backorder) {
            $data['is_backorder'] = $this->purchaseOrder->is_backorder;
        }

        if ($this->purchaseOrder->template_id) {
            $data['template_id'] = $this->purchaseOrder->template_id;
        }

        if ($this->purchaseOrder->attention) {
            $data['attention'] = $this->purchaseOrder->attention;
        }

        if ($this->purchaseOrder->delivery_org_address_id) {
            $data['delivery_org_address_id'] = $this->purchaseOrder->delivery_org_address_id;
        }

        if ($this->purchaseOrder->delivery_customer_id) {
            $data['delivery_customer_id'] = $this->purchaseOrder->delivery_customer_id;
        }

        if ($this->purchaseOrder->notes) {
            $data['notes'] = $this->purchaseOrder->notes;
        }

        if ($this->purchaseOrder->terms) {
            $data['terms'] = $this->purchaseOrder->terms;
        }

        if ($this->purchaseOrder->exchange_rate && $this->purchaseOrder->exchange_rate != 1) {
            $data['exchange_rate'] = $this->purchaseOrder->exchange_rate;
        }

        if ($this->purchaseOrder->custom_fields) {
            $data['custom_fields'] = $this->purchaseOrder->custom_fields;
        }

        if ($this->purchaseOrder->location_id) {
            $data['location_id'] = $this->purchaseOrder->location_id;
        }

        if ($this->purchaseOrder->gst_treatment) {
            $data['gst_treatment'] = $this->purchaseOrder->gst_treatment;
        }

        if ($this->purchaseOrder->tax_treatment) {
            $data['tax_treatment'] = $this->purchaseOrder->tax_treatment;
        }

        if ($this->purchaseOrder->gst_no) {
            $data['gst_no'] = $this->purchaseOrder->gst_no;
        }

        if ($this->purchaseOrder->source_of_supply) {
            $data['source_of_supply'] = $this->purchaseOrder->source_of_supply;
        }

        if ($this->purchaseOrder->destination_of_supply) {
            $data['destination_of_supply'] = $this->purchaseOrder->destination_of_supply;
        }

        if ($this->purchaseOrder->lineItems && count($this->purchaseOrder->lineItems) > 0) {
            $data['line_items'] = $this->prepareLineItems();
        }

        return $data;
    }

    /**
     * Prepare line items for Zoho API
     */
    protected function prepareLineItems(): array
    {
        return $this->purchaseOrder->lineItems->map(function ($lineItem) {
            $item = [
                'item_id' => $lineItem->item_id,
                'name' => $lineItem->name,
                'quantity' => $lineItem->quantity,
                'purchase_rate' => $lineItem->purchase_rate,
            ];

            $item['account_id'] = "846374000000000509";

            // if ($lineItem->description) {
            //     $item['description'] = $lineItem->description;
            // }

            // if ($lineItem->item_order !== null) {
            //     $item['item_order'] = $lineItem->item_order;
            // }

            // if ($lineItem->bcy_rate) {
            //     $item['bcy_rate'] = $lineItem->bcy_rate;
            // }

            // if ($lineItem->quantity_received) {
            //     $item['quantity_received'] = $lineItem->quantity_received;
            // }

            // if ($lineItem->unit) {
            //     $item['unit'] = $lineItem->unit;
            // }

            // if ($lineItem->item_total) {
            //     $item['item_total'] = $lineItem->item_total;
            // }

            // if ($lineItem->tax_id) {
            //     $item['tax_id'] = $lineItem->tax_id;
            // }

            // if ($lineItem->tds_tax_id) {
            //     $item['tds_tax_id'] = $lineItem->tds_tax_id;
            // }

            // if ($lineItem->tax_name) {
            //     $item['tax_name'] = $lineItem->tax_name;
            // }

            // if ($lineItem->tax_type) {
            //     $item['tax_type'] = $lineItem->tax_type;
            // }

            // if ($lineItem->tax_percentage) {
            //     $item['tax_percentage'] = $lineItem->tax_percentage;
            // }

            // if ($lineItem->image_id) {
            //     $item['image_id'] = $lineItem->image_id;
            // }

            // if ($lineItem->image_name) {
            //     $item['image_name'] = $lineItem->image_name;
            // }

            // if ($lineItem->image_type) {
            //     $item['image_type'] = $lineItem->image_type;
            // }

            // if ($lineItem->reverse_charge_tax_id) {
            //     $item['reverse_charge_tax_id'] = $lineItem->reverse_charge_tax_id;
            // }

            // if ($lineItem->hsn_or_sac) {
            //     $item['hsn_or_sac'] = $lineItem->hsn_or_sac;
            // }

            // if ($lineItem->tax_exemption_code) {
            //     $item['tax_exemption_code'] = $lineItem->tax_exemption_code;
            // }

            // if ($lineItem->location_id) {
            //     $item['location_id'] = $lineItem->location_id;
            // }

            // if ($lineItem->tax_exemption_id) {
            //     $item['tax_exemption_id'] = $lineItem->tax_exemption_id;
            // }

            if ($lineItem->salesorder_item_id) {
                $item['salesorder_item_id'] = $lineItem->salesorder_item_id;
            }

            return $item;
        })->toArray();
    }
}

