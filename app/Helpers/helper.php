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

    public static function responseError($msg, $statusCode = 200)
    {
        return response()->json([
            'status' => false,
            'msg' => $msg,
        ], $statusCode);
    }

    public static function responseData($data, $msg = '')
    {
        $response = ['status' => true];
        if (!empty($msg)) {
            $response['msg'] = $msg;
        }
        $response['data'] = $data;
        return response()->json($response);
    }

    public static function responseMsg($msg)
    {
        return response()->json([
            'status' => true,
            'msg' => $msg,
        ]);
    }
}
