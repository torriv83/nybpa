
<x-filament::dropdown
    teleport
    placement="bottom-end"
>
    <x-slot name="trigger">
        <button
            type="button"
            class="shrink-0 font-semibold rounded-full w-8 h-8 bg-white text-primary-500"
        >
{{--            <span class="shrink-0 font-semibold rounded-full w-5 h-5 bg-white text-primary-500">--}}
                {{ Str::of($currentPanel->getId())->substr(0, 2)->upper() }}
{{--            </span>--}}
{{--            <span class="text-white">
                {{ Str::of($currentPanel->getId())->substr(0, 1)->ucfirst() }}
            </span>--}}

{{--            <x-filament::icon
                icon="heroicon-m-chevron-down"
                icon-alias="panels::panel-switch-menu.toggle-button"
                class="ms-auto h-5 w-5 shrink-0 text-white"
            />--}}

        </button>
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($panels as $panel)
            <x-filament::dropdown.list.item
                :href="$canSwitchPanels && $panel->getId() !== $currentPanel->getId() ? config('app.url').'/'.$panel->getPath() : null"
                :badge="str($panel->getId())->substr(0, 2)->upper()"
                tag="a"
            >
                {{ str($panel->getId())->ucfirst() }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>

</x-filament::dropdown>
