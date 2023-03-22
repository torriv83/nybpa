<div>
    Hei, <br><br>


    Får du bestilt dette:
    <ul>
        @foreach($utstyr as $u)
            <li>{{$u->navn}}, @if($u->artikkelnummer != 0 || is_null($u->artikkelnummer))
                            Art.nr: {{$u->artikkelnummer}},
                @endif  {{$u->antall}} stk.
            </li>
        @endforeach
    </ul>

    @if (!empty($data['info']))
        <br>

        {{ $data['info'] }}<br><br>
    @endif


    Mvh <br>
    Tor J. Rivera
</div>