<?php

namespace App\Http\Controllers\api;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\profileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();
        return response()->json([
            'status' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function update(profileRequest $request)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();

        if ($user !== null) {
            if ($request->name) {
                $user->update(['name' => $request->name]);
            }
            if ($request->image) {
                $oldImage = $user->image;
                $oldImagePath = public_path('uploads/' . $oldImage);
                if ($oldImage && $oldImagePath) {
                    unlink('uploads/' . $oldImage);
                }
                $userImage = helper::uploadFile($request->file('image'), 'users/photos/');
                $user->update(['image' => $userImage]);
            }
            if ($request->cover) {
                $oldCover = $user->cover;
                if (!empty($oldCover)) {
                    $oldCoverPath = public_path('uploads/' . $oldCover);

                    if (file_exists($oldCoverPath)) {
                        unlink('uploads/' . $oldCover);
                    }
                }
                $userCover = helper::uploadFile($request->file('cover'), 'users/covers/');
                $user->update(['cover' => $userCover]);
            }
            if ($request->longitude && $request->latitude) {
                $user->update([
                    'longitude' => $request->longitude,
                    'latitude' => $request->latitude,
                ]);
            }
            if ($request->birthday) {
                $user->update(['birthday' => $request->birthday]);
            }
            return  helper::responseData(new UserResource($user), "Your Profile updated successfully");
        } else {
            return helper::responseError("User Not Found");
        }
    }

    public function delete_account(Request $request)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'max:50'],
        ]);
        if ($validator->fails()) {
            return helper::responseError($validator->errors());
        }
        $password_Correct = Hash::check($request->password, $user->password);
        if ($password_Correct) {
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => "User $user->name deleted successfully",
            ]);
        } else {
            return helper::responseError('Your password is not correct');
        }
    }

    public function update_password(Request $request)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();

        $validator = Validator::make($request->all(), [
            'old_password' => ['required', 'string', 'min:8', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'max:50', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return helper::responseError($validator->errors());
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
            return helper::responseError("Your old password is not correct");
        }
    }
}
