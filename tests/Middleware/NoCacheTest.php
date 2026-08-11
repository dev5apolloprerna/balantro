<?php

namespace Tests\Middleware;

use App\Http\Middleware\NoCache;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class NoCacheTest extends TestCase
{
    public function test_it_prevents_the_browser_from_caching_a_response(): void
    {
        $middleware = new NoCache;
        $request = Request::create('/dashboard');

        $response = $middleware->handle($request, fn () => new Response('protected content'));

        $cacheControl = $response->headers->get('Cache-Control');

        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
        $this->assertSame('no-cache', $response->headers->get('Pragma'));
        $this->assertSame('Sat, 01 Jan 1990 00:00:00 GMT', $response->headers->get('Expires'));
    }
}