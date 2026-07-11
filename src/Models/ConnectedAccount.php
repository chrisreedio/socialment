<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $provider
 * @property string $provider_user_id
 * @property string $name
 * @property string $nickname
 * @property string $email
 * @property string $avatar
 * @property string $token
 * @property string $refresh_token
 * @property \Datetime $expires_at
 */
class ConnectedAccount extends Model
{
    use HasFactory;

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
