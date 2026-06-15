<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

trait HandlesProfileImages
{
    protected function storeProfileImage(Request $request, string $directory): ?string
    {
        $image = $request->file('image');

        if (! $image instanceof UploadedFile) {
            return null;
        }

        $path = $image->store($directory, 'public');

        if ($path === false) {
            throw new RuntimeException('The profile image could not be stored.');
        }

        return $path;
    }

    protected function deleteProfileImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
