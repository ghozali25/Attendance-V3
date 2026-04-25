<?php

namespace App\Livewire\Profile;

use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as JetstreamUpdateProfileInformationForm;

class UpdateProfileInformationForm extends JetstreamUpdateProfileInformationForm
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [
        'name' => '',
        'nip' => '',
        'email' => '',
        'phone' => '',
        'gender' => '',
        'address' => '',
        'provinsi_kode' => '',
        'kabupaten_kode' => '',
        'kecamatan_kode' => '',
        'kelurahan_kode' => '',
        'birth_date' => '',
        'birth_place' => '',
        'education_id' => null,
        'division_id' => null,
        'job_level_id' => null,
    ];

    /**
     * Get the user's profile information.
     *
     * @return array
     */
    protected function getUserProfileInformation(): array
    {
        $user = $this->user;

        return [
            'name' => $user->name,
            'nip' => $user->nip,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'address' => $user->address,
            'provinsi_kode' => $user->provinsi_kode,
            'kabupaten_kode' => $user->kabupaten_kode,
            'kecamatan_kode' => $user->kecamatan_kode,
            'kelurahan_kode' => $user->kelurahan_kode,
            'birth_date' => $user->birth_date?->format('Y-m-d'),
            'birth_place' => $user->birth_place,
            'education_id' => $user->education_id,
            'division_id' => $user->division_id,
            'job_level_id' => $user->job_level_id,
        ];
    }
}
