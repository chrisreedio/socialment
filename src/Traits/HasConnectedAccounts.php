<?php

namespace ChrisReedIO\Socialment\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasConnectedAccounts
{
    public function connectedAccounts(): HasMany
    {
        return $this->hasMany(ConnectedAccount::class);
    }
}
