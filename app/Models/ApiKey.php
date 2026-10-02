<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'merchant_id',
        'key',
        'secret',
        'name',
        'last_used_at',
        'revoked_at',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public static function generateFor(Merchant $merchant, string $name): array
    {
        // 1. generate $key, $secret
        $key = 'sk_live_' . Str::random(32);
        $secret = Str::random(64);

        // 2. create the row, storing hash
        $model = self::create([
            'merchant_id' => $merchant->id,
            'name' => $name,
            'key' => $key,
            'secret' => hash('sha256', $secret),
        ]);

        return [
            'model' => $model,
            'key' => $key,
            'secret' => $secret,
        ];
    }

    public static function verify(string $key, string $secret): ?self 
    {
        $model = self::where('key', $key)->first();
        if (!$model) {return null;}
        if ($model->isRevoked()) {return null;}
        
        $hash = hash('sha256', $secret);

        if (!hash_equals($model->secret, $hash)) 
        {
            return null;
        }
        
        $model->update([
            'last_used_at' => now(),
        ]);

        return $model;

    }
}
