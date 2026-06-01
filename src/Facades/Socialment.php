<?php

namespace ChrisReedIO\Socialment\Facades;

use ChrisReedIO\Socialment\SocialmentPlugin;
use Illuminate\Support\Facades\Facade;

/**
 * @see SocialmentPlugin
 */
class Socialment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SocialmentPlugin::class;
    }
}
