<div>
    Hei, <br><br>


    Får du bestilt dette:
    <ul>
        @foreach($utstyr as $u)
        <li>{{$u->navn}}, Art.nr: {{$u->artikkelnummer}}, {{$u->antall}} stk. </li>
        @endforeach
    </ul>

    @if (!empty($data['info']))
    <br>

    {{ $data['info'] }}<br><br>
    @endif


    Mvh <br>
    Tor J. Rivera
</div>