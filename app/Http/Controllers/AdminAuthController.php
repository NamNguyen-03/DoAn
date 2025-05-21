<?php

// AdminAuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $admin = Admin::where('admin_email', $request->email)->first();

        if (!$admin) {
            return response()->json(['message' => 'Email không tồn tại!'], 401);
        }

        if (!Hash::check($request->password, $admin->admin_password)) {
            return response()->json(['message' => 'Mật khẩu không chính xác!'], 401);
        }

        $token = $admin->createToken('AdminToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'token' => $token,
            'admin' => $admin
        ]);
    }

    public function logout(Request $request)
    {
        // Lấy token từ header Authorization
        $token = $request->bearerToken();

        // Kiểm tra nếu không có token
        if (!$token) {
            return response()->json([
                'message' => 'Token không hợp lệ hoặc không có token.'
            ], 401);
        }


        $user = auth('admins')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Token không hợp lệ'
            ], 401);
        }



        // Xóa tất cả các token có tên 'AdminToken' từ bảng personal_access_tokens
        $user->tokens->where('name', 'AdminToken')->each(function ($token) {
            $token->delete(); // Xóa token
        });

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ]);
    }
    public function changeAdminPassword(Request $request)
    {
        $admin = $request->user();
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Hash::check($request->current_password, $admin->admin_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng.'
            ], 403);
        }

        $admin->admin_password = Hash::make($request->new_password);
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }
    public function verifyAdminPass(Request $request)
    {
        $admin = $request->user();

        // Debug
        logger('Admin:', [$admin]);
        logger('Request password:', [$request->password]);
        logger('Hash check result:', [Hash::check($request->password, $admin->admin_password)]);

        if (!$admin || !Hash::check($request->password, $admin->admin_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Sai mật khẩu!'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Verify thành công!',
        ]);
    }
}
