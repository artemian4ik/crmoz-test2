<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLineItem;
use App\Jobs\SendPurchaseOrderToZoho;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::query();

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('salesorder_id')) {
            $query->where('salesorder_id', $request->salesorder_id);
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
        $purchaseOrders = $query->paginate($perPage);

        return response()->json($purchaseOrders);
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['vendor', 'salesOrder', 'lineItems'])->findOrFail($id);
        return response()->json($purchaseOrder);
    }

    public function showByPurchaseorderId($purchaseorderId)
    {
        $purchaseOrder = PurchaseOrder::with(['vendor', 'salesOrder', 'lineItems'])
            ->where('purchaseorder_id', $purchaseorderId)
            ->firstOrFail();
        return response()->json($purchaseOrder);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|string',
            'vendor_name' => 'required|string|max:255',
            'date' => 'required|date',
            'purchaseorder_number' => 'required|string|unique:purchase_orders,purchaseorder_number',
            'line_items' => 'required|array|min:1',
            'line_items.*.item_id' => 'required|string',
            'line_items.*.name' => 'required|string',
            'line_items.*.quantity' => 'required|integer|min:1',
            'line_items.*.purchase_rate' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $purchaseOrderData = $request->except(['line_items', 'subtotal', 'total_tax']);
            
            $totalQuantity = collect($request->line_items)->sum('quantity');
            $purchaseOrderData['quantity'] = $totalQuantity;
            $purchaseOrderData['order_status'] = $request->status ?? 'draft';
            
            $purchaseOrder = PurchaseOrder::create($purchaseOrderData);

            foreach ($request->line_items as $index => $lineItem) {
                PurchaseOrderLineItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $lineItem['item_id'],
                    'name' => $lineItem['name'],
                    'description' => $lineItem['description'] ?? null,
                    'quantity' => $lineItem['quantity'],
                    'purchase_rate' => $lineItem['purchase_rate'],
                    'bcy_rate' => $lineItem['bcy_rate'] ?? $lineItem['purchase_rate'],
                    'quantity_received' => $lineItem['quantity_received'] ?? 0,
                    'unit' => $lineItem['unit'] ?? null,
                    'tax_id' => $lineItem['tax_id'] ?? null,
                    'tax_name' => $lineItem['tax_name'] ?? null,
                    'tax_percentage' => $lineItem['tax_percentage'] ?? 0,
                    'item_total' => $lineItem['item_total'],
                    'item_order' => $index,
                    'account_id' => $lineItem['account_id'] ?? null,
                    'salesorder_item_id' => $lineItem['salesorder_item_id'] ?? null,
                ]);
            }

            DB::commit();

            $purchaseOrder->load('lineItems');

            SendPurchaseOrderToZoho::dispatch($purchaseOrder);

            return response()->json([
                'success' => true,
                'data' => $purchaseOrder,
                'message' => 'Purchase Order створено успішно'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating purchase order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Помилка при створенні Purchase Order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'vendor_name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $purchaseOrder->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $purchaseOrder,
            'message' => 'Purchase Order оновлено успішно'
        ]);
    }

    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order видалено успішно'
        ]);
    }

    public function statistics()
    {
        $stats = [
            'total' => PurchaseOrder::count(),
            'draft' => PurchaseOrder::draft()->count(),
            'active' => PurchaseOrder::active()->count(),
            'total_amount' => PurchaseOrder::sum('total'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
