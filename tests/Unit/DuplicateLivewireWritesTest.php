<?php

use App\Http\Middleware\PreventDuplicateLivewireWrites;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;

uses(Tests\TestCase::class);

function writeRequest(string $method = 'save', int $quantity = 0): Request
{
    $request = Request::create('/livewire/update', 'POST', ['components' => [[
        'snapshot' => '{"checksum":"same-form"}',
        'updates' => ['quantity' => $quantity],
        'calls' => [['method' => $method, 'params' => [], 'path' => '']],
    ]]]);
    $session = new Store('test', new ArraySessionHandler(120));
    $session->setId(str_repeat('a', 40));
    $request->setLaravelSession($session);
    $request->setUserResolver(fn () => (new User)->forceFill(['id' => 1]));
    return $request;
}

it('executes three identical saves only once', function () {
    $count = 0;
    $next = function () use (&$count) { $count++; return response()->json(['components' => []]); };
    $middleware = new PreventDuplicateLivewireWrites;
    for ($i = 0; $i < 3; $i++) {
        expect($middleware->handle(writeRequest(), $next)->getStatusCode())->toBe(200);
    }
    expect($count)->toBe(1);
    $middleware->handle(writeRequest(quantity: 1), $next);
    expect($count)->toBe(2);
});

it('allows a retry after validation errors', function () {
    $count = 0;
    $next = function () use (&$count) {
        $count++;
        return response()->json(['components' => [['snapshot' => json_encode(['memo' => ['errors' => ['quantity' => ['Required']]]])]]]);
    };
    $middleware = new PreventDuplicateLivewireWrites;
    $middleware->handle(writeRequest(), $next);
    $middleware->handle(writeRequest(), $next);
    expect($count)->toBe(2);
});

it('does not cache navigation and other read calls', function () {
    $count = 0;
    $next = function () use (&$count) { $count++; return response()->json(['components' => []]); };
    $middleware = new PreventDuplicateLivewireWrites;
    $middleware->handle(writeRequest('gotoPage'), $next);
    $middleware->handle(writeRequest('gotoPage'), $next);
    expect($count)->toBe(2);
});
