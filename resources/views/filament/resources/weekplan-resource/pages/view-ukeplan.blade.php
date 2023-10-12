<x-filament-panels::page>
    <div class="overflow-y-auto overflow-x-auto">
        <table class="w-full min-w-full dark:bg-gray-800 text-white dark:border-gray-700 rounded-xl">

            <livewire:landslag.weekplan.table-header />

            @livewire('landslag.weekplan.table-header')


            <tbody>
                <livewire:landslag.weekplan.table-row :weekplanId="$this->weekplan->id"/>
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
