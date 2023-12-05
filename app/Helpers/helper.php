<?php

namespace App\Helpers;


class helper
{
    public static function uploadFile($file, $subdirectory, $prefix = '')
    {
        if ($file) {
            $ext = $file->getClientOriginalExtension();
            $filename = $prefix  . uniqid() . '.' . $ext;
            $destination = public_path('uploads/' . $subdirectory);
            $file->move($destination, $filename);
            return $subdirectory.$filename;
        }else{
            return null;
        }
    }

}
