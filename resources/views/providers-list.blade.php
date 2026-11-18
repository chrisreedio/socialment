@php
    $panelId = $panel->getId();
@endphp
<section class="flex flex-col gap-y-6">
    <div class="flex items-center gap-x-4">
        <div class="flex-grow border-t border-gray-200 dark:border-white/10"></div>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ config('socialment.view.prompt', 'Or Login Via') }}
        </span>
        <div class="flex-grow border-t border-gray-200 dark:border-white/10"></div>
    </div>
    <div class="flex flex-wrap justify-center gap-4">
        @foreach ($providers as $providerName => $provider)
            <x-filament::button
                tag="a"
                :href="route('socialment.redirect.panel', ['panelId' => $panelId, 'provider' => $providerName])"
                :icon="$provider['icon'] ?? null"
                :color="$provider['color'] ?? 'gray'"
                :size="$provider['size'] ?? null"
            >
                {{ $provider['label'] }}
            </x-filament::button>
        @endforeach
    </div>
</section>
