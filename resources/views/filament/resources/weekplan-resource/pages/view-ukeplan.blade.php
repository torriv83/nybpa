<x-filament::page :widget-data="['record' => $record]">
    {{--    <h1>{{$okter['name']}}</h1>--}}

    <table class="min-w-full bg-gray-800 text-white border border-collapse dark:border-gray-700">
        <thead>
        <tr>
            @foreach($okter as $item)
                <th class="py-2 px-4 border-b border-r dark:border-gray-600">{{ $item['day'] }}</th>
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

                            switch ($intensity) {
                                case 'crimson':
                                    $bgColorStyle = 'background-color: #DC2626';
                                    break;
                                case 'darkcyan':
                                    $bgColorStyle = 'background-color: #008B8B';
                                    break;
                                case 'green':
                                    $bgColorStyle = 'background-color: green';
                                    break;
                                // Add more cases for other intensities/colors if needed
                                default:
                                    $bgColorStyle = '';
                            }

                            $from = $item['exercises'][$i]['from'];
                            $to = $item['exercises'][$i]['to'];
                            $timeFormat = "{$from} - {$to}";
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