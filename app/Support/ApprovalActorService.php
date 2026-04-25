<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class ApprovalActorService
{
    /**
     * @return Collection<int, string>
     */
    public function subordinateIds(User $user): Collection
    {
        try {
            return $user->subordinates->pluck('id');
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function hasSubordinates(User $user): bool
    {
        return $this->subordinateIds($user)->isNotEmpty();
    }

    public function canFinalizeFinanceApproval(User $user): bool
    {
        return $user->isAdmin
            || $user->isSuperadmin
            || $this->isFinanceHead($user);
    }

    public function isFinanceHead(User $user): bool
    {
        return (int) ($user->jobLevel?->rank ?? 99) <= 2
            && strtolower((string) $user->division?->name) === 'finance';
    }

    public function canManageDivisionSubordinates(User $user): bool
    {
        return (int) ($user->jobLevel?->rank ?? 99) <= 2;
    }
}
