<?php

namespace App\Http\Livewire\Landslag\Weekplan;

use Illuminate\Support\Carbon;
use Livewire\Component;

class TableHeader extends Component
{

    public $dager = [
        1 => 'Mandag',
        2 => 'Tirsdag',
        3 => 'Onsdag',
        4 => 'Torsdag',
        5 => 'Fredag',
        6 => 'Lørdag',
        7 => 'Søndag',
    ];

    public function getDayStyles($day): array
    {
        $data['border'] = $day != 'Søndag' ? 'border-right: 1px solid #52525B;' : '';
        $data['today']  = $day == ucfirst(Carbon::today()->isoFormat('dddd'));
        return $data;
    }
}
