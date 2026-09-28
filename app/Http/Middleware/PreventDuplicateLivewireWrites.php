<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PreventDuplicateLivewireWrites
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->isMethod('POST') || ! $request->is('livewire/update') || ! $request->user()) {
            return $next($request);
        }

        $writes = collect($request->input('components', []))->flatMap(fn ($component) => $component['calls'] ?? [])
            ->contains(fn ($call) => preg_match('/^(save|update|delete|approve|confirm|submit|ship|process|send|mark|toggle|enable|disable|regenerate|resetPassword)/', $call['method'] ?? ''));
        if (! $writes) {
            return $next($request);
        }

        // The signed snapshot distinguishes a new form/action from a network retry.
        $key = 'livewire-write:'.hash('sha256', $request->session()->getId().'|'.$request->user()->getAuthIdentifier().'|'.json_encode($request->input('components')));
        $cache = Cache::store(config('cache.default'));

        try {
            return $cache->lock($key.':lock', 180)->block(10, function () use ($cache, $key, $request, $next) {
                if ($content = $cache->get($key)) {
                    return response($content, 200, ['Content-Type' => 'application/json']);
                }

                $response = $next($request);
                if ($response->isSuccessful() && str_contains($response->headers->get('Content-Type', ''), 'application/json')) {
                    $payload = json_decode($response->getContent(), true);
                    $hasErrors = collect($payload['components'] ?? [])->contains(function ($component) {
                        $snapshot = json_decode($component['snapshot'] ?? '{}', true);
                        return ! empty($snapshot['memo']['errors']);
                    });
                    if (! $hasErrors && ! $request->session()->has('error')) {
                        $cache->put($key, $response->getContent(), now()->addDay());
                    }
                }

                return $response;
            });
        } catch (LockTimeoutException) {
            return response()->json(['message' => 'Penyimpanan masih diproses. Tunggu hingga selesai.'], 409);
        }
    }
}
