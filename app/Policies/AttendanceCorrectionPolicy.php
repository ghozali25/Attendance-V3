<?php

namespace App\Policies;

use App\Models\AttendanceCorrection;
use App\Models\User;

class AttendanceCorrectionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function viewAdminAny(User $user): bool
    {
        return $user->can('accessAdminPanel');
    }

    public function view(User $user, AttendanceCorrection $correction): bool
    {
        return $correction->user_id === $user->id
            || $user->can('accessAdminPanel')
            || $this->managesUser($user, $correction);
    }

    public function create(User $user): bool
    {
        return $user->isUser;
    }

    public function approve(User $user, AttendanceCorrection $correction): bool
    {
        // Only admin and superadmin can approve
        if ($user->can('accessAdminPanel')) {
            return in_array($correction->status, [
                AttendanceCorrection::STATUS_PENDING,
                AttendanceCorrection::STATUS_PENDING_ADMIN,
            ], true);
        }

        return false;
    }

    public function reject(User $user, AttendanceCorrection $correction): bool
    {
        return $this->approve($user, $correction);
    }

    protected function managesUser(User $user, AttendanceCorrection $correction): bool
    {
        try {
            return $user->subordinates->pluck('id')->contains($correction->user_id);
        } catch (\Exception $e) {
            return false;
        }
    }
}
