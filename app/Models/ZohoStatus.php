<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZohoStatus extends Model
{
    use HasFactory;

    protected $table = 'zoho_status';

    protected $fillable = [
        'name',
        'status',
        'message',
    ];

    public static function createStatus(string $name, string $status, string $message): self
    {
        return static::create([
            'name' => $name,
            'status' => $status,
            'message' => $message,
        ]);
    }
}
