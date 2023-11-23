<?php

namespace App\Http\Controllers\api;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\loginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Mail\ForgetPasswordMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        $imageName = helper::uploadFile($request->file('image'), 'users/');

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'birthday'    => $request->birthday,
            'password' => Hash::make($request->password),
            'image'    => $imageName,
            'role_id'  => Role::where('name', 'user')->value('id'),
        ]);
        // Send email verification notification
        $user->sendEmailVerificationNotification();

        return response()->json([
            "status"  => true,
            'message' => "You are successfully registered",
        ]);
    }

    public function login(loginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = Str::random(64);
            $user->update(['token' => $token]);
            $cookie = cookie('auth_token', $token, 60 * 24 * 30);
            return response()->json([
                'status'  => true,
                'message' => 'Login successful',
                'user'    => new UserResource($user),
            ])->withCookie($cookie);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function logout(Request $request)
    {
        $token = $request->cookie('auth_token');
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
            ], 422);
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
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $user = User::where('otp', $otp)->first();

        if (!$user) {
            return response()->json(['message' => 'OTP is not correct'], 404);
        }

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'New password must be different from the previous password'], 422);
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
            ], 404);
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

        abort(404); // You can customize this response as needed
    }
}
