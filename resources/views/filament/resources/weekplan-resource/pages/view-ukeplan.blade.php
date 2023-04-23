<x-filament::page>
    <h2>
        {{$test['name']}}
    </h2>

    <table style="border-collapse: separate; border-spacing: 5px 1rem;">
        @php $antall = 1; @endphp
        @foreach($test['data'] as $d => $e)
            <tr>
                <th class="bg-gray-500 border text-left px-6 py-4">
                    {{$e['day']}}
                </th>

                @foreach($e['exercises'] as $data)
                    <td class="border px-6 py-4 " style="background-color: {{$data['intensity']}}">
                        {{$data['exercise']}}
                        <br>
                        {{$data['from']}} - {{$data['to']}}
                    </td>
                    @if($loop->count < $antall)
                        <td class="border px-6 py-4 ">&nbsp;</td>
                    @endif

                    @if($loop->count > $antall)
                        @php $antall = $loop->count @endphp
                    @endif
                @endforeach
            </tr>

        @endforeach
    </table>

</x-filament::page>