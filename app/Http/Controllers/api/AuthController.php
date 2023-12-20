<?php

namespace App\Http\Controllers\api;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\loginRequest;
use App\Http\Requests\registerRequest;
use App\Http\Resources\UserResource;
use App\Mail\ForgetPasswordMail;
use App\Models\Role;
use App\Models\User;
use Google_Client;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AuthController extends Controller
{

    public function register(registerRequest $request)
    {
        $userImage = helper::uploadFile($request->file('image'), 'users/photos/');
        $userCover = helper::uploadFile($request->file('cover'), 'users/covers/');
        $token = Str::random(64);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'birthday'    => $request->birthday,
            'latitude'    => $request->latitude,
            'longitude'    => $request->longitude,
            'image'    => $userImage,
            'cover'    => $userCover,
            'token'    => $token,
            'role_id'  => Role::where('name', 'user')->value('id'),
        ]);
        // Send email verification notification
        $user->sendEmailVerificationNotification();

        return response()->json([
            "status"  => true,
            'message' => "You are successfully registered",
            "data" => new UserResource($user),
        ]);
    }

    public function login(loginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $token = Str::random(64);
            $user = Auth::user();
            $user->token = $token;
            $user->save();
            return response()->json([
                'status'  => true,
                'message' => 'Login successful',
                'data'    => new UserResource($user),
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Invalid credentials',
        ], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();
        if ($user) {
            $user->update(['token' => null]);
            Auth::logout();

            return response()->json([
                'status'  => true,
                'message' => 'Logged out successfully',
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Invalid token',
        ]);
    }

    function forget(Request $request)
    {
        $vaildator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($vaildator->fails()) {
            return response()->json([
                'errors' => $vaildator->errors()->all(),
            ], 200);
        }

        $user = User::where('email', $request->email)->first();
        $otp = random_int(1000, 9999);
        $user->update([
            'otp' => $otp,
        ]);
        try {
            Mail::to($request->email)->send(new ForgetPasswordMail($user->otp));
            return response()->json([
                'message' => 'OTP sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    function otp($otp)
    {
        $user = User::where('otp', $otp)->first();

        $response = [
            'status' => $user !== null,
        ];

        if (!$response['status']) {
            $response['message'] = 'OTP is not correct';
        }

        return response()->json($response);
    }

    public function reset(Request $request, $otp)
    {
        $validator = Validator::make($request->all(), [
            "password"  => ["required", "string", "min:8", "max:50", "confirmed"],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 200);
        }

        $user = User::where('otp', $otp)->first();

        if (!$user) {
            return response()->json(['message' => 'OTP is not correct'], 200);
        }

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'New password must be different from the previous password'], 200);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }


    public function sendVerificationLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
            ], 200);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return response()->json([
                'status'  => true,
                'message' => 'Verification link sent successfully.',
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Email is already verified.',
        ]);
    }


    public function verify(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if ($user && hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }

            return redirect()->to('/'); // Redirect to your desired location after verification
        }
    }
    public function handleGoogleLogin(Request $request, string $uid)
    {
        try {
            $firebase = Firebase::auth();
            $userData = $firebase->getUser($uid);
            $user = User::where('email', $userData->email)->first();
            if ($user != null) {
                Auth::login($user);
                return helper::responseData(new UserResource($user), 'Login successful');
            } else {
                // Perform user registration
                $access_token = Str::random(64);
                $newuser = new User();
                $newuser->name = $userData->displayName;
                $newuser->email = $userData->email;
                $newuser->password = Hash::make($userData->uid . now());
                $newuser->token = $access_token;
                $newuser->role_id = Role::where('name', 'user')->value('id');
                $newuser->save();
                return helper::responseData(new UserResource($newuser), 'You are successfully registered');
            }
        } catch (UserNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
