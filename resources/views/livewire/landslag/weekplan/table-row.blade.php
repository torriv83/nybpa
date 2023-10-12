<div>
    @php $skipRows = array_fill(1, 7, 0); @endphp

    @foreach($data as $row)
        <tr>
            <td class="h-16 pt-1 px-4 border-t dark:border-gray-600 border-gray-500" style="border-right: 1px solid #52525B; vertical-align: top;">
                {{ $row['time'] }}
            </td>

            @for($day = 1; $day <= 7; $day++)
                @if($skipRows[$day] > 0)
                    @php
                        $skipRows[$day]--;
                    @endphp
                @else
                    @livewire('landslag.weekplan.exercise-cell', [
                        'exercise' => $row['exercises'][$day] ?? null,
                        'exists' => &$exists,
                        'day' => $day,
                        'rowspan' => $row['exercises'][$day]['rowspan'] ?? 1
                    ])

                    @if(isset($row['exercises'][$day]['rowspan']) && $row['exercises'][$day]['rowspan'] > 1)
                        @php
                            $skipRows[$day] = $row['exercises'][$day]['rowspan'] - 1;
                        @endphp
                    @endif
                @endif
            @endfor
        </tr>
    @endforeach

</div>
