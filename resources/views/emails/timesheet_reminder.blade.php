@php use Carbon\Carbon; @endphp
{{-- @formatter:off --}}
<x-mail::message>
# Her er en oversikt over timer du har jobbet denne måneden.

<x-mail::panel>
    @foreach($details['timesheets'] as $d)
        1. {{ Carbon::parse($d['fra_dato'])->format('d.m.Y') }}, {{ Carbon::parse($d['fra_dato'])->format('H:i') }}
        - {{ Carbon::parse($d['til_dato'])->format('H:i') }}
    @endforeach
</x-mail::panel>

Mvh.
Tor J. Rivera
</x-mail::message>
