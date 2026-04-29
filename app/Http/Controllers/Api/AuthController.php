<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required', // Removed |email to allow username/name
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (!Auth::attempt([$loginType => $request->email, 'password' => $request->password])) {
             return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where($loginType, $request->email)->firstOrFail();

        $token = $user->createToken($request->device_name)->plainTextToken;
        $settings = \App\Models\Setting::getStoreSettings();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'permissions' => $user->permissions ?? [], // Ensure it returns array
            ],
            'settings' => $settings,
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Update FCM Token for Push Notifications
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        if ($user) {
            $user->fcm_token = $request->fcm_token;
            // $user->timestamps = false; // Optional to not update updated_at
            $user->save();
        }

        return response()->json(['message' => 'FCM Token updated successfully']);
    }
}
