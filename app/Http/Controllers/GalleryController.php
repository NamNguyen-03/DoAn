<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search'); // Lấy từ khóa tìm kiếm từ request
        $galleries = Gallery::latest();

        if ($search) {
            $galleries->where(function ($query) use ($search) {
                $query->where('gallery_name', 'LIKE', "%$search%");
            });
        }

        $galleries = $galleries->get();

        if (!$galleries) {
            return response()->json([
                'success' => false,
                'message' => 'Lấy danh sách gallery thất bại'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $galleries,
            'message' => 'Lấy danh sách gallery thành công'
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
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'gallery_name'     => 'required|string|max:255',
            'gallery_image'    => 'required|string|',
            'product_id'       => 'required|integer'
        ]);

        // Nếu validate thất bại, trả về lỗi JSON
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Xử lý lưu ảnh vào thư mục public/uploads/product nếu có ảnh
        $imageName = null;
        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/product'), $imageName);
        }

        // Tạo sản phẩm mới với product_sold mặc định là 0
        $gallery = Gallery::create([
            'gallery_name'     => $request->gallery_name,
            'product_id'     => $request->product_id,

        ]);

        return response()->json([
            'success' => true,
            'data'    => $gallery,
            'message' => 'Thêm sản phẩm thành công!'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getGalleryByProduct($id)
    {
        $gallery = Gallery::where('product_id', $id)->get();

        return response()->json([
            'success' => true,
            'data' => $gallery
        ]);
    }

    public function uploadMultiple(Request $request, $id)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        // Validate file ảnh
        $validator = Validator::make($request->all(), [
            'gallery_images' => 'required|array',
            'gallery_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $files = $request->file('gallery_images');
        $uploaded = [];

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            // $originalExtentsion = $file->getClientOriginalName();

            $fileName = time() . '_' . uniqid() . '_' . $originalName;
            $file->move(public_path('uploads/gallery'), $fileName);

            $gallery = Gallery::create([
                'gallery_name' => $fileName,
                'gallery_image' => $fileName,
                'product_id' => $id,
            ]);

            $uploaded[] = $gallery;
        }

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' ảnh đã được tải lên',
            'data' => $uploaded
        ]);
    }
    public function deleteGallery(Request $request, $id)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy ảnh.'
            ], 404);
        }

        // Xóa file ảnh khỏi thư mục
        $imagePath = public_path('uploads/gallery/' . $gallery->gallery_image);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Xóa khỏi DB
        $gallery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ảnh đã được xóa thành công.'
        ]);
    }
}
