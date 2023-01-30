{{-- <tr class="border-t-2 border-indigo-500"> --}}
    <td class="px-4 py-3 font-bold">Totalt:</td>
    <td class="px-4 py-3 font-bold">{{ number_format($income / 1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format($activity / 1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format($budgeted / 1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format(($income + $activity) /1000, 2)}}</td>
    {{--
</tr> --}}