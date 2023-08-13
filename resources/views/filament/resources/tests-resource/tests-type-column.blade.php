<div>
    @foreach($getRecord()->ovelser as $t)
        {{$t['navn']}}: {{$t['type']}},
    @endforeach
</div>
