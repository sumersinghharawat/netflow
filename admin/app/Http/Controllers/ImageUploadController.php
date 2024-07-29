<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\UploadImage;
use App\Traits\ImageUpload;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RequestsImageUpload;

class ImageUploadController extends CoreInfController
{
    use ImageUpload;

    public function store(RequestsImageUpload $request)
    {
        try {
            if ($request->has('inactive_tree_icon') || $request->has('active_tree_icon')) {
                if ($request->has('active_tree_icon')) {
                    $checkIcon = UploadImage::where('image_type', 'active_tree_icon')->exists();

                    $image = $request->file('active_tree_icon');
                    $name = time().'.'.$request->file('active_tree_icon')->extension();
                    // $folder                =           public_path('/uploads/member_status');
                    $folder = 'uploads/member_status/';
                    if($checkIcon) {
                        $uploadImage = UploadImage::where('image_type', 'active_tree_icon')->first();
                        Storage::delete($folder.$uploadImage->image);
                        $filePath = $folder.$name;
                        $this->uploadOne($image, $folder, 'public', $name);
                        $uploadImage->update([
                            'image' => $filePath,
                        ]);
                    } else {
                        $filePath = $folder.$name;
                        $this->uploadOne($image, $folder, 'public', $name);
                        $data = new UploadImage;
                        $image_type = 'active_tree_icon';
                        $data->imageable_type = 'App\Models\Configuration';
                        $data->imageable_id = $this->configuration()['id'];
                        $data->date = now();
                        $data->image_type = $image_type;
                        $data->image = $filePath;
                        $data->save();
                    }
                }
                if ($request->has('inactive_tree_icon')) {
                    $checkIcon = UploadImage::where('image_type', 'inactive_tree_icon')->exists();
                    $image = $request->file('inactive_tree_icon');
                    $name = time().'.'.$request->file('inactive_tree_icon')->extension();
                    $folder = 'uploads/member_status/';
                    $filePath = $folder.$name;
                    $this->uploadOne($image, $folder, 'public', $name);
                    if($checkIcon) {
                        $uploadImage = UploadImage::where('image_type', 'inactive_tree_icon')->first();
                        $folder = 'uploads/member_status/';
                        Storage::delete($folder.$uploadImage->image);
                        $uploadImage->update([
                            'image' => $filePath,
                        ]);
                    } else {
                        $filePath = $folder.$name;
                        $data = new UploadImage;
                        $image_type = 'inactive_tree_icon';
                        $data->imageable_type = 'App\Models\Configuration';
                        $data->imageable_id = $this->configuration()['id'];
                        $data->date = now();
                        $data->image_type = $image_type;
                        $data->image = $filePath;
                        $data->save();
                    }

                }
            }
            return back()
                ->with('success', 'Image uploaded successfully.');
        } catch (\Exception $e) {
            return back()
            ->with('error', $e->getMessage());
        }
    }
    public function destroy(Request $request)
    {
        # code...
    }
}
