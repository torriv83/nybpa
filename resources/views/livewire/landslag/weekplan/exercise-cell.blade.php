<div>
    @php
        $borderR = $day != 7 ? 'border-right: 1px solid #52525B;' : '';
    @endphp

    <td class="py-2 px-4 {{ $exercise !== null && isset($exercise['intensity']) ? '' : 'border-t' }} dark:border-gray-600 border-gray-500"
        style="{{ $exercise !== null && isset($exercise['intensity']) ? getIntensityColorClass($exercise['intensity']) : '' }}; {{$borderR}}"
        @if(isset($rowspan) && $rowspan > 1) rowspan="{{ $rowspan }}" @endif
    >

        @if ($exercise !== null && isset($exercise['intensity']) && !$isDuplicate)
                <div class="mb-1 font-bold">{{ $exercise['time'] }}</div>
                <div>{{ $exercise['exercise'] }}</div>
                @if($exercise['program'])
                    <div class="text-xs hover:underline">
                        @livewire('landslag.weekplan.exercise-modal', ['programId' => $exercise['program_id'], 'programName' => $exercise['program']])
                    </div>
                @endif
        @endif
    </td>
</div>
