<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Url;
use Livewire\Component;

class Profile extends Component
{
    #[Url]
    public string $tab = 'profile';

    // Profile fields
    public string $name = '';
    public string $email = '';

    // Password fields
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';

        if (!in_array($this->tab, ['profile', 'security'])) {
            $this->tab = 'profile';
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['profile', 'security'])) {
            $this->tab = $tab;
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        session()->flash('profile_status', 'Informasi profil berhasil diperbarui.');
        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', PasswordRule::min(8), 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
        ]);

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        session()->flash('password_status', 'Password akun Anda berhasil diperbarui! Simpan dan gunakan password baru ini saat login.');
        $this->dispatch('password-updated');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function render()
    {
        $user = Auth::user()->load(['roles', 'policeStation', 'regionalPolice', 'userType']);
        $managedTypes = [];
        if ($user->userType && !empty($user->userType->types)) {
            $managedTypes = \App\Models\Type\Type::whereIn('id', $user->userType->types)->pluck('name')->toArray();
        }

        return view('livewire.settings.profile', [
            'user' => $user,
            'managedTypes' => $managedTypes,
        ])->layout('components.layouts.main.app', [
            'title' => 'Pengaturan Akun & Keamanan',
        ]);
    }
}
