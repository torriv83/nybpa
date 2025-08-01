@php use Carbon\Carbon; @endphp
<x-mail::message>
    {{-- @formatter:off --}}
@if(!$expiredPrescriptions->isEmpty())
# Disse reseptene har utløpt:

<x-mail::panel>
    <ol>
        @foreach ($expiredPrescriptions as $prescription)
            <li>{{ $prescription->name }} gikk ut: {{ Carbon::parse($prescription->validTo)->format('d.m.Y') }}</li>
        @endforeach
    </ol>
</x-mail::panel>
@endif

@if(!$expiringPrescriptions->isEmpty())
# Disse reseptene går snart ut:

<x-mail::panel>
    <ol>
        @foreach($expiringPrescriptions as $d)
            <li>{{ $d->name }} går ut: {{ Carbon::parse($d->validTo)->format('d.m.Y') }}</li>
        @endforeach
    </ol>
</x-mail::panel>
@endif

Mvh.<br>
Tor J. Rivera
</x-mail::message>
