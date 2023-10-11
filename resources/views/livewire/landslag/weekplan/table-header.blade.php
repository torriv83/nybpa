<div>
    <thead>
    <tr>
        <th class="py-2 px-4 border-b dark:border-gray-700 rounded-l-md w-4" style="border-right: 1px solid #52525B;">Tid</th>
        @foreach($dager as $dag)
            <th class="py-2 px-4 border-b dark:border-gray-700" style="{{ $this->getDayStyles($dag) }}">
                {{ $dag }}
            </th>
        @endforeach
    </tr>
    </thead>
</div>
