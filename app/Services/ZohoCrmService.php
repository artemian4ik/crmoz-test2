<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ZohoIntegration;

class ZohoCrmService 
{
    private $clientId;
    private $clientSecret;
    private $scope;
    private $redirectUri;
    private $accessToken;
    private $refreshToken;
    private $baseUrl;
    private $expiresIn;

    public function __construct()
    {
        $this->clientId = config('services.zoho.client_id');
        $this->clientSecret = config('services.zoho.client_secret');
        $this->scope = config('services.zoho.scope');
        $this->redirectUri = url('/zoho-integration/callback');
        $this->baseUrl = 'https://www.zohoapis.eu/crm/v6';
        
        $this->loadTokensFromDatabase();
    }

    public function getAccountIdByEmail($accountData)
    {
        if($this->getAccountByEmail($accountData['Email'])) {
            return $this->getAccountByEmail($accountData['Email']);
        }
        
        return $this->createAccount($accountData);
    }

    private function loadTokensFromDatabase()
    {
        $integration = ZohoIntegration::getActive();

        $this->accessToken = $integration->access_token;
        $this->refreshToken = $integration->refresh_token;
        $this->expiresIn = $integration->expires_in;
        
        if (!$integration || $integration->isTokenExpired()) {
            $this->refreshAccessToken();
        }
    }   

    public function generateAuthUrl()
    {
        $params = [
            'response_type' => 'code',
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'scope'         => $this->scope,
            'access_type'   => 'offline',
            'prompt'        => 'consent'
        ];

        return 'https://accounts.zoho.eu/oauth/v2/auth?' . http_build_query($params);
    }

    public function getTokenData($code = null)
    {
        $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            $this->accessToken = $data['access_token'];
            return $data;
        }

        Log::error('Failed to get token data', ['response' => $response->body()]);
        return null;
    }

    /**
     * Get new access token using refresh token
     */
    public function refreshAccessToken()
    {
        try {
            $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
                'refresh_token' => $this->refreshToken,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->accessToken = $data['access_token'];

                $integration = ZohoIntegration::getActive();
                if ($integration) {
                    $integration->update([
                        'access_token' => $data['access_token'],
                        'updated_at' => now(),
                    ]);
                }

                Log::info('Zoho CRM access token refreshed successfully');
                return true;
            }

            Log::error('Failed to refresh Zoho CRM access token', ['response' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error('Exception while refreshing Zoho CRM access token', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
