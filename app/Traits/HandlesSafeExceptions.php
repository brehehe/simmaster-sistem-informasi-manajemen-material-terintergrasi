<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;

trait HandlesSafeExceptions
{
    /**
     * Safely log the exception with context and flash a safe error message to the session.
     * Prevents internal SQL, database schema, and server stack traces from leaking to end users.
     */
    protected function handleSafeException(
        Throwable $e,
        string $fallbackMessage = 'Terjadi kesalahan pada sistem saat memproses permintaan. Silakan coba beberapa saat lagi.'
    ): void {
        Log::error($e->getMessage(), [
            'exception' => $e,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
        ]);

        if (config('app.debug')) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return;
        }

        // In production, allow safe domain/business RuntimeExceptions (without SQL or connection details)
        if ($e instanceof \RuntimeException && ! str_contains($e->getMessage(), 'SQL') && ! str_contains($e->getMessage(), 'Connection')) {
            session()->flash('error', $e->getMessage());
            return;
        }

        session()->flash('error', $fallbackMessage);
    }
}
