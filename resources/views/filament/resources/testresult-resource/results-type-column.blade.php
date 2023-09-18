<div>
    @foreach($getRecord()->resultat as $test)
        @foreach($test as $key => $value)
            {{ $key }}: {{ $value }},
        @endforeach
        <br>
    @endforeach
</div>
