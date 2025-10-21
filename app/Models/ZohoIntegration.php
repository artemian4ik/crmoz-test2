<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZohoIntegration extends Model
{
    use HasFactory;

    protected $table = 'zoho_integration';

    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_in',
    ];

    public function isTokenExpired(): bool
    {
        return $this->updated_at->addSeconds($this->expires_in)->isPast();
    }

    public function updateTokens(string $accessToken, string $refreshToken, int $expiresIn): bool
    {
        return $this->update([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn,
        ]);
    }

    public static function getActive(): ?self
    {
        return static::latest()->first();
    }
}
