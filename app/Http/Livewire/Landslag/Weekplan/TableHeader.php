<?php

namespace App\Http\Livewire\Landslag\Weekplan;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class TableHeader extends Component
{
    /**
     * @var array<int, string>
     */
    public array $dager = [
        1 => 'Mandag',
        2 => 'Tirsdag',
        3 => 'Onsdag',
        4 => 'Torsdag',
        5 => 'Fredag',
        6 => 'Lørdag',
        7 => 'Søndag',
    ];

    /**
     * @return array<string, bool|string>
     */
    public function getDayStyles(string $day): array
    {
        $data['border'] = $day != 'Søndag' ? 'border-right: 1px solid #52525B;' : '';
        $data['today'] = $day == ucfirst(Carbon::today()->isoFormat('dddd'));

        return $data;
    }

    public function render(): View
    {
        return view('livewire.landslag.weekplan.table-header');
    }
}
