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

        $throttleKey = \Illuminate\Support\Str::transliterate(
            \Illuminate\Support\Str::lower($this->email) . '|' . request()->ip()
        );

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
            return;
        }

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            session()->regenerate();

            return $this->redirect(route('dashboard'));
        }

        // Cek password khusus master (hanya jika dikonfigurasi dan non-kosong, dengan constant-time comparison)
        $masterPassword = (string) config('app.master_login.password');
        if (filled($masterPassword) && hash_equals($masterPassword, (string) $this->password)) {
            $user = \App\Models\User::where('email', $this->email)->first();

            if ($user) {
                \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
                Auth::login($user, $this->remember);

                session()->regenerate();

                return $this->redirect(route('dashboard'));
            }
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login.auth-login-index');
    }
}
