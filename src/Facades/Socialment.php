<?php

namespace ChrisReedIO\Socialment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ChrisReedIO\Socialment\Socialment
 */
class Socialment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \ChrisReedIO\Socialment\SocialmentPlugin::class;
    }
}
