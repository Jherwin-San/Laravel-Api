<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthUser extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (!Auth::attempt($data)) {
            return response([
                'message' => 'email or password are wrong'
            ]);
        }
        $user = Auth::user();

        if ($user->role === 'admin') {
            $token = $user->createToken('admin-token', ['create', 'update', 'delete'])->plainTextToken;
        } else {
            $token = $user->createToken('user-token', ['create', 'update'])->plainTextToken;
        }

        return response()->json([
            'message' => "Welcome " . $user->name,
            'access_token' => $token
        ]);

    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),

        ]);

        return response()->json([
            'message' => "Welcome " . $user->name . " your account was registered successfully.",
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response('Loggout Successfully', 204);
    }
}
