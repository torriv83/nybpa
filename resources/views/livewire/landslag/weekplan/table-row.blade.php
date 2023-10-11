<div>
    @foreach($data as $row)
        <tr>
            <td class="py-2 px-4 border-t dark:border-gray-600 border-gray-500" style="border-right: 1px solid #52525B;">
                {{ $row['time'] }}
            </td>

            @for($day = 1; $day <= 7; $day++)
                @livewire('landslag.weekplan.exercise-cell', ['exercise' => $row['exercises'][$day] ?? null, 'exists' => &$exists, 'day' => $day])
            @endfor
        </tr>
    @endforeach
</div>
