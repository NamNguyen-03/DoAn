<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use Illuminate\Http\Request;

class PostCateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = PostCategory::with('posts'); // Load luôn các bài viết liên quan

        // Nếu có từ khóa tìm kiếm, thêm điều kiện lọc
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cate_post_name', 'LIKE', "%$search%")
                    ->orWhere('cate_post_desc', 'LIKE', "%$search%");
            });
        }

        $postcates = $query->latest()->get();
        if (!$postcates) {
            return response()->json([
                'success' => false,
                'message' => 'Không có danh mục bài viết!'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $postcates,
            'message' => 'Lấy danh sách danh mục bài viết thành công'
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
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        $request->validate([
            'cate_post_name' => 'required|string|max:255',
            'cate_post_desc' => 'nullable|string',
            'cate_post_status' => 'required|integer',
            'cate_post_slug' => 'nullable|string|max:255',
        ]);

        $postcates = PostCategory::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $postcates,
            'message' => 'Thêm danh mục bài viết thành công'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PostCategory $postcate)
    {
        $postcate->load('posts');
        if (!$postcate) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại danh mục'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $postcate,
            'message' => 'Lấy danh mục bài viết thành công'
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
    public function update(Request $request, PostCategory $postcate)
    {
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }


        if (!$postcate) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại danh mục bài viết'
            ], 404);
        }

        $postcate->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $postcate,
            'message' => 'Cập nhật danh mục bài viết thành công'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, PostCategory $postcate)
    {
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }


        if (!$postcate) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại danh mục bài viết'
            ], 404);
        }

        $postcate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa danh mục bài viết thành công'
        ]);
    }
}
