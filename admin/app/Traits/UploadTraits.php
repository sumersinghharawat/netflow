<?php

namespace App\Traits;

use File;
use Illuminate\Support\Facades\Storage;

trait UploadTraits
{
    public function singleFileUpload(...$options)
    {
        $file = $options[0];
        $model = $options[1];
        $prefix = $options[2];
        $folder = $options[3];
        $filename = $prefix . '-' . $model->id . time() . '.' . $file->getClientOriginalExtension();
        if (Storage::putFileAs($folder, $file, $filename)) {
            $this->unlinkFile($model);

            $host = request()->getSchemeAndHttpHost();
            $model->image = $host . "/storage/$folder/$filename";
            $model->save();

            return true;
        }

        return false;
    }

    public function multipleFileUpload(...$options)
    {
        $files = $options[0];
        $model = $options[1];
        $prefix = $options[2];
        $folder = $options[3];

        foreach ($files as $key => $file) {
            $filename = $prefix . '-' . $model->id . time() . '.' . $file->getClientOriginalExtension();
            if (Storage::putFileAs($folder, $file, $filename)) {
                $this->unlinkFile($model);
                $host = request()->getSchemeAndHttpHost();
                $model->image = $host . "/storage/$folder/$filename";
                $model->save();
            }
        }

        return true;
    }

    public function unlinkFile($model)
    {
        if ($model->image) {
            if (File::exists($model->image)) {
                File::delete($model->image);
            }
            $model->image = null;
            if ($model->save()) {
                return true;
            }
        }

        return false;
    }

    public function uploadBnkRcpt(...$options)
    {
        $options = $options[0];
        $file = $options['file'];
        $prefix = $options['prefix'];
        $folder = $options['folder'];

        $filename = $prefix . '-' . time() . '.' . $file->getClientOriginalExtension();

        if (Storage::putFileAs($folder, $file, $filename)) {
            return $filename;
        }

        return false;
    }

    public function treeIconUpload(...$options)
    {
        $file       = $options[0];
        $model      = $options[1];
        $prefix     = $options[2];
        $folder     = $options[3];
        $filename   = $prefix . '-' . $model->id . time() . '.' . $file->getClientOriginalExtension();
        if (Storage::putFileAs($folder, $file, $filename)) {
            if ($model->tree_icon) {
                if (File::exists($model->tree_icon)) {
                    File::delete($model->tree_icon);
                }
                $model->tree_icon   = null;
                if ($model->save());
            }
            $host = request()->getSchemeAndHttpHost();
            $model->tree_icon   = $host . "/storage/$folder/$filename";
            $model->save();
            return true;
        }
        return false;
    }

    public function documentUpload(...$options)
    {
        $file = $options[0];
        $model = $options[1];
        $prefix = $options[2];
        $folder = $options[3];
        $filename = $prefix . '-' . $model->id . time() . '.' . $file->getClientOriginalExtension();
        if (Storage::putFileAs($folder, $file, $filename)) {
            $this->unlinkFile($model);
            $host = request()->getSchemeAndHttpHost();
            $model->file_name = $host . "/storage/$folder/$filename";

            return true;
        }

        return false;
    }


    public function loginLogo(...$options)
    {
        $file = $options[0];
        $model = $options[1];
        $prefix = $options[2];
        $folder = $options[3];
        $filename = $prefix . '-' . $model->id . time() . '.' . $file->getClientOriginalExtension();
        if (Storage::putFileAs($folder, $file, $filename)) {
            $host = request()->getSchemeAndHttpHost();
            return $host . "/storage/$folder/$filename";
        }
        return false;
    }

    public function singleImageUpload(...$options)
    {
        $file = $options[0];
        $model = $options[1];
        $prefix = $options[2];
        $folder = $options[3];
        $filename = $prefix . '-' . $model->id . time() . '.' . $file->getClientOriginalExtension();
        if (Storage::putFileAs($folder, $file, $filename)) {
            $this->unlinkFile($model);
            $host = request()->getSchemeAndHttpHost();
            return $host . "/storage/$folder/$filename";
        }
        return false;
    }
}
