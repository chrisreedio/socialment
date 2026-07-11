@php
    $panelId = $panel->getId();
    $panelPath = $panel->getPath();
@endphp
<section class='filalite-panel flex flex-col gap-6 mt-4'>
    <div class="relative flex items-center">
        <div class="flex-grow border-t border-gray-400"></div>
        <span class="flex-shrink mx-4 text-gray-400 px-4">
            {{ config('socialment.view.prompt', 'Or Login Via') }}
        </span>
        <div class="flex-grow border-t border-gray-400"></div>
    </div>
    <div class='flex justify-center gap-x-4 p-2'>
        @foreach ($providers as $providerName => $provider)
            <x-filament::button
                href="{{ route('socialment.redirect', $providerName) }}"
                tag="a"
                icon="{{ $provider['icon'] }}"
                color="{{ $provider['color'] }}"
                size="{{ $provider['size'] }}"
            >
                {{ $provider['label'] }}
            </x-filament::button>
        @endforeach
    </div>
</section>
