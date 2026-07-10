<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\RedirectHttpToHttps;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RedirectHttpToHttpsTest extends TestCase
{
    public function test_it_permanently_redirects_http_requests_to_https(): void
    {
        $request = Request::create('http://example.com/reports?filter=open', 'GET');
        $middleware = new RedirectHttpToHttps();

        $response = $middleware->handle($request, fn () => new Response('ok'));

        $this->assertSame(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertSame('https://example.com/reports?filter=open', $response->headers->get('Location'));
    }

    public function test_it_allows_https_requests_to_continue(): void
    {
        $request = Request::create('https://example.com/reports', 'GET');
        $middleware = new RedirectHttpToHttps();

        $response = $middleware->handle($request, fn () => new Response('ok'));

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('ok', $response->getContent());
    }
}