<div>
    @php
        $borderR = $day != 7 ? 'border-right: 1px solid #52525B;' : '';
    @endphp

    <td class="py-2 px-4 {{ $exercise !== null && isset($exercise['intensity']) ? '' : 'border-t' }} dark:border-gray-600 border-gray-500"
        style="{{ $exercise !== null && isset($exercise['intensity']) ? getIntensityColorClass($exercise['intensity']) : '' }}; {{$borderR}}"
    >

        @if ($exercise !== null && isset($exercise['intensity']))
            @if(!$isDuplicate)
                <div class="mb-1 font-bold">{{ $exercise['time'] }}</div>
                <div>{{ $exercise['exercise'] }}</div>
                @if($exercise['program'])
                    <div class="text-xs hover:underline">
                        <a href="/landslag/training-programs/{{$exercise['program_id']}}">({{ $exercise['program'] }})</a>
                    </div>
                @endif
            @endif
        @else
            &nbsp;
        @endif
    </td>
</div>
