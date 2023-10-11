<?php

namespace App\Http\Livewire\Landslag\Weekplan;

use Livewire\Component;

class TableHeader extends Component
{

    public $dager = [];

    public function mount()
    {
        $this->dager = $this->getDays();
    }

    public function render()
    {
        return view('livewire.landslag.weekplan.table-header');
    }

    public function getDays(): array
    {
        return [
            1 => 'Mandag',
            2 => 'Tirsdag',
            3 => 'Onsdag',
            4 => 'Torsdag',
            5 => 'Fredag',
            6 => 'Lørdag',
            7 => 'Søndag',
        ];
    }

    public function getDayStyles($day): string
    {
        $border = $day != 'Søndag' ? 'border-right: 1px solid #52525B;' : '';
        $today = $day == ucfirst(\Illuminate\Support\Carbon::today()->isoFormat('dddd')) ? 'background-color: grey;' : '';
        return $border . ' ' . $today;
    }
}
