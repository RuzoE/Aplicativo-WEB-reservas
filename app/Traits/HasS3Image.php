<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasS3Image
{
    public function getImageUrlAttribute(): string
    {
        $imageField = property_exists($this, 'imageField')
            ? $this->imageField
            : 'image';

        $path = $this->{$imageField} ?? null;

        if (! $path) {
            return asset('images/no-image.png');
        }

        try {
            return Storage::disk('s3')->url($path);
        } catch (\Throwable $e) {
            return asset('images/no-image.png');
        }
    }
}
