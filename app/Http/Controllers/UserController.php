<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use HasApiTokens, Notifiable;
    /**
     * Hiển thị danh sách người dùng.
     */
    public function index()
    {
        $q = request()->query('search');

        $usersQuery = User::with([
            'orders.shipping',
            'orders.order_details'
        ])->latest();

        if ($q) {
            $usersQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            });
        }

        $users = $usersQuery->get();

        return response()->json([
            'success' => true,
            'data' => $users,
            'message' => 'Lấy danh sách user thành công'
        ]);
    }


    /**
     * Lưu user mới vào database.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|regex:/^[0-9]{10,15}$/',  // Kiểm tra độ dài và định dạng của số điện thoại
            'password' => 'required|string|min:8',  // Mật khẩu tối thiểu 8 ký tự và phải xác nhận khớp
        ]);

        // Nếu validation thất bại, trả về lỗi
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Nếu validation thành công, tạo user mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Thêm user thành công'
        ], 201);
    }

    /**
     * Hiển thị chi tiết user.
     */
    public function show($id)
    {
        $user = User::with([
            'orders.shipping',
            'orders.order_details',
            'wishlist.product',
            'comments.replies',
            'comments.product',
            'comments.rating'
        ])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại user'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Lấy user thành công'
        ]);
    }


    /**
     * Cập nhật thông tin user.
     */
    public function update(Request $request, int $id)
    {
        // Tìm user theo id
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại user'
            ], 404);
        }

        // Validate dữ liệu từ request
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15|regex:/^[0-9]{10,15}$/', // Kiểm tra định dạng số điện thoại
        ]);

        // Nếu validation thất bại, trả về lỗi
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Cập nhật user với các trường hợp nếu có giá trị mới từ request
        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'phone' => $request->phone ?? $user->phone,
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Cập nhật user thành công'
        ]);
    }



    /**
     * Xóa user.
     */
    public function destroy(Request $request, int $id)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại user'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa user thành công'
        ]);
    }


    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();
    //         $token = bin2hex(random_bytes(32)); // Tạo token đơn giản

    //         // Lưu token vào database
    //         $user->api_token = $token;
    //         $user->save();

    //         return response()->json([
    //             'success' => true,
    //             'token' => $token,
    //             'message' => 'Đăng nhập thành công'
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Tài khoản hoặc mật khẩu không đúng'
    //     ], 401);
    // }
    public function getUser(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Token không hợp lệ'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Lấy thông tin user thành công'
        ]);
    }
}
