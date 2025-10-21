<?php

namespace App\Services\Zoho;

use Illuminate\Support\Facades\Log;

class Inventory extends ZohoBaseService
{
    protected function setBaseUrl()
    {
        $this->baseUrl = 'https://www.zohoapis.eu/inventory/v1';
        $this->module = 'inventory';
    }

    public function getItems($page = 1, $perPage = 200)
    {
        return $this->get('/items', [
            'page' => $page,
            'per_page' => $perPage
        ]);
    }

    public function getItem($itemId)
    {
        return $this->get("/items/{$itemId}");
    }

    public function createItem($itemData)
    {
        return $this->post('/items', [
            'JSONString' => json_encode($itemData)
        ]);
    }

    public function updateItem($itemId, $itemData)
    {
        return $this->put("/items/{$itemId}", [
            'JSONString' => json_encode($itemData)
        ]);
    }

    public function deleteItem($itemId)
    {
        return $this->delete("/items/{$itemId}");
    }

    public function getSalesOrders($page = 1, $perPage = 200)
    {
        return $this->get('/salesorders', [
            'page' => $page,
            'per_page' => $perPage
        ]);
    }

    public function getSalesOrder($orderId)
    {
        return $this->get("/salesorders/{$orderId}");
    }

    public function createSalesOrder($orderData)
    {
        return $this->post('/salesorders', [
            'JSONString' => json_encode($orderData)
        ]);
    }

    public function getInvoices($page = 1, $perPage = 200)
    {
        return $this->get('/invoices', [
            'page' => $page,
            'per_page' => $perPage
        ]);
    }

    public function getInvoice($invoiceId)
    {
        return $this->get("/invoices/{$invoiceId}");
    }

    public function createInvoice($invoiceData)
    {
        return $this->post('/invoices', [
            'JSONString' => json_encode($invoiceData)
        ]);
    }

    public function getContacts($page = 1, $perPage = 200)
    {
        return $this->get('/contacts', [
            'page' => $page,
            'per_page' => $perPage
        ]);
    }

    public function getContact($contactId)
    {
        return $this->get("/contacts/{$contactId}");
    }

    public function createContact($contactData)
    {
        return $this->post('/contacts', [
            'JSONString' => json_encode($contactData)
        ]);
    }

    public function updateContact($contactId, $contactData)
    {
        return $this->put("/contacts/{$contactId}", [
            'JSONString' => json_encode($contactData)
        ]);
    }

    public function searchItems($searchTerm)
    {
        return $this->get('/items', [
            'search_text' => $searchTerm
        ]);
    }

    public function getStockSummary($itemId = null)
    {
        $endpoint = $itemId ? "/items/{$itemId}/stocksummary" : '/items/stocksummary';
        return $this->get($endpoint);
    }

    public function getPurchaseOrders($page = 1, $perPage = 200)
    {
        return $this->get('/purchaseorders', [
            'page' => $page,
            'per_page' => $perPage
        ]);
    }

    public function getPurchaseOrder($orderId)
    {
        return $this->get("/purchaseorders/{$orderId}");
    }

    public function createPurchaseOrder($orderData)
    {
        return $this->post('/purchaseorders', [
            'JSONString' => json_encode($orderData)
        ]);
    }

    public function getOrganizations()
    {
        return $this->get('/organizations');
    }
}

