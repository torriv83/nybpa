<?php

namespace App\Http\Livewire\Landslag\Weekplan;

use App\Models\TrainingProgram;
use Livewire\Component;

class ExerciseModal extends Component
{
    public $programId;
    public $programName;
    public $programDetails;

    public function mount($programId = null, $programName = null)
    {
        $this->programId = $programId;
        $this->programName = $programName;
        $this->programDetails = $this->getProgram($programId);
        
    }

    public function render()
    {
        return view('livewire.landslag.weekplan.exercise-modal');
    }

    public function getProgram($programId)
    {
        return TrainingProgram::where('id', $programId)->with('WorkoutExercises')->get();
    }
}
