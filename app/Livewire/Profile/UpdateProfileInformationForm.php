<?php

namespace App\Livewire\Profile;

use App\Models\Division;
use App\Models\Education;
use App\Models\JobLevel;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as JetstreamUpdateProfileInformationForm;

class UpdateProfileInformationForm extends JetstreamUpdateProfileInformationForm
{
    public $divisions = [];
    public $educations = [];
    public $jobLevels = [];

    public function mount()
    {
        parent::mount();
        $this->divisions = Division::all()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->values()->toArray();
        $this->educations = Education::all()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->values()->toArray();
        $this->jobLevels = JobLevel::all()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->values()->toArray();

        // Force job_level_id into state
        $this->state['job_level_id'] = $this->user->job_level_id;
    }

    /**
     * Get the user's profile information.
     *
     * @return array
     */
    protected function getUserProfileInformation(): array
    {
        $user = $this->user;
        $parentInfo = parent::getUserProfileInformation();

        return array_merge($parentInfo, [
            'job_level_id' => $user->job_level_id,
        ]);
    }
}
