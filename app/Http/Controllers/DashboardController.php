<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Services\Zoho\Inventory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected $inventoryService;

    public function __construct()
    {
        $this->inventoryService = new Inventory();
    }

    public function getStatistics()
    {
        try {
            $statistics = Cache::remember('dashboard_statistics', 300, function () {
                
                $localItemsCount = Items::count();
                $customersCount = Customer::count();
                $salesOrdersCount = SalesOrder::count();
                $purchaseOrdersCount = PurchaseOrder::count();

                return [
                    'items' => [
                        'count' => $localItemsCount,
                        'label' => 'Товарів',
                        'icon' => 'box',
                        'color' => 'blue'
                    ],
                    'customers' => [
                        'count' => $customersCount,
                        'label' => 'Клієнтів',
                        'icon' => 'users',
                        'color' => 'green'
                    ],
                    'sales_orders' => [
                        'count' => $salesOrdersCount,
                        'label' => 'Замовлень на продаж',
                        'icon' => 'shopping-cart',
                        'color' => 'purple'
                    ],
                    'purchase_orders' => [
                        'count' => $purchaseOrdersCount,
                        'label' => 'Замовлень на закупівлю',
                        'icon' => 'shopping-bag',
                        'color' => 'orange'
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard Statistics Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Помилка отримання статистики',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getPurchaseOrdersCount()
    {
        try {
            $result = $this->inventoryService->getPurchaseOrders(1, 1);
            
            if ($result['success'] && isset($result['data']['page_context'])) {
                return $result['data']['page_context']['total'] ?? 0;
            }
            
            return 0;
        } catch (\Exception $e) {
            Log::warning('Failed to get purchase orders count: ' . $e->getMessage());
            return 0;
        }
    }

    private function getInvoicesCount()
    {
        try {
            $result = $this->inventoryService->getInvoices(1, 1);
            
            if ($result['success'] && isset($result['data']['page_context'])) {
                return $result['data']['page_context']['total'] ?? 0;
            }
            
            return 0;
        } catch (\Exception $e) {
            Log::warning('Failed to get invoices count: ' . $e->getMessage());
            return 0;
        }
    }
}

