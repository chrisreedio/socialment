<?php

namespace ChrisReedIO\Socialment\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectedAccount extends Model
{
    protected $fillable = [
        'provider',
        'provider_user_id',
        'name',
        'nickname',
        'email',
        'phone',
        'avatar',
        'token',
        'refresh_token',
        'expires_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('socialment.models.user'));
    }

    public function scopeProvider(Builder $query, string $provider): Builder
    {
        return $query->where('provider', $provider);
    }
}
