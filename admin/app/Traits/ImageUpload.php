<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait ImageUpload
{

    public function UserImageUpload($query, $path) // Taking input image as parameter
    {
        $ext = strtolower($query->getClientOriginalExtension());
        $image = time() . '.' . $ext;
        $success = $query->storeAs($path, $image, 'public');
        return $success; // Just return image
    }
    public function uploadOne(UploadedFile $uploadedFile, $folder, $disk = 'public', $filename)
    {
        $name = !is_null($filename) ? $filename : str_random(25);
        $file       =   $uploadedFile->storeAs($folder, $name, 'public');
        return $file;
    }
}
