<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    function Register(RegisterRequest $request)
    {

        $payload = $request->validated();

        if (User::firstWhere("email", $payload["email"])) {
            return response()->json([
                "message" => "Akun sudah terdaftar"
            ], 409);
        }

        Hash::make($payload["password"]);
        $user = User::create($payload);

        if ($user) {
            return response()->json(["message" => "Register sukses"], 201);
        } else {
            return response()->json(["message" => "Gagal mendaftar"], 500);
        }
    }

    function Login(LoginRequest $request)
    {
        $payload = $request->validated();
        $user = User::firstWhere("email", $payload["email"]);

        if ($user) {
            if (Hash::check($payload["password"], $user->password)) {
                $token = $user->createToken("auth_token")->plainTextToken;

                return response()->json([
                    "message" => "Login berhasil",
                    "user" => $user,
                    "token" => $token
                ], 200);
            } else {
                return response()->json([
                    "message" => "Password salah"
                ], 401);
            }
        } else {
            return response()->json([
                "message" => "Akun tidak terdaftar"
            ], 404);
        }
    }

    function logout(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();

            $user->currentAccessToken()->delete();
            $user->tokens()->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful',
        ], 204);
    }

    function editProfile(Request $request)
    {
        $loggedIn = Auth::user();

        // Validasi data
        $request->validate([
            'username' => ['nullable', 'string', Rule::unique('users')->ignore($loggedIn->id)],
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($loggedIn->id)],
            'password' => ['nullable', 'string'],
        ]);

        $user = User::find($loggedIn->id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found',
            ], 404);
        }

        $user->username = $request->username;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Data berhasil diubah',
            'data' => $user,
        ], 200);
    }

    function User() {
        $user = Auth::user();

        return response()->json($user, 200);
    }
}