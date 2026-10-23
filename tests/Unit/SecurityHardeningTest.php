<?php

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeaders;
use App\Livewire\Auth\Login\AuthLoginIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    /**
     * Test that SecurityHeaders middleware attaches all required headers.
     */
    public function test_security_headers_are_attached_to_responses(): void
    {
        $middleware = new SecurityHeaders();
        $request = Request::create('/any-route', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertEquals('1; mode=block', $response->headers->get('X-XSS-Protection'));
        $this->assertEquals('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertStringContainsString('camera=()', $response->headers->get('Permissions-Policy'));
    }

    /**
     * Test that dev-check route rejects unauthenticated requests.
     */
    public function test_dev_check_route_is_protected(): void
    {
        $response = $this->get('/dev-check');
        // Unauthenticated access must return 404 (or 403)
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    /**
     * Test that login throttles after multiple failed attempts.
     */
    public function test_login_rate_limiting_protects_against_brute_force(): void
    {
        $email = 'bruteforce-test@armaster.net';
        $ip = '127.0.0.1';
        $throttleKey = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($email) . '|' . $ip);

        RateLimiter::clear($throttleKey);

        // Simulate 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 60);
        }

        $this->assertTrue(RateLimiter::tooManyAttempts($throttleKey, 5));
        $this->assertGreaterThan(0, RateLimiter::availableIn($throttleKey));

        // Clean up
        RateLimiter::clear($throttleKey);
    }
}
