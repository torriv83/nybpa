<div>
    Hei, <br><br>


    Får du bestilt dette:
    <ol>
        @foreach($utstyr as $u)
        <li>{{$u->navn}}, Art.nr: {{$u->artikkelnummer}}, {{$u->antall}} stk. </li>
        @endforeach
    </ol>

    <br>

    {{ $data['info'] }}<br><br>

    Mvh <br>
    Tor J. Rivera
</div>