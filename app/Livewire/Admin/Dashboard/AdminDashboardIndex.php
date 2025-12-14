<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

class AdminDashboardIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard.admin-dashboard-index')
            ->layout('components.layouts.main.app');
    }
}
