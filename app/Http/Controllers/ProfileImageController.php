<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileImageController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $path = str_replace('\\', '/', (string) $request->query('path'));

        abort_unless(Str::startsWith($path, 'profiles/'), 404);

        $root = realpath(public_path('profiles'));
        $file = realpath(public_path($path));

        abort_unless(
            $root
                && $file
                && is_file($file)
                && Str::startsWith($file, $root.DIRECTORY_SEPARATOR),
            404
        );

        return response()->file($file);
    }
}