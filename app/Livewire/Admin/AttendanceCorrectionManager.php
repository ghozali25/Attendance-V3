<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceCorrection;
use App\Support\AttendanceCorrectionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AttendanceCorrectionManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    protected AttendanceCorrectionService $correctionService;

    public string $statusFilter = 'pending_admin';
    public string $typeFilter = 'all';
    public string $search = '';
    public ?int $selectedId = null;
    public string $rejectionNote = '';
    public bool $confirmingRejection = false;

    public function boot(AttendanceCorrectionService $correctionService): void
    {
        $this->correctionService = $correctionService;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        try {
            $correction = AttendanceCorrection::findOrFail($id);
            $this->authorize('approve', $correction);

            session()->flash('success', $this->correctionService->approve($correction, auth()->user()));
        } catch (\Exception $e) {
            session()->flash('error', __('Failed to approve attendance correction: ') . $e->getMessage());
            \Log::error('Livewire approve failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function confirmReject(int $id): void
    {
        $this->selectedId = $id;
        $this->rejectionNote = '';
        $this->confirmingRejection = true;
    }

    public function reject(): void
    {
        try {
            if (! $this->selectedId) {
                return;
            }

            $correction = AttendanceCorrection::findOrFail($this->selectedId);
            $this->authorize('reject', $correction);

            session()->flash('success', $this->correctionService->reject($correction, auth()->user(), $this->rejectionNote ?: null));

            $this->confirmingRejection = false;
            $this->selectedId = null;
            $this->rejectionNote = '';
        } catch (\Exception $e) {
            session()->flash('error', __('Failed to reject attendance correction: ') . $e->getMessage());
            \Log::error('Livewire reject failed', [
                'id' => $this->selectedId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function cancelReject(): void
    {
        $this->confirmingRejection = false;
        $this->selectedId = null;
        $this->rejectionNote = '';
    }

    public function render()
    {
        try {
            $this->authorize('viewAdminAny', AttendanceCorrection::class);

            $corrections = $this->correctionService
                ->managementQuery(auth()->user(), $this->statusFilter, $this->typeFilter, $this->search)
                ->paginate(12);

            return view('livewire.admin.attendance-correction-manager', [
                'corrections' => $corrections,
                'requestTypes' => AttendanceCorrection::requestTypes(),
            ]);
        } catch (\Exception $e) {
            session()->flash('error', __('Failed to load attendance corrections: ') . $e->getMessage());
            \Log::error('Livewire render failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return view('livewire.admin.attendance-correction-manager', [
                'corrections' => collect(),
                'requestTypes' => AttendanceCorrection::requestTypes(),
            ]);
        }
    }
}
