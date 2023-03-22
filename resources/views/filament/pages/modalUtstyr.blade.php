<div class="m-2 p-1">
    <ol>
        @foreach($records as $r)
            <li><span class="text-gray-400">{{$r->navn}}:</span> {{$r->antall}} stk.</li>
        @endforeach
    </ol>
</div>