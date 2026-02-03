<?php

namespace App\Http\Controllers\Api\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Models\User;

class LoginController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|string|max:255',
            ], [
                'email.unique' => 'This email is already registered. Please use another email.',
            ]);

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => $request->role,
                'is_active' => true,
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'user'  => $user->only(['id','name','email','role','is_active','created_at']),
                'token' => $token,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    // Login
    public function login(Request $request)
    {
        // 1) Validate (stronger)
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:72'],
            'device_name' => ['nullable', 'string', 'max:64'], // token name
            'remember' => ['nullable', 'boolean'],
        ]);
        
        // Normalize email
        $email = Str::lower(trim($credentials['email']));
        
        // 2) Rate limit keys (email + ip)
        $emailKey = 'login:email:' . $email;
        $ipKey    = 'login:ip:' . $request->ip();

        // 3) Check limits
        if (
            RateLimiter::tooManyAttempts($emailKey, 3) ||
            RateLimiter::tooManyAttempts($ipKey, 20)
        ) {
            return response()->json([
                'message' => "Too many login attempts. Please try again later.",
            ], 420);
        }

        // 4) Find user (no info leak)
        $user = User::where('email', $email)->first();

        // If user invalid OR password invalid → hit limit + generic error
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($emailKey, 60);
            RateLimiter::hit($ipKey, 60);

            return response()->json([
                'message' => "Invalid login credentials.",
            ], 401);
        }

        // Optional: block/inactive check (if you have this column)
        if ($user->is_active === 0) {
            return response()->json([
                'message' => "Your account is disabled.",
            ], 403);
        }

        // 5) Successful login → clear limits
        RateLimiter::clear($emailKey);
        RateLimiter::clear($ipKey);

        // Optional: update last login data (add columns if needed)
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        $remember = (bool)($credentials['remember'] ?? false);

        if ($remember) {
            $user->setRememberToken(Str::random(60));
            $user->save();
        } else {
            // চাইলে remember_token null করে দিতে পারো
            $user->setRememberToken(null);
            $user->save();
        }

        // Optional: revoke old tokens (single-device login)
        $user->tokens()->delete();

        // 6) Create token with abilities
        $deviceName = $credentials['device_name'] ?? 'api-token';
        $token = $user->createToken($deviceName, ['*'])->plainTextToken;
        
        return response()->json([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
        ], 200);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
