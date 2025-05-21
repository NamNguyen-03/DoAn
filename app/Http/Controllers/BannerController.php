<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search'); // Get the search query from the request
        $banners = Banner::query(); // Start building the query

        // Apply search conditions if a search term is provided
        if ($search) {
            $banners->where(function ($query) use ($search) {
                $query->where('banner_name', 'LIKE', "%$search%")
                    ->orWhere('banner_desc', 'LIKE', "%$search%");
            });
        }

        // Get the results, ordered by the latest
        $banners = $banners->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $banners,
            'message' => 'Lấy danh sách banner thành công'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banner_name'   => 'required|string|max:255',
            'banner_desc'   => 'nullable|string',
            'banner_status' => 'required|integer|in:0,1',
            'banner_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra định dạng và kích thước ảnh
        ]);

        // Nếu validate thất bại, trả về lỗi JSON
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Xử lý lưu ảnh vào thư mục public/uploads/banner nếu có ảnh
        $bannerImageName = null;
        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $bannerImageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banner'), $bannerImageName);
        }

        // Tạo banner mới
        $banner = Banner::create([
            'banner_name'   => $request->banner_name,
            'banner_desc'   => $request->banner_desc,
            'banner_status' => $request->banner_status,
            'banner_image'  => $bannerImageName, // Lưu tên ảnh nếu có
        ]);

        // Trả về kết quả sau khi tạo banner thành công
        return response()->json([
            'success' => true,
            'data'    => $banner,
            'message' => 'Thêm banner thành công!'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại banner'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $banner,
            'message' => 'Lấy banner thành công'
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
    public function update(Request $request, string $id)
    {
        // Tìm banner theo ID
        $banner = Banner::find($id);

        // Kiểm tra nếu banner không tồn tại
        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại banner'
            ], 404);
        }

        // Các rules kiểm tra dữ liệu gửi lên
        $rules = [
            'banner_name'    => 'nullable|string|max:255',
            'banner_desc'    => 'nullable|string',
            'banner_status'  => 'nullable|boolean',
            'banner_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Lọc các trường hợp được gửi lên
        $filteredRules = array_filter($rules, function ($key) use ($request) {
            return $request->has($key);
        }, ARRAY_FILTER_USE_KEY);

        // Validate dữ liệu
        $validator = Validator::make($request->all(), $filteredRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Lấy các dữ liệu hợp lệ
        $validatedData = $validator->validated();

        // Kiểm tra và xử lý ảnh nếu có ảnh mới
        if ($request->hasFile('banner_image')) {
            $oldImage = $banner->banner_image;

            // Xoá ảnh cũ nếu có
            if ($oldImage && File::exists(public_path('uploads/banner/' . $oldImage))) {
                File::delete(public_path('uploads/banner/' . $oldImage));
            }

            // Lưu ảnh mới
            $image = $request->file('banner_image');
            $bannerImageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banner'), $bannerImageName);

            // Gán tên ảnh vào dữ liệu cập nhật
            $validatedData['banner_image'] = $bannerImageName;
        }

        // Cập nhật các trường khác
        $banner->update($validatedData);

        // Trả về phản hồi JSON sau khi cập nhật thành công
        return response()->json([
            'success' => true,
            'data'    => $banner,
            'message' => 'Cập nhật banner thành công'
        ], 200);
    }

    // public function updateBannerStatus(Request $request, $id)
    // {
    //     // Find the product by ID or return a 404 error if not found
    //     $banner = Banner::findOrFail($id);

    //     // Validate the incoming request data
    //     $validatedData = $request->validate([
    //         'banner_status' => 'required|boolean', // Ensure this is validated
    //     ]);

    //     try {
    //         // Update the banner status
    //         $banner->update($validatedData);
    //     } catch (\Exception $e) {
    //         // Handle any exceptions that occur during the update
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Cập nhật trạng thái banner thất bại!',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }

    //     // Return a success response with the updated product data
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Trạng thái banner đã được cập nhật thành công!',
    //         'data' => $banner
    //     ]);
    // }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại banner'
            ], 404);
        }

        // Xóa banner
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa banner thành công'
        ]);
    }
}
