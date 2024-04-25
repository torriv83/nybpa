<x-mail::message>
# Resepter går snart ut

@if(!$expiringPrescriptions->isEmpty())
### Disse reseptene går snart ut:

<x-mail::panel>
@foreach($expiringPrescriptions as $d)
1. {{$d['name']}} går ut: {{ \Carbon\Carbon::parse($d['validTo'])->format('d.m.Y') }}
@endforeach
</x-mail::panel>
@endif

@if(!$expiredPrescriptions->isEmpty())
### Disse reseptene har gått ut:

<x-mail::panel>
    @foreach($expiredPrescriptions as $prescription)
        1. {{ $prescription->name }} gikk ut: {{ \Carbon\Carbon::parse($prescription->validTo)->format('d.m.Y') }}
    @endforeach
</x-mail::panel>
@endif

Mvh.
Tor J. Rivera
</x-mail::message>
