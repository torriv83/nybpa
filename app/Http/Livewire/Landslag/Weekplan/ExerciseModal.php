<?php

namespace App\Http\Livewire\Landslag\Weekplan;

use App\Models\TrainingProgram;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ExerciseModal extends Component
{
    public ?int $programId;

    public ?string $programName;

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrainingProgram>|null
     */
    public ?Collection $programDetails;

    public function mount(?int $programId = null, ?string $programName = null): void
    {
        $this->programId = $programId;
        $this->programName = $programName;
        $this->programDetails = $this->getProgram($programId);
    }

    public function render(): View
    {
        return view('livewire.landslag.weekplan.exercise-modal');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrainingProgram>
     */
    public function getProgram(?int $programId): Collection
    {
        return TrainingProgram::where('id', $programId)->with('WorkoutExercises')->get();
    }
}
