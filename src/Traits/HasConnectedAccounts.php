<?php

namespace ChrisReedIO\Socialment\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use ChrisReedIO\Socialment\Models\ConnectedAccount;

trait HasConnectedAccounts
{
    public function connectedAccounts(): HasMany
    {
        return $this->hasMany(ConnectedAccount::class);
    }
}
