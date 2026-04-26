<?php

namespace App\Support;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Shift;
use App\Models\User;
use App\Notifications\AttendanceCorrectionStatusUpdated;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceCorrectionService
{
    public function __construct(
        protected ApprovalActorService $approvalActors,
    ) {
    }

    public function submit(User $user, array $payload): AttendanceCorrection
    {
        $attendanceDate = Carbon::parse($payload['attendance_date'])->toDateString();
        $attendance = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('date', $attendanceDate)
            ->first();

        return AttendanceCorrection::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance?->id,
            'attendance_date' => $attendanceDate,
            'request_type' => $payload['request_type'],
            'requested_time_in' => $this->nullableDateTime($payload['requested_time_in'] ?? null),
            'requested_time_out' => $this->nullableDateTime($payload['requested_time_out'] ?? null),
            'requested_shift_id' => $payload['requested_shift_id'] ?: null,
            'current_snapshot' => $this->snapshot($attendance),
            'reason' => trim((string) $payload['reason']),
            'status' => AttendanceCorrection::STATUS_PENDING_ADMIN, // Always go to admin review
        ]);
    }

    public function managementQuery(User $actor, string $statusFilter = 'pending', string $typeFilter = 'all', string $search = ''): Builder
    {
        return AttendanceCorrection::query()
            ->with(['user', 'attendance.shift', 'requestedShift', 'headApprover', 'reviewer'])
            ->when(! $actor->can('accessAdminPanel'), function (Builder $query) use ($actor) {
                try {
                    $subordinateIds = $this->approvalActors->subordinateIds($actor);
                    if ($subordinateIds->isNotEmpty()) {
                        $query->whereIn('user_id', $subordinateIds)
                            ->where('status', AttendanceCorrection::STATUS_PENDING);
                    } else {
                        $query->where('id', 0); // Return no results if no subordinates
                    }
                } catch (\Exception $e) {
                    $query->where('id', 0); // Return no results on error
                }
            })
            ->when($statusFilter !== 'all', fn (Builder $query) => $query->where('status', $statusFilter))
            ->when($typeFilter !== 'all', fn (Builder $query) => $query->where('request_type', $typeFilter))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested->where('reason', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('nip', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByRaw("CASE WHEN status = 'pending_admin' THEN 0 WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderByDesc('attendance_date')
            ->orderByDesc('created_at');
    }

    public function approve(AttendanceCorrection $correction, User $actor): string
    {
        // Only admin/superadmin can approve
        if (! $actor->can('accessAdminPanel')) {
            throw new AuthorizationException;
        }

        try {
            DB::transaction(function () use ($correction, $actor) {
                // Simple update first to test
                $correction->update([
                    'status' => AttendanceCorrection::STATUS_APPROVED,
                    'reviewed_by' => $actor->id,
                    'reviewed_at' => now(),
                    'rejection_note' => null,
                ]);
            });

            return __('Attendance correction approved.');
        } catch (\Exception $e) {
            \Log::error('Attendance correction approval failed', [
                'correction_id' => $correction->id,
                'actor_id' => $actor->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function reject(AttendanceCorrection $correction, User $actor, ?string $note = null): string
    {
        // Only admin/superadmin can reject
        if (! $actor->can('accessAdminPanel')) {
            throw new AuthorizationException;
        }

        try {
            $correction->update([
                'status' => AttendanceCorrection::STATUS_REJECTED,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'rejection_note' => $note,
            ]);

            try {
                $this->notifyStatusUpdated($correction);
            } catch (\Exception $e) {
                // Ignore notification errors to not block rejection
            }

            return __('Attendance correction rejected.');
        } catch (\Exception $e) {
            \Log::error('Attendance correction rejection failed', [
                'correction_id' => $correction->id,
                'actor_id' => $actor->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function snapshot(?Attendance $attendance): ?array
    {
        if (! $attendance) {
            return null;
        }

        return [
            'status' => $attendance->status,
            'shift_id' => $attendance->shift_id,
            'shift_name' => $attendance->shift?->name,
            'time_in' => $attendance->time_in?->format('Y-m-d H:i:s'),
            'time_out' => $attendance->time_out?->format('Y-m-d H:i:s'),
        ];
    }

    private function nullableDateTime(mixed $value): ?Carbon
    {
        if (! filled($value)) {
            return null;
        }

        return Carbon::parse((string) $value);
    }

    private function resolvedStatus(?Carbon $timeIn, ?Shift $shift, int $gracePeriod, ?string $fallback): string
    {
        if (! $timeIn) {
            return $fallback ?: 'present';
        }

        if (! $shift) {
            return 'present';
        }

        $shiftStart = Carbon::parse($shift->start_time)->setDate($timeIn->year, $timeIn->month, $timeIn->day);

        return $timeIn->gt($shiftStart->copy()->addMinutes($gracePeriod)) ? 'late' : 'present';
    }

    private function needsSupervisorReview(User $user): bool
    {
        try {
            return (bool) optional($user->supervisor)->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function canSupervisorReview(AttendanceCorrection $correction, User $actor): bool
    {
        return $this->approvalActors->subordinateIds($actor)->contains($correction->user_id);
    }

    private function notifyStatusUpdated(AttendanceCorrection $correction): void
    {
        $correction->refresh()->loadMissing('user');
        $correction->user?->notify(new AttendanceCorrectionStatusUpdated($correction));
    }
}
