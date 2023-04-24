<x-filament::page :widget-data="['record' => $record]">
    <table style="border-collapse: separate; border-spacing: 5px 1rem;">
        @php $antall = 1; @endphp
        @foreach($okter['data'] as $d => $e)
            <tr>
                <th class="bg-gray-500 border text-left px-6 py-4">
                    {{$e['day']}}
                </th>

                @foreach($e['exercises'] as $data)
                    <td class="border px-6 py-4 " style="background-color: {{$data['intensity']}}">
                        <span class="font-bold"> {{$data['exercise']}}</span>
                        <br>
                        {{$data['from']}} - {{$data['to']}}
                    </td>
                    @if($loop->count < $antall && $loop->last)

                        @php $remaining = $antall - $loop->count; @endphp

                        @for($i = 1; $i <= $remaining; $i++)
                            <td class="border px-6 py-4">&nbsp;</td>
                        @endfor

                    @endif

                    @if($loop->count > $antall)
                        @php $antall = $loop->count @endphp
                    @endif
                @endforeach
            </tr>

        @endforeach
    </table>

</x-filament::page>