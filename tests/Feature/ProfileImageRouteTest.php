<?php

namespace Tests\Feature;

use App\Support\ProfileImageUrl;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProfileImageRouteTest extends TestCase
{
    private string $profileDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profileDirectory = public_path('profiles/test-profile-image-route');
        File::ensureDirectoryExists($this->profileDirectory);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->profileDirectory);

        parent::tearDown();
    }

    public function test_a_signed_url_streams_a_profile_image_through_laravel(): void
    {
        File::put($this->profileDirectory.'/avatar.png', 'image contents');

        $response = $this->get(ProfileImageUrl::for('profiles/test-profile-image-route/avatar.png'));

        $response->assertOk();
        $this->assertSame('image contents', $response->baseResponse->getFile()->getContent());
    }

    public function test_an_unsigned_profile_image_request_is_rejected(): void
    {
        $this->get('/media/profile-image?path=profiles/avatar.png')->assertForbidden();
    }

    public function test_a_signed_url_cannot_read_outside_the_profiles_directory(): void
    {
        $url = URL::signedRoute('media.profile-image', ['path' => 'profiles/../index.php']);

        $this->get($url)->assertNotFound();
    }
}