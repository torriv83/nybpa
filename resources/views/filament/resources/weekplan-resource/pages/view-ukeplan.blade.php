<x-filament::page :widget-data="['record' => $record]">

    <div class="overflow-y-auto overflow-x-auto">
        <table class="w-full min-w-full dark:bg-gray-800 text-white border border-collapse dark:border-gray-700">
            <thead>
            <tr>
                <th class="py-2 px-4 border-b border-r dark:border-gray-700">Tid</th>
                {{-- Empty cell for spacing --}}
                @foreach($okter as $item)
                    <th class="py-2 px-4 border-b border-r dark:border-gray-700">{{ $item['day'] }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($data as $row)
                <tr>
                    {{--Display time with x-minute interval--}}
                    <td class="py-2 px-4 border-b dark:border-gray-600 border-r border-gray-500">
                        {{ $row['time'] }}
                    </td>

                    @foreach($row['exercises'] as $exercise)
                        @if (!isset($exercise['intensity']))
                            <td class="py-2 px-4 border-b dark:border-gray-600 border-r border-gray-500">&nbsp;</td>
                        @else
                            <td class="py-2 px-4 border-b dark:border-gray-600 border-r border-gray-500"
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