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
                    <td class="py-2 px-4 border-t dark:border-gray-600 border-gray-500" style="border-right: 1px solid #52525B;">
                        {{ $row['time'] }}
                    </td>

                    @for($day = 1; $day <= 7; $day++)
                        @php
                            $exercise = $row['exercises'][$day] ?? null;
                            $borderR = $day != 7 ? 'border-right: 1px solid #52525B;' : '';
                        @endphp
                        <td class="
                                py-2 px-4 {{ $exercise !== null && isset($exercise['intensity']) ? '' : 'border-t' }}
                                dark:border-gray-600 border-gray-500"
                            style="
                                {{ $exercise !== null && isset($exercise['intensity']) ? getIntensityColorClass($exercise['intensity']) : '' }};
                                {{$borderR}}
                            "
                        >
                            @if ($exercise !== null && isset($exercise['intensity']))
                                @php
                                    $exerciseExists = collect($exists)->contains(function ($value) use ($exercise) {
                                        return $value['day'] === $exercise['day'] && $value['exercise'] === $exercise['exercise'];
                                    });
                                @endphp

                                @if(!$exerciseExists)
                                    <div class="mb-1">{{ $exercise['time'] }}</div>
                                    <div>{{ $exercise['exercise'] }}</div>
                                    @if($exercise['program'])
                                        <div class="text-xs"><a href="/landslag/training-programs/{{$exercise['program_id']}}">({{ $exercise['program'] }})</a></div>
                                    @endif
                                @endif

                                @php
                                    $exists[] = array('day' => $exercise['day'], 'exercise' => $exercise['exercise']);
                                @endphp
                            @else
                                &nbsp;
                            @endif
                        </td>
                    @endfor
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
