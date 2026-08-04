<?php

namespace App\Support;

use Illuminate\Support\Facades\URL;

class ProfileImageUrl
{
    public static function for(string $path): string
    {
        return URL::signedRoute('media.profile-image', ['path' => $path]);
    }
}