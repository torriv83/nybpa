<x-filament-panels::page>
    <div class="overflow-y-auto overflow-x-auto">
        <table class="w-full min-w-full dark:bg-gray-800 text-white dark:border-gray-700 rounded-xl">
            <thead>
            <tr>
                <th class="py-2 px-4 border-b dark:border-gray-700 rounded-l-md w-4" style="border-right: 1px solid #52525B;">Tid</th>
                @foreach($okter as $item)
                    @if($item['day'] != 'Søndag')
                        @php $border = 'border-right: 1px solid #52525B;';@endphp
                    @else
                        @php $border = ''; @endphp
                    @endif

                    @php $today = ''; @endphp

                    @if($item['day'] == ucfirst(\Illuminate\Support\Carbon::today()->isoFormat('dddd')))
                        @php $today = 'background-color: grey;' @endphp
                    @endif

                    <th class="py-2 px-4 border-b dark:border-gray-700" style="{{$border}} {{$today}}">{{ $item['day'] }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @php
                $exists = [];
            @endphp

            @foreach($data as $row)

                <tr>
                    {{--Display time with x-minute interval--}}
                    <td class="py-2 px-4 border-t dark:border-gray-600 border-gray-500" style="border-right: 1px solid #52525B;">
                        {{ $row['time'] }}
                    </td>

                    @foreach($row['exercises'] as $exercise)

                        @php $borderR = !$loop->last ? 'border-right: 1px solid #52525B;' : ''; @endphp

                        @if (!isset($exercise['intensity']))
                            <td class="py-2 px-4 border-t dark:border-gray-600 border-gray-500" style="{{$borderR}}">
                                &nbsp;
                            </td>
                        @else
                            @php
                                $exerciseExists = collect($exists)->contains(function ($value) use ($exercise) {
                                    return $value['day'] === $exercise['day'] && $value['id'] === $exercise['id'] && $value['exercise'] === $exercise['exercise'];
                                });
                            @endphp

                            @if($exerciseExists)
                                <td class="py-2 px-4 dark:border-gray-600 border-gray-500"
                                    style="{{ getIntensityColorClass($exercise['intensity']) }}; {{$borderR}}">
                                    &nbsp;
                                </td>
                            @else
                                <td class="py-2 px-4 border-t dark:border-gray-600 border-gray-500"
                                    style="{{ getIntensityColorClass($exercise['intensity']) }}; {{$borderR}}">
                                    <div class="mb-1">{{ $exercise['time'] }}</div>
                                    <div>{{ $exercise['exercise'] }}</div>
                                </td>
                            @endif

                            @php
                                $exists[] = array('day' => $exercise['day'], 'id' => $exercise['id'], 'exercise' => $exercise['exercise']);
                            @endphp
                        @endif
                    @endforeach

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
