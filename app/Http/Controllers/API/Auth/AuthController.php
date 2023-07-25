<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register']]);
    }

    public function register(RegisterUserRequest $request)
    {
        try {
            DB::beginTransaction();
            // Create the user record in the users table
            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "role" => $request->role,
            ]);

            // Create the user_details record for additional user information
            $user->userDetails()->create([
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();

            return $this->success([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 'Successfully registered');
        } catch (\Throwable $th) {
            Log::error("ERROR | Message: {$th->getMessage()}, Line: {$th->getLine()}, File: {$th->getFile()}");
            DB::rollBack();

            return $this->error('El registro no pudo completarse', 400);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function getUser(Request $request)
    {
        return response()->json($request->user());
    }
}