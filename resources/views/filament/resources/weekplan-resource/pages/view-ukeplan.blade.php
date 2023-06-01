<x-filament::page :widget-data="['record' => $record]">
    
    <table class="w-full min-w-full bg-gray-800 text-white border border-collapse dark:border-gray-700">
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
        @php
            $startTime = 11; // Start time in hours (24-hour format)
            $endTime = 20; // End time in hours (24-hour format)
            $interval = 60; // Interval in minutes
        @endphp

        @for ($time = $startTime; $time <= $endTime; $time++)
            @for ($minute = 0; $minute < 60; $minute += $interval)
                <tr>
                    <td class="py-2 px-4 border-b dark:border-gray-600 border-r border-gray-500">
                        {{ sprintf('%02d', $time) }}:{{ sprintf('%02d', $minute) }}
                    </td>
                    {{-- Display time with 30-minute interval --}}
                    @foreach($okter as $index => $item)
                        @php
                            $exercises = collect($item['exercises'])->filter(function ($exercise) use ($time, $minute, $interval) {
                                $from = strtotime($exercise['from']);
                                $to = strtotime($exercise['to']);

                                $fromHour = date('H', $from);
                                $fromMinute = date('i', $from);
                                $toHour = date('H', $to);
                                $toMinute = date('i', $to);

                                // Calculate the number of time slots based on the from and to times
                                $fromTime = ($fromHour * 60) + $fromMinute;
                                $toTime = ($toHour * 60) + $toMinute;
                                $intervalSlots = max(($toTime - $fromTime) / $interval, 1); // Ensure minimum 1 slot

                                $currentTime = ($time * 60) + $minute;

                                return $currentTime >= $fromTime && $currentTime < ($fromTime + $intervalSlots * $interval);
                            });
                        @endphp

                        @if ($exercises->isEmpty())
                            <td class="py-2 px-4 border-b dark:border-gray-600 border-r border-gray-500">&nbsp;</td>
                        @else
                            @foreach($exercises as $exercise)
                                <td class="py-2 px-4 border-b dark:border-gray-600 border-r border-gray-500"
                                    style="{{ getIntensityColorClass($exercise['intensity']) }}">
                                    <div class="mb-1">{{ formatTime($exercise['from'], $exercise['to'], 'H:i') }}</div>
                                    <div>{{ $exercise['exercise'] }}</div>
                                </td>
                            @endforeach
                        @endif
                    @endforeach
                </tr>
            @endfor
        @endfor
        </tbody>
    </table>


</x-filament::page>