<x-app-layout>
    <x-slot name="header">
        @include('vendor.filament-authentication.components.banner')
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="flex py-12">
        <div class="w-1/3 mx-auto sm:px-6 lg:px-4">
            <div class="h-40 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold">Dine neste arbeidstider er:</h3>
                    @foreach ($nesteArbeidstid as $arbeid)
                    {{ $arbeid->fra_dato->format('d.m.Y') }}<br>
                    {{ $arbeid->fra_dato->format('H:i') }} - {{ $arbeid->til_dato->format('H:i') }}
                    @endforeach
                    {{-- {{ $nesteArbeidstid }} - {{ $nesteArbeidstid }} --}}
                </div>
            </div>
        </div>
        <div class="w-1/3 mx-auto sm:px-6 lg:px-4">
            <div class="h-40 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }} {{ Auth::user()->name }}
                    {{ $timerJobbet }}
                </div>
            </div>
        </div>
        <div class="w-1/3 mx-auto sm:px-6 lg:px-4">
            <div class="h-40 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }} {{ Auth::user()->name }}
                    {{ $timerJobbet }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>