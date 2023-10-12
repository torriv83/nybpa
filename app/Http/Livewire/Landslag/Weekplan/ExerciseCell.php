<?php

namespace App\Http\Livewire\Landslag\Weekplan;

use Livewire\Component;

class ExerciseCell extends Component
{

    public static $exists = [];
    public        $exercise;
    public        $day;

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

        return view('livewire.landslag.weekplan.exercise-cell', ['isDuplicate' => $isDuplicate]);
    }
}
