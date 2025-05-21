<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Start building the query
        $query = Coupon::query();

        // Apply search conditions if a search term is provided
        if ($search) {
            $query->where('coupon_name', 'LIKE', "%$search%")
                ->orWhere('coupon_number', 'LIKE', "%$search%");
        }

        // Get the results, ordered by the latest
        $coupons = $query->latest()->get();
        if (!$coupons) {
            return response()->json([
                'success' => false,
                'message' => 'Lấy danh sách coupon thất bại'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $coupons,
            'message' => 'Lấy danh sách coupon thành công'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $request->validate([
            'coupon_name' => 'required|string|max:255',
            'coupon_code' => 'required|string|max:50|unique:tbl_coupon,coupon_code',
            'coupon_number' => 'required|integer',
            'coupon_condition' => 'required|integer',
            'coupon_qty' => 'required|integer',
            'coupon_date' => 'required|string|max:255',

        ]);

        $coupon = Coupon::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => 'Thêm coupon thành công'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại coupon'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => 'Lấy coupon thành công'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        // Kiểm tra token


        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại coupon'
            ], 404);
        }

        // Validate dữ liệu
        $validated = $request->validate([
            'coupon_name' => 'required|string|max:255',
            'coupon_code' => 'required|string|max:50|unique:tbl_coupon,coupon_code,' . $coupon->coupon_id . ',coupon_id',
            'coupon_number' => 'required|integer',
            'coupon_condition' => 'required|integer',
            'coupon_qty' => 'required|integer',
            'coupon_date' => 'required|string|max:255',
        ]);

        // Cập nhật dữ liệu đã validate
        $coupon->update($validated);

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => 'Cập nhật coupon thành công'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại coupon'
            ], 404);
        }

        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa coupon thành công'
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
        ]);
        $code = $request->coupon_code;
        $userId = $request->user_id;
        $coupon = Coupon::where('coupon_code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã không hợp lệ'
            ]);
        }

        $today = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

        if ($coupon->coupon_date < $today) {
            return response()->json([
                'success' => true,
                'expired' => true,
                'message' => 'Mã đã hết hạn'
            ]);
        }

        if ($coupon->coupon_qty <= 0) {
            return response()->json([
                'success' => true,
                'expired' => true,
                'message' => 'Mã đã hết lượt sử dụng'
            ]);
        }
        $used = Order::where('customer_id', $userId)
            ->where(function ($query) use ($code) {
                $query->where('order_coupon', $code)
                    ->orWhere('order_coupon', 'LIKE', $code . '-%');
            })->exists();

        if ($used) {
            return response()->json([
                'success' => true,
                'message' => 'Bạn đã dùng mã này rồi'
            ]);
        }
        return response()->json([
            'success' => true,
            'expired' => false,
            'discount' => $coupon->coupon_condition == '1'
                ? $coupon->coupon_number . '%'
                : number_format($coupon->coupon_number) . 'đ',
            'amount' => $coupon->coupon_number,
            'type' => $coupon->coupon_condition,
            'date' => $coupon->coupon_date
        ]);
    }
}
