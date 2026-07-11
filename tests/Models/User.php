<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Tests\Models;

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use ChrisReedIO\Socialment\Tests\database\factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
    ];

    public $timestamps = false;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function connectedAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ConnectedAccount::class);
    }
}
