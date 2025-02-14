<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PersonalInfo;

class PendingApplicationsCount extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->count = PersonalInfo::query()
            ->where('status', 'pending')
            ->count();
    }

    protected $listeners = [
        'applicationStatusUpdated' => 'updateCount',
    ];

    public function render()
    {
        return view('livewire.pending-applications-count');
    }
} 