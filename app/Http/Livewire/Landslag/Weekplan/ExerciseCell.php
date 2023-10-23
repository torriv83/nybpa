<?php

namespace App\Http\Livewire\Landslag\Weekplan;

use Illuminate\Support\Carbon;
use Livewire\Component;

class ExerciseCell extends Component
{

    public static $exists  = [];
    public        $exercise;
    public        $day;
    public        $rowspan = 1;

    public function render()
    {
        $isDuplicate = false;

        if ($this->exercise !== null && isset($this->exercise['day']) && isset($this->exercise['exercise'])) {
            $exerciseId  = $this->exercise['day'].'-'.$this->exercise['exercise'];
            $isDuplicate = in_array($exerciseId, self::$exists);

            if (!$isDuplicate) {
                self::$exists[] = $exerciseId;
            }
        }

        return view('livewire.landslag.weekplan.exercise-cell', ['isDuplicate' => $isDuplicate, 'rowspan' => $this->rowspan]);
    }

    public function isToday(): string
    {
        $isToday = $this->day == Carbon::now()->dayOfWeekIso;

        return $isToday ? 'bg-slate-800' : '';
    }
}
