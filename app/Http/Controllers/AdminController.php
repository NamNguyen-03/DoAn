<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::all();
        return response()->json($admins);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:tbl_admin,admin_email',
            'admin_password' => 'required|string|min:6',
            'admin_phone' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $admin = Admin::create([
            'admin_name' => $request->admin_name,
            'admin_email' => $request->admin_email,
            'admin_password' => Hash::make($request->admin_password),
            'admin_phone' => $request->admin_phone,
        ]);

        return response()->json(['message' => 'Admin created successfully', 'admin' => $admin], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        $roles = $admin->roles->pluck('role_name');

        return response()->json([
            'success' => true,
            'message' => 'Lấy admin thành công',
            'data' =>  $admin,
            'roles' => $roles
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $admin = Admin::findOrFail($id);

        $rules = [
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:tbl_admin,admin_email,' . $admin->admin_id . ',admin_id',
            'admin_phone' => 'required|max:255|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $admin->update([
            'admin_name' => $request->admin_name,
            'admin_email' => $request->admin_email,
            'admin_phone' => $request->admin_phone,

        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin updated successfully',
            'data' => $admin
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully']);
    }

    public function createAdmin(Request $request)
    {
        try {
            $admin = auth('admins')->user();
            if (!$admin instanceof \App\Models\Admin || !$admin->hasRole('superadmin')) {
                return response()->json(['message' => 'Bạn không có quyền tạo admin mới.'], 403);
            }

            // Validate dữ liệu
            $validated = $request->validate([
                'admin_name' => 'required',
                'admin_email' => 'required|email|unique:tbl_admin,admin_email',
                'admin_password' => 'required|min:6',
                'admin_phone' => 'required|min:10|numeric',
            ]);

            $admin = Admin::create([
                'admin_name' => $validated['admin_name'],
                'admin_email' => $validated['admin_email'],
                'admin_phone' => $validated['admin_phone'],
                'admin_password' => Hash::make($validated['admin_password']),
            ]);

            // Gán vai trò admin thường
            $role = Roles::where('role_name', 'admin')->first();
            $admin->roles()->attach($role);

            return response()->json([
                'success' => true,
                'message' => 'Admin mới đã được tạo!'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        }
    }


    public function updateAdminProfile(Request $request)
    {
        $admin = auth('admins')->user();

        if (!$admin instanceof \App\Models\Admin) {
            return response()->json(['message' => 'Không xác định được người dùng.'], 401);
        }

        if ($admin->admin_id != $request->admin_id && !$admin->hasRole('superadmin')) {
            return response()->json(['message' => 'Bạn không có quyền sửa tài khoản của người khác.'], 403);
        }

        $admin->update([
            'admin_name' => $request->admin_name,
            'admin_email' => $request->admin_email,
        ]);

        return response()->json(['message' => 'Cập nhật thông tin thành công!']);
    }
}
