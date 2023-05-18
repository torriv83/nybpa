<tr style="border-bottom: 1px solid; border-color: rgb(55 65 81/var(--tw-divide-opacity))">
    <td class="px-4 py-3 font-bold">Totalt:</td>
    <td class="px-4 py-3 font-bold">{{ number_format($income / 1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format($activity / 1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format($budgeted / 1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format(($income + $activity) /1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format(($income - $budgeted) /1000, 2)}}</td>
</tr>
<tr style="border-top: none;">
    <td class="px-4 py-3 font-bold">Gjennomsnitt:</td>
    <td class="px-4 py-3 font-bold">{{ number_format($avgincome / 1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format($avgactivity / 1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format($avgbudgeted / 1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format(($avgincome + $avgactivity) /1000, 2)}}</td>
    <td class="px-4 py-3 font-bold">{{ number_format(($avgincome - $avgbudgeted) /1000, 2)}}</td>
</tr>