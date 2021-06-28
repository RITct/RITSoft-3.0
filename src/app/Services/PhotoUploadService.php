<?php

namespace App\Services;

class PhotoUploadService
{
    /**
     * Return the url of uploaded image
     * @return string
     */
    public function handleUploadedImage($image): string
    {
        $localDir = "profile_photos";
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path($localDir), $imageName);
        return asset($localDir . "/" . $imageName);
    }
}
