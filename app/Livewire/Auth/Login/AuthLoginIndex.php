<?php

namespace App\Livewire\Auth\Login;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.auth')]
class AuthLoginIndex extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function mount() {
        if (Auth::check()) {
            return $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return $this->redirect(route('dashboard'), navigate: true);
        }

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login.auth-login-index');
    }
}
