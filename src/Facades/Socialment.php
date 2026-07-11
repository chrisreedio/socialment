<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Facades;

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @see \ChrisReedIO\Socialment\SocialmentPlugin
 *
 * @method static static executePreLogin(ConnectedAccount $account)
 * @method static Model|null|\Closure createUser(ConnectedAccount $account)
 * @method static static executePostLogin(ConnectedAccount $account)
 */
class Socialment extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ChrisReedIO\Socialment\SocialmentPlugin::class;
    }
}
