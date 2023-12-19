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
            return $subdirectory . $filename;
        } else {
            return null;
        }
    }

    public static function responseError($statusCode = 200, $msg)
    {
        return response()->json([
            'status' => false,
            'msg' => $msg,
        ], $statusCode);
    }

    public static function responseData($data, $msg = '')
    {
        $response = [
            'status' => true,
            'data' => count($data) == 0 ? 'No data available' : $data,
        ];
        if (!empty($msg)) {
            $response['msg'] = $msg;
        }
        return response()->json($response);
    }
}
