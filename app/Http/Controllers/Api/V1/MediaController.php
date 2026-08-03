<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Document;
use App\Models\MessageAttachment;
use App\Models\Profile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function dashboardIcon(string $filename): BinaryFileResponse
    {
        return $this->fileWithin(public_path('assets/images'), $filename);
    }

    public function profileImage(Profile $profile, string $filename): BinaryFileResponse
    {
        abort_unless(
            $profile->profile_image && hash_equals(basename($profile->profile_image), $filename),
            404
        );

        $relativePath = preg_replace('#^profiles[/\\\\]+#', '', $profile->profile_image);

        return $this->fileWithin(public_path('profiles'), $relativePath);
    }

    public function messageAttachment(MessageAttachment $attachment, string $filename): BinaryFileResponse
    {
        abort_unless(hash_equals($attachment->file_name, $filename), 404);

        return $this->fileWithin(
            public_path('chat'),
            $attachment->message_id.DIRECTORY_SEPARATOR.$attachment->file_name
        );
    }

    public function document(Document $document, string $filename): BinaryFileResponse
    {
        $latestFile = $document->files()->latest('created_at')->first();
        $storedPath = $latestFile?->path ?: $document->file;

        abort_unless($storedPath && hash_equals(basename($storedPath), $filename), 404);

        $relativePath = preg_replace('#^documents[/\\\\]+#', '', $storedPath);

        return $this->fileWithin(public_path('documents'), $relativePath);
    }

    private function fileWithin(string $root, string $relativePath): BinaryFileResponse
    {
        $realRoot = realpath($root);
        $realPath = realpath($root.DIRECTORY_SEPARATOR.ltrim($relativePath, '/\\'));

        abort_unless(
            $realRoot
                && $realPath
                && is_file($realPath)
                && Str::startsWith($realPath, $realRoot.DIRECTORY_SEPARATOR),
            404
        );

        return response()->file($realPath);
    }
}