<?php

namespace App\Livewire\Profile;

use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as JetstreamUpdateProfileInformationForm;

class UpdateProfileInformationForm extends JetstreamUpdateProfileInformationForm
{
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
