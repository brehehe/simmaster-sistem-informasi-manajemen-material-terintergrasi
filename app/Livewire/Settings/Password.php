<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Password extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
            ], [
                'current_password.required' => 'Password saat ini wajib diisi.',
                'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
                'password.required' => 'Password baru wajib diisi.',
                'password.min' => 'Password baru minimal harus 8 karakter.',
                'password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        session()->flash('password_status', 'Password akun Anda berhasil diperbarui!');
        $this->dispatch('password-updated');
    }

    public function render()
    {
        return view('livewire.settings.password')->layout('components.layouts.main.app', [
            'title' => 'Ganti Password Akun',
        ]);
    }
}
