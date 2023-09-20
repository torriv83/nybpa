<x-mail::message>
# Resepter går snart ut
Disse reseptene går snart ut:

<x-mail::panel>
@foreach($details as $d)
1. {{$d['name']}} går ut: {{ \Carbon\Carbon::parse($d['validTo'])->format('d.m.Y') }}
@endforeach
</x-mail::panel>

Mvh.
Tor J. Rivera
</x-mail::message>
