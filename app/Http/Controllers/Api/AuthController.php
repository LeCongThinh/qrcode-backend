<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Kiểm tra tài khoản
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        // Lấy user
        $user = Auth::user();

        // Tạo token
        $token = $user->createToken('api-token')->plainTextToken;

        // Trả về token
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ]);
    }
}
