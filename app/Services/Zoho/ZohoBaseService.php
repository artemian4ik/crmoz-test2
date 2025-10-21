<?php

namespace App\Services\Zoho;

use App\Services\ZohoCrmService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class ZohoBaseService
{
    protected $zohoCrmService;
    protected $baseUrl;
    protected $module;

    public function __construct()
    {
        $this->zohoCrmService = new ZohoCrmService();
        $this->setBaseUrl();
    }

    abstract protected function setBaseUrl();

    public function getCrmService()
    {
        return $this->zohoCrmService;
    }

    protected function get($endpoint, $params = [])
    {
        return $this->makeRequest('GET', $endpoint, $params);
    }

    protected function post($endpoint, $data = [])
    {
        return $this->makeRequest('POST', $endpoint, $data);
    }

    protected function put($endpoint, $data = [])
    {
        return $this->makeRequest('PUT', $endpoint, $data);
    }

    protected function delete($endpoint)
    {
        return $this->makeRequest('DELETE', $endpoint);
    }

    protected function makeRequest($method, $endpoint, $params = [])
    {
        $accessToken = $this->zohoCrmService->getAccessToken();
        
        if (!$accessToken) {
            $this->zohoCrmService->refreshAccessToken();

            $accessToken = $this->zohoCrmService->getAccessToken();
            if (!$accessToken) {
                Log::error("No access token available for {$this->module}");
                return [
                    'success' => false,
                    'error' => 'No access token available'
                ];
            }
        }

        $url = $this->baseUrl . $endpoint;

        try {
            $request = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
            ]);

            $response = match($method) {
                'GET' => $request->get($url, $params),
                'POST' => $request->asForm()->post($url, $params),
                'PUT' => $request->asForm()->put($url, $params),
                'DELETE' => $request->delete($url),
                default => throw new \Exception("Unsupported HTTP method: {$method}")
            };

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            if ($response->status() === 401) {
                Log::info("Token expired for {$this->module}, refreshing...");
                
                if ($this->zohoCrmService->refreshAccessToken()) {
                    return $this->makeRequest($method, $endpoint, $params);
                }
            }

            Log::error("Zoho {$this->module} API request failed", [
                'method' => $method,
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $response->body(),
                'status' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error("Exception in Zoho {$this->module} API request", [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

