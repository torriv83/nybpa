<div>
    Hei, <br><br>


    Får du bestilt dette:
    <ul>
        @foreach($utstyr as $u)
        <li>{{$u->navn}}, Art.nr: {{$u->artikkelnummer}}, {{$u->antall}} stk. </li>
        @endforeach
    </ul>

    <br>

    {{ $data['info'] }}<br><br>

    Mvh <br>
    Tor J. Rivera
</div>