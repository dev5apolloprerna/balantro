<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profile;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileDocumentController extends Controller
{
    public function __invoke(int $user, string $type): BinaryFileResponse
    {
        $profile = Profile::query()->where('user_id', $user)->firstOrFail();
        $column = $type === 'pan' ? 'pan_card_file' : 'gst_certificate_file';
        $relativePath = $profile->{$column};

        abort_unless($relativePath, 404);

        $path = public_path($relativePath);

        abort_unless(is_file($path), 404);

        return response()->file($path);
    }
}