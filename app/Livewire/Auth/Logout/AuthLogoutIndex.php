<?php

namespace App\Livewire\Auth\Logout;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AuthLogoutIndex extends Component
{
    public function render()
    {
        return view('livewire.auth.logout.auth-logout-index');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect('/login', navigate: true);
    }
}
