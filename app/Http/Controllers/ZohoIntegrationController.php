<?php

namespace App\Http\Controllers;

use App\Models\ZohoIntegration;
use App\Services\ZohoCrmService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ZohoIntegrationController extends Controller
{
    protected $zohoService;

    public function __construct(ZohoCrmService $zohoService)
    {
        $this->zohoService = $zohoService;
    }

    public function index(): RedirectResponse
    {
        $authUrl = $this->zohoService->generateAuthUrl();

        Log::info('Zoho Integration URL: ' . $authUrl);
        
        return redirect($authUrl);
    }

    public function callback(Request $request): View
    {
        try {
            if (!$request->has('code')) {
                return view('zoho-integration.error', [
                    'error' => 'Code not received'
                ]);
            }

            $tokenData = $this->zohoService->getTokenData($request->get('code'));

            if (!$tokenData) {
                return view('zoho-integration.error', [
                    'error' => 'Failed to get access tokens'
                ]);
            }

            ZohoIntegration::create([
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? '',
                'expires_in' => $tokenData['expires_in'] ?? 3600,
            ]);

            return view('zoho-integration.success', [
                'message' => 'Integration with Zoho CRM successfully configured!'
            ]);

        } catch (\Exception $e) {
            Log::error('Zoho Integration Callback Error: ' . $e->getMessage());
            
            return view('zoho-integration.error', [
                'error' => 'Error configuring integration: ' . $e->getMessage()
            ]);
        }
    }

    public function status(): View
    {
        $integration = ZohoIntegration::getActive();
        
        return view('zoho-integration.status', [
            'integration' => $integration,
            'isActive' => $integration && !$integration->isTokenExpired()
        ]);
    }

    public function refreshTokens(Request $request)
    {
        $this->zohoService->refreshAccessToken();

        return response()->json([
            'success' => 'Tokens refreshed successfully',
        ], 200);
    }

    public function getTokenStatus()
    {
        $integration = ZohoIntegration::getActive();

        if (!$integration) {
            return response()->json([
                'status' => 'not_configured',
                'message' => 'Zoho інтеграція не налаштована',
                'is_active' => false
            ]);
        }

        $isExpired = $integration->isTokenExpired();
        $lastRefresh = $integration->updated_at;
        $expiresAt = $integration->created_at->addSeconds($integration->expires_in);
        
        return response()->json([
            'status' => $isExpired ? 'expired' : 'active',
            'message' => $isExpired ? 'Токен застарів' : 'Токен актуальний',
            'is_active' => !$isExpired,
            'last_refresh' => $lastRefresh->format('Y-m-d H:i:s'),
            'last_refresh_human' => $lastRefresh->diffForHumans(),
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'expires_at_human' => $expiresAt->diffForHumans()
        ]);
    }
}
