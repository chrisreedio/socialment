<?php

namespace ChrisReedIO\Socialment\Tests\Fixtures;

use ChrisReedIO\Socialment\Traits\HasConnectedAccounts;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasConnectedAccounts;

    protected $guarded = [];

    protected $hidden = ['password'];
}
