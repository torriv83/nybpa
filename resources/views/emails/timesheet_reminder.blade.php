<x-mail::message>
# Det er tid for å sende inn din timeliste!
Her er tidene som jeg har registrert:

<x-mail::panel>
@foreach($details['timesheets'] as $d)
1. {{ \Carbon\Carbon::parse($d['fra_dato'])->format('d.m.Y') }}, {{ \Carbon\Carbon::parse($d['fra_dato'])->format('H:i') }} - {{ \Carbon\Carbon::parse($d['til_dato'])->format('H:i') }}
@endforeach
</x-mail::panel>

Send meg gjerne en melding når du har sendt inn timelisten, så får jeg godkjent den med en gang!

Mvh.
Tor J. Rivera
</x-mail::message>
