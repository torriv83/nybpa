<x-filament::page :widget-data="['record' => $record]">

    <div class="overflow-y-auto overflow-x-auto">
        <table class="w-full min-w-full dark:bg-gray-800 text-white dark:border-gray-700 rounded-2xl">
            <thead>
            <tr>
                <th class="py-2 px-4 border-b border-r dark:border-gray-700 rounded-l-md w-4">Tid</th>
                {{-- Empty cell for spacing --}}
                @foreach($okter as $item)
                    @if($item['day'] != 'Søndag')
                        @php $border = 'border-r';@endphp
                    @else
                        @php $border = ''; @endphp
                    @endif
                    <th class="py-2 px-4 border-b dark:border-gray-700 {{$border}}">{{ $item['day'] }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($data as $row)
                @if(!$loop->last)
                    @php $borderB = 'border-b'; @endphp
                @else
                    @php $borderB = ''; @endphp
                @endif
                <tr>
                    {{--Display time with x-minute interval--}}
                    <td class="py-2 px-4 {{$borderB}} dark:border-gray-600 border-r border-gray-500">
                        {{ $row['time'] }}
                    </td>

                    @foreach($row['exercises'] as $exercise)
                        @if(!$loop->last)
                            @php $borderR = 'border-r'; @endphp
                        @else
                            @php $borderR = ''; @endphp
                        @endif
                        @if (!isset($exercise['intensity']))
                            <td class="py-2 px-4 {{$borderB}} dark:border-gray-600 {{$borderR}} border-gray-500">&nbsp;</td>
                        @else
                            <td class="py-2 px-4 {{$borderB}} dark:border-gray-600 {{$borderR}} border-gray-500"
                                style="{{ getIntensityColorClass($exercise['intensity']) }}">
                                <div class="mb-1">{{ $exercise['from'] }}</div>
                                <div>{{ $exercise['exercise'] }}</div>
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</x-filament::page>