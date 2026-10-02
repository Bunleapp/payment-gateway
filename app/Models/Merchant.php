<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'status',
    ];


    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'merchant_users')
        ->withPivot('role')
        ->withTimestamps();
    }

    public function apiKeys(): HasMany 
    {
        return $this->hasMany(ApiKey::class);
    }
}
