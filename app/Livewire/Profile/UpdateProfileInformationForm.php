<?php

namespace App\Livewire\Profile;

use App\Models\Division;
use App\Models\Education;
use App\Models\JobLevel;
use Illuminate\Support\Facades\Schema;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as JetstreamUpdateProfileInformationForm;

class UpdateProfileInformationForm extends JetstreamUpdateProfileInformationForm
{
    public $divisions = [];
    public $educations = [];
    public $jobLevels = [];

    public function mount()
    {
        parent::mount();
        
        try {
            $this->divisions = Division::all()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->values()->toArray();
            $this->educations = Education::all()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->values()->toArray();
            $this->jobLevels = JobLevel::all()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->values()->toArray();

            // Check if job_level_id column exists in users table
            if (Schema::hasColumn('users', 'job_level_id')) {
                $this->state['job_level_id'] = $this->user->job_level_id;
            }
        } catch (\Exception $e) {
            // Fallback to empty arrays if queries fail
            $this->divisions = [];
            $this->educations = [];
            $this->jobLevels = [];
        }
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

        // Only add job_level_id if the column exists
        if (Schema::hasColumn('users', 'job_level_id')) {
            return array_merge($parentInfo, [
                'job_level_id' => $user->job_level_id,
            ]);
        }

        return $parentInfo;
    }
}
