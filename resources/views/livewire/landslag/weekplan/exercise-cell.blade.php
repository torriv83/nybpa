<div>
    @php
        $borderR = $day != 7 ? 'border-right: 1px solid #52525B;' : '';
    @endphp

    <td class="text-center py-2 px-2 border-t dark:border-gray-600 border-gray-500
        {{$this->isToday()}}
        {{ $exercise !== null && isset($exercise['intensity']) ? getIntensityColorClass($exercise['intensity']) : '' }}"
        style="{{$borderR}}"
        @if(isset($rowspan) && $rowspan > 1) rowspan="{{ $rowspan }}" @endif
    >

        @if ($exercise !== null && isset($exercise['intensity']) && !$isDuplicate)
            <div class="{{ $this->isToday() ? getIntensityColorClass($exercise['intensity']) : '' }} py-3">
                <div class="mb-1 font-bold">{{ $exercise['time'] }}</div>
                <div>{{ $exercise['exercise'] }}</div>

                @if($exercise['program'])
                    <div class="text-xs hover:underline">
                        @livewire('landslag.weekplan.exercise-modal',
                            ['programId' => $exercise['program_id'],
                            'programName' => $exercise['program']]
                        )
                    </div>
                @endif
            </div>
        @endif
    </td>
</div>
