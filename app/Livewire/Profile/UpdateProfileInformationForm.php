<?php

namespace App\Livewire\Profile;

use App\Models\Division;
use App\Models\Education;
use App\Models\JobLevel;
use Illuminate\Support\Facades\Schema;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as JetstreamUpdateProfileInformationForm;

class UpdateProfileInformationForm extends JetstreamUpdateProfileInformationForm
{
    public function mount()
    {
        parent::mount();

        // Check if job_level_id column exists and add it to state
        if (Schema::hasColumn('users', 'job_level_id')) {
            $this->state['job_level_id'] = $this->user->job_level_id;
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
