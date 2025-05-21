<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Hiển thị danh sách thương hiệu.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $brands = Brand::with('products'); // Eager load quan hệ cha

        if ($search) {
            $brands->where(function ($query) use ($search) {
                $query->where('brand_name', 'LIKE', "%$search%")
                    ->orWhere('brand_desc', 'LIKE', "%$search%");
            });
        }

        $brands = $brands->latest()->get();
        if (!$brands) {
            return response()->json([
                'success' => false,
                'message' => 'Không lấy được danh sách thương hiệu thành công'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $brands,
            'message' => 'Lấy danh sách thương hiệu thành công'
        ]);
    }

    /**
     * Lưu thương hiệu mới vào database.
     */
    public function store(Request $request)
    {
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        $request->validate([
            'brand_name' => 'required|string|max:255',
            'brand_desc' => 'nullable|string',
            'brand_status' => 'required|integer',
            'brand_slug' => 'nullable|string|max:255',
        ]);

        $brand = Brand::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $brand,
            'message' => 'Thêm thương hiệu thành công'
        ], 201);
    }

    /**
     * Hiển thị chi tiết thương hiệu.
     */
    public function show(Brand $brand)
    {
        $brand->load('products');

        return response()->json([
            'success' => true,
            'data' => $brand,
            'message' => 'Lấy thương hiệu thành công'
        ]);
    }

    /**
     * Cập nhật thông tin thương hiệu.
     */
    public function update(Request $request, Brand $brand)
    {
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại thương hiệu'
            ], 404);
        }

        $brand->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $brand,
            'message' => 'Cập nhật thương hiệu thành công'
        ]);
    }

    /**
     * Xóa thương hiệu.
     */
    public function destroy(Request $request, Brand $brand)
    {
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại thương hiệu'
            ], 404);
        }

        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa thương hiệu thành công'
        ]);
    }
    public function updateBrandOrder(Request $request)
    {
        $sorted = $request->input('sorted'); // lấy mảng brand_id + brand_order

        if (!$sorted || !is_array($sorted)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 400);
        }

        foreach ($sorted as $item) {
            if (isset($item['brand_id']) && isset($item['brand_order'])) {
                Brand::where('brand_id', $item['brand_id'])
                    ->update(['brand_order' => $item['brand_order']]);
            }
        }

        return response()->json(['success' => true]);
    }
}
