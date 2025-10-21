<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderLineItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLineItem;
use App\Jobs\SendSalesOrderToZoho;
use App\Jobs\SendPurchaseOrderToZoho;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesOrder::query();

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $sortBy = $request->get('sortBy', 'date');
        $sortOrder = $request->get('sortOrder', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('perPage', 20);
        $salesOrders = $query->paginate($perPage);

        return response()->json($salesOrders);
    }

    public function show($id)
    {
        $salesOrder = SalesOrder::with(['customer', 'lineItems'])->findOrFail($id);
        return response()->json($salesOrder);
    }

    public function showBySalesorderId($salesorderId)
    {
        $salesOrder = SalesOrder::with(['customer', 'lineItems'])
            ->where('salesorder_id', $salesorderId)
            ->firstOrFail();
        return response()->json($salesOrder);
    }

    public function store(Request $request)
    {
        Log::info('Sales order data: ' . json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'date' => 'required|date',
            'line_items' => 'required|array|min:1',
            'line_items.*.item_id' => 'required|string',
            'line_items.*.name' => 'required|string',
            'line_items.*.quantity' => 'required|integer|min:1',
            'line_items.*.rate' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $salesOrderData = $request->except(['line_items', 'subtotal', 'total_tax']);
            
            $totalQuantity = collect($request->line_items)->sum('quantity');
            $salesOrderData['quantity'] = $totalQuantity;
            $salesOrderData['balance'] = $request->total;
            $salesOrderData['order_status'] = $request->status ?? 'draft';

            $salesOrder = SalesOrder::create($salesOrderData);

            foreach ($request->line_items as $index => $lineItem) {
                SalesOrderLineItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $lineItem['item_id'],
                    'name' => $lineItem['name'],
                    'description' => $lineItem['description'] ?? null,
                    'sku' => $lineItem['sku'] ?? null,
                    'quantity' => $lineItem['quantity'],
                    'rate' => $lineItem['rate'],
                    'discount' => $lineItem['discount'] ?? 0,
                    'tax_id' => $lineItem['tax_id'] ?? null,
                    'tax_name' => $lineItem['tax_name'] ?? null,
                    'tax_percentage' => $lineItem['tax_percentage'] ?? 0,
                    'item_total' => $lineItem['item_total'],
                    'item_order' => $index,
                ]);
            }

            DB::commit();

            $salesOrder->load('lineItems');

            SendSalesOrderToZoho::dispatch($salesOrder);

            return response()->json([
                'success' => true,
                'data' => $salesOrder,
                'message' => 'Замовлення створено успішно'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sales order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Помилка при створенні замовлення: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $salesOrder = SalesOrder::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'customer_name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $salesOrder->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $salesOrder,
            'message' => 'Замовлення оновлено успішно'
        ]);
    }

    public function destroy($id)
    {
        $salesOrder = SalesOrder::findOrFail($id);
        $salesOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Замовлення видалено успішно'
        ]);
    }

    public function statistics()
    {
        $stats = [
            'total' => SalesOrder::count(),
            'draft' => SalesOrder::draft()->count(),
            'confirmed' => SalesOrder::confirmed()->count(),
            'active' => SalesOrder::active()->count(),
            'total_amount' => SalesOrder::sum('total'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function storeWithPurchaseOrders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'date' => 'required|date',
            'line_items' => 'required|array|min:1',
            'purchase_order_items' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $salesOrderData = $request->except(['line_items', 'subtotal', 'total_tax', 'create_purchase_orders', 'purchase_order_items']);
            
            $totalQuantity = collect($request->line_items)->sum('quantity');
            $salesOrderData['quantity'] = $totalQuantity;
            $salesOrderData['balance'] = $request->total;
            $salesOrderData['order_status'] = $request->status ?? 'draft';
            
            $salesOrder = SalesOrder::create($salesOrderData);

            foreach ($request->line_items as $index => $lineItem) {
                SalesOrderLineItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $lineItem['item_id'],
                    'name' => $lineItem['name'],
                    'description' => $lineItem['description'] ?? null,
                    'sku' => $lineItem['sku'] ?? null,
                    'quantity' => $lineItem['quantity'],
                    'rate' => $lineItem['rate'],
                    'discount' => $lineItem['discount'] ?? 0,
                    'tax_id' => $lineItem['tax_id'] ?? null,
                    'tax_name' => $lineItem['tax_name'] ?? null,
                    'tax_percentage' => $lineItem['tax_percentage'] ?? 0,
                    'item_total' => $lineItem['item_total'],
                    'item_order' => $index,
                ]);
            }

            $purchaseOrdersCount = 0;
            $purchaseOrdersByVendor = [];

            foreach ($request->purchase_order_items as $key => $poItem) {
                if (!isset($poItem['create_po']) || !$poItem['create_po']) {
                    continue;
                }

                $vendorId = $poItem['vendor_id'] ?? 846374000000078168;
                
                if (!isset($purchaseOrdersByVendor[$vendorId])) {
                    $purchaseOrdersByVendor[$vendorId] = [
                        'items' => [],
                        'vendor_id' => $vendorId,
                        'vendor_name' => $poItem['vendor_name'] ?? 'Default Vendor'
                    ];
                }

                $purchaseOrdersByVendor[$vendorId]['items'][] = $poItem;
            }

            foreach ($purchaseOrdersByVendor as $vendorData) {
                $purchaseOrder = \App\Models\PurchaseOrder::create([
                    'vendor_id' => $vendorData['vendor_id'],
                    'vendor_name' => $vendorData['vendor_name'],
                    'so_id' => $salesOrder->id,
                    'date' => $request->date,
                    'status' => 'draft',
                    'currency_code' => $request->currency_code ?? 'UAH',
                    'is_drop_shipment' => true,
                ]);

                $totalQuantity = 0;
                $totalAmount = 0;

                foreach ($vendorData['items'] as $item) {
                    \App\Models\PurchaseOrderLineItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'item_id' => $item['item_id'],
                        'name' => $item['name'],
                        'description' => $item['description'] ?? null,
                        'quantity' => $item['quantity_to_order'],
                        'purchase_rate' => $item['rate'] ?? 0,
                        'bcy_rate' => $item['rate'] ?? 0,
                        'item_total' => ($item['rate'] ?? 0) * $item['quantity_to_order'],
                        'item_order' => 0,
                    ]);

                    $totalQuantity += $item['quantity_to_order'];
                    $totalAmount += ($item['rate'] ?? 0) * $item['quantity_to_order'];
                }

                $purchaseOrder->update([
                    'quantity' => $totalQuantity,
                    'total' => $totalAmount,
                    'bcy_total' => $totalAmount,
                ]);

                $purchaseOrdersCount++;
            }

            DB::commit();

            SendSalesOrderToZoho::dispatch($salesOrder, $purchaseOrder);

            return response()->json([
                'success' => true,
                'data' => $salesOrder,
                'purchase_orders_count' => $purchaseOrdersCount,
                'message' => "Замовлення створено успішно!"
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sales order with POs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Помилка при створенні замовлення: ' . $e->getMessage()
            ], 500);
        }
    }
}

