<x-filament-panels::page>

    <div class="overflow-y-auto overflow-x-auto bg-gray-800">
        <table class="w-1/2 table w-full border border-gray-600">
            <thead class="bg-gray-50 dark:bg-white/5 text-left border-b border-gray-600 text-gray-300">
            <tr>
                <th class="px-6 py-4">Øvelse</th>
                <th class="px-6 py-4">Reps</th>
                <th class="px-6 py-4">Set</th>
                <th class="px-6 py-4">Pause</th>
            </tr>
            </thead>
            <tbody class="dark:divide-white/5 divide-y divide-gray-600 text-left border border-gray-600 text-gray-300 dark:bg-gray-900">
            @foreach($data as $item)
                <tr class="hover:bg-gray-700 dark:hover:bg-white/5">
                    <td class="px-6 py-4">
                        {{ $item->exercise_name }}
                        <br>
                        <span class="text-sm text-gray-500">{{ $item->pivot->description }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $item->pivot->repetitions }}</td>
                    <td class="px-6 py-4">{{ $item->pivot->sets }}</td>
                    <td class="px-6 py-4">{{ Carbon\Carbon::parse($item->pivot->rest)->format('i:s') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
