<?php

namespace App\Http\Controllers\api;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $token = $request->header('auth_token');
        $user = User::where('token', $token)->first();
        return response()->json([
            'status' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function update(Request $request)
    {
        $token = $request->header('auth_token');
        $user = User::where('token', $token)->first();
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string'],
            'image' => ['nullable', 'image'],
            'cover' => ['nullable', 'image'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }

        if ($user !== null) {
            if ($request->name) {
                $user->update(['name' => $request->name]);
            }
            if ($request['image']) {
                $oldImage = $user->image;
                $oldImagePath = public_path('uploads/' . $oldImage);
                if ($oldImage && $oldImagePath) {
                    unlink('uploads/' . $oldImage);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "Image file not found in public folder",
                    ]);
                }
                $userImage = helper::uploadFile($request->file('image'), 'users/photos/');
                $user->update(['image' => $userImage]);
            }

            if ($request['cover']) {
                $oldCover = $user->cover;
                if (!empty($oldCover)) {
                    $oldCoverPath = public_path('uploads/' . $oldCover);

                    if (file_exists($oldCoverPath)) {
                        unlink('uploads/' . $oldCover);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => "Cover file not found in public folder",
                        ]);
                    }
                }
                $userCover = helper::uploadFile($request->file('cover'), 'users/covers/');
                $user->update(['cover' => $userCover]);
            }

            return response()->json([
                'status' => true,
                'message' => "Your Profile updated successfully",
                "data" => new UserResource($user)
            ]);
        }
        return response()->json([
            'status' => false,
            "message" => 'User Not Found'
        ]);
    }

    public function delete_account(Request $request)
    {
        $token = $request->header('auth_token');
        $user = User::where('token', $token)->first();
        $user->delete();
        return response()->json([
            'status' => true,
            'message' => "User $user->name deleted successfully",
        ]);
    }

    public function update_password(Request $request)
    {
        $token = $request->header('auth_token');
        $user = User::where('token', $token)->first();

        $validator = Validator::make($request->all(), [
            'old_password' => ['required', 'string', 'min:8', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'max:50', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        $password_Correct = Hash::check($request->old_password, $user->password);
        if ($password_Correct) {
            if (!Hash::check($request->password, $user->password)) {
                $user->update(['password' => Hash::make($request->password)]);
                return response()->json([
                    'status' => true,
                    'message' => "Your password updated successfully",
                ]);
            } else {
                return response()->json(['message' => 'New password must be different from the previous password']);
            }
        } else {
            return response()->json([
                'status' => false,
                "message" => 'Your old password is not correct'
            ]);
        }
    }
}
