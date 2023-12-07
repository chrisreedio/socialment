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
            <a class='ring-2 ring-slate-700/50 hover:ring-slate-600/70 transition-all rounded-lg px-4 py-3 flex gap-2 items-center'
                href='{{ route('socialment.redirect.panel', ['panelId' => $panelId, 'provider' => $providerName]) }}'>
                <x-icon name="{{ $provider['icon'] }}" class='w-8' />
                <span>{{ $provider['label'] }}</span>
            </a>
        @endforeach
    </div>
</section>
