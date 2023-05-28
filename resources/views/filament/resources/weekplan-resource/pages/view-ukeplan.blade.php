<x-filament::page :widget-data="['record' => $record]">

    <table class="min-w-full bg-gray-800 text-white border border-collapse dark:border-gray-700">
        <thead>
        <tr>
            @foreach($okter as $item)
                <th class="py-2 px-4 border-b border-r dark:border-gray-700">{{ $item['day'] }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @for ($i = 0; $i < count($okter[0]['exercises']); $i++)
            <tr>
                @foreach($okter as $item)
                    @if (isset($item['exercises'][$i]))
                        @php
                            $intensity = $item['exercises'][$i]['intensity'];
                            $bgColorStyle = getIntensityColorClass($intensity);
                            $from = $item['exercises'][$i]['from'];
                            $to = $item['exercises'][$i]['to'];
                            $timeFormat = formatTime($from, $to);
                        @endphp

                        <td class="py-2 px-4 border-b dark:border-gray-600 border-r border-gray-500" style="{{ $bgColorStyle }}">
                            <div class="mb-1">{{ $timeFormat }}</div>
                            <div>{{ $item['exercises'][$i]['exercise'] }}</div>
                        </td>
                    @else
                        <td class="py-2 px-4 border-b dark:border-gray-600 border-r border-gray-500">&nbsp;</td>
                    @endif
                @endforeach
            </tr>
        @endfor
        </tbody>
    </table>

</x-filament::page>