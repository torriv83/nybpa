<?php

namespace App\Http\Livewire\Landslag\Weekplan;

use App\Models\TrainingProgram;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ExerciseModal extends Component
{
    public $programId;

    public $programName;

    public $programDetails;

    public function mount($programId = null, $programName = null): void
    {
        $this->programId = $programId;
        $this->programName = $programName;
        $this->programDetails = $this->getProgram($programId);
    }

    public function render(): View
    {
        return view('livewire.landslag.weekplan.exercise-modal');
    }

    public function getProgram($programId): Collection
    {
        return TrainingProgram::where('id', $programId)->with('WorkoutExercises')->get();
    }
}
