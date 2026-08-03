<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DocumentFileRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        URL::forceScheme('https');
    }

    public function test_document_file_route_is_registered_with_signature_protection(): void
    {
        $route = Route::getRoutes()->getByName('document-files.show');

        $this->assertNotNull($route);
        $this->assertSame('document-files/{document}/{filename}', $route->uri());
        $this->assertContains('signed', $route->gatherMiddleware());
    }

    public function test_document_file_url_contains_a_valid_signature(): void
    {
        $url = URL::signedRoute('document-files.show', [
            'document' => 123,
            'filename' => 'example.jpg',
        ]);

        $request = \Illuminate\Http\Request::create($url);

        $this->assertTrue(URL::hasValidSignature($request));
    }
}