<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Eager load cate_post quan hệ belongsTo
        $posts = Post::with('cate_post')->latest();

        if ($search) {
            $posts->where(function ($query) use ($search) {
                $query->where('post_title', 'LIKE', "%$search%")
                    ->orWhere('post_desc', 'LIKE', "%$search%");
            });
        }

        $posts = $posts->get();

        if (!$posts) {
            return response()->json([
                'success' => false,
                'message' => 'Lấy danh sách bài viết thất bại'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $posts,
            'message' => 'Lấy danh sách bài viết thành công'
        ]);
    }






    public function store(Request $request)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'post_title' => 'required|string|max:255',
            'post_slug' => 'required|string|max:255|unique:tbl_post,post_slug',
            'post_desc' => 'nullable|string',
            'post_content' => 'nullable|string',
            'cate_post_id' => 'required|integer|exists:tbl_category_post,cate_post_id',
            'post_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'post_status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $imageName = null;
        if ($request->hasFile('post_image')) {
            $image = $request->file('post_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalName();
            $image->move(public_path('uploads/post'), $imageName);
        }

        $post = Post::create([
            'post_title' => $request->post_title,
            'post_slug' => $request->post_slug,
            'post_desc' => $request->post_desc,
            'post_content' => $request->post_content,
            'cate_post_id' => $request->cate_post_id,
            'post_image' => $imageName,
            'post_status' => $request->post_status,
        ]);

        return response()->json([
            'success' => true,
            'data' => $post,
            'message' => 'Thêm bài viết thành công'
        ]);
    }




    public function show(Post $post)
    {
        $post->load('cate_post');
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài viết'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $post,
            'message' => 'Lấy bài viết thành công'
        ]);
    }





    public function update(Request $request, Post $post)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'post_title' => 'required|string|max:255',
            'post_slug' => 'required|string|max:255|unique:tbl_post,post_slug,' . $post->post_id . ',post_id',
            'post_desc' => 'nullable|string',
            'post_content' => 'nullable|string',
            'cate_post_id' => 'required|integer|exists:tbl_category_post,cate_post_id',
            'post_status' => 'required|boolean',
            'post_image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('post_image')) {
            if ($post->post_image) {
                $oldPath = public_path('uploads/post/' . $post->post_image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $image = $request->file('post_image');
            $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/post'), $imageName);
            $post->post_image = $imageName;
        }

        $post->update([
            'post_title' => $request->post_title,
            'post_slug' => $request->post_slug,
            'post_desc' => $request->post_desc,
            'post_content' => $request->post_content,
            'cate_post_id' => $request->cate_post_id,
            'post_status' => $request->post_status,
        ]);

        return response()->json([
            'success' => true,
            'data' => $post,
            'message' => 'Cập nhật bài viết thành công'
        ]);
    }





    public function destroy(Request $request, Post $post)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài viết'], 404);
        }

        if ($post->post_image) {
            $path = public_path('uploads/post/' . $post->post_image);
            if (file_exists($path)) unlink($path);
        }

        $post->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa bài viết thành công'
        ]);
    }

    public function updatePostStatus(Request $request, $id)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        $post = Post::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'post_status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $post->post_status = $request->post_status;
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái bài viết thành công!',
            'data' => $post
        ]);
    }

    // public function getPostsByCategory($cate_post_id)
    // {
    //     try {
    //         $posts = Post::where('cate_post_id', $cate_post_id)->where('post_status', 1)->get();

    //         return response()->json([
    //             'success' => true,
    //             'data' => $posts,
    //             'message' => 'Lấy bài viết theo danh mục thành công'
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error("Lỗi lấy bài viết theo danh mục {$cate_post_id}: " . $e->getMessage());
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Lỗi hệ thống'
    //         ], 500);
    //     }
    // }
}
