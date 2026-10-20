<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves profile pictures straight from the "public" disk.
 *
 * This is the fallback used when the public/storage symlink is missing (common on
 * Windows/IIS/shared hosting), so avatars keep rendering instead of showing a
 * broken image icon.
 */
class AvatarController extends Controller
{
    public function show(string $path): StreamedResponse
    {
        $path = ltrim(urldecode($path), '/');

        // Only avatar files may be served, and no path traversal.
        abort_unless(str_starts_with($path, 'avatars/') && ! str_contains($path, '..'), 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        return $disk->response($path, null, [
            'Cache-Control' => 'private, max-age=604800',
        ]);
    }
}
