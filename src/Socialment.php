<?php

namespace ChrisReedIO\Socialment;

class Socialment
{
    protected array $providers = [];

    public function getProvider($driver)
    {
        return $this->providers[$driver];
    }

    public function registerProvider(string $provider, string $icon, string $label, array $scopes = []): static
    {
        $this->providers[$provider] = [
            'icon' => $icon,
            'label' => $label,
            'scopes' => $scopes
        ];

        return $this;
    }
}
