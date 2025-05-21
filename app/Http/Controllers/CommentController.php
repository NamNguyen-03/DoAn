<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Rating;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    // public function index(Request $request)
    // {
    //     $search = $request->get('search');

    //     // Khởi tạo query và eager load quan hệ product
    //     $query = Comment::with(['product' => function ($q) {
    //         $q->select('product_id', 'product_name', 'product_image');
    //     }]);

    //     // Nếu có từ khóa tìm kiếm
    //     if ($search) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('comment', 'LIKE', "%$search%")
    //                 ->orWhere('comment_name', 'LIKE', "%$search%");
    //         });
    //     }

    //     // Lấy kết quả mới nhất trước
    //     $comments = $query->orderBy('comment_id', 'desc')->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $comments,
    //         'message' => 'Lấy danh sách comment thành công'
    //     ]);
    // }
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Comment::with([
            'product' => function ($q) {
                $q->select('product_id', 'product_name', 'product_image');
            },
            'rating',
            'replies' => function ($q) {
                $q->select('comment_id', 'comment_name', 'comment', 'comment_date', 'parent_comment_id', 'comment_status', 'product_id')
                    ->with('rating'); // Eager load luôn rating cho replies
            },
            'customer'
        ]);

        // Nếu có từ khóa tìm kiếm
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'LIKE', "%$search%")
                    ->orWhere('comment_name', 'LIKE', "%$search%")
                    // Tìm kiếm trong replies
                    ->orWhereHas('replies', function ($q) use ($search) {
                        $q->where('comment', 'LIKE', "%$search%")
                            ->orWhere('comment_name', 'LIKE', "%$search%");
                    });
            });
        }

        // Lấy comment gốc (không phải reply)
        $query->where('parent_comment_id', 0);

        // Lấy kết quả mới nhất trước
        $comments = $query->orderBy('comment_id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $comments,
            'message' => 'Lấy danh sách comment thành công'
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
        // Kiểm tra token bearer
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        // Validate dữ liệu
        $request->validate([
            'comment' => 'required|string',
            'rating' => 'nullable|integer|min:0|max:5',
            'user_name' => 'required|string',
            'product_id' => 'required|exists:tbl_product,product_id',
            'customer_id' => 'required|integer|exists:users,id',
        ]);

        // Xử lý nếu có parent comment và comment status
        $parentCommentId = $request->has('parent_comment_id') ? $request->parent_comment_id : 0;
        $commentStatus = $request->has('comment_status') ? $request->comment_status : 0;

        try {
            // Tạo bình luận
            $comment = Comment::create([
                'comment' => $request->comment,
                'comment_name' => $request->user_name,
                'product_id' => $request->product_id,
                'customer_id' => $request->customer_id,
                'comment_status' => $commentStatus,
                'parent_comment_id' => $parentCommentId
            ]);

            // Nếu có đánh giá (rating), tạo bản ghi rating liên quan
            if ($request->has('rating')) {
                $comment->rating()->create([
                    'rating' => $request->rating,
                    'product_id' => $request->product_id,
                ]);
            }

            // Trả về phản hồi thành công
            return response()->json([
                'success' => true,
                'message' => 'Đã gửi bình luận!',
                'data' => $comment
            ]);
        } catch (\Exception $e) {
            // Nếu có lỗi trong quá trình tạo bình luận và rating
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi bình luận.',
                'error' => $e->getMessage()
            ]);
        }
    }





    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

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
    public function destroy(Request $request, $id)
    {
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        // Lấy bình luận cha
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['message' => 'Không tìm thấy bình luận.'], 404);
        }

        // Xóa các bình luận con (nếu có)
        $comment->replies()->delete();  // Xóa tất cả bình luận con liên quan đến bình luận cha
        $comment->rating()->delete();
        // Xóa bình luận cha
        $comment->delete();

        return response()->json(['message' => 'Đã xóa bình luận và các bình luận trả lời thành công.'], 200);
    }


    public function getByProductId($product_id)
    {
        $comments = Comment::where('product_id', $product_id)
            ->where('comment_status', 1)
            ->where('parent_comment_id', 0) // Chỉ lấy comment cha
            ->with([
                'replies' => function ($query) {
                    $query->where('comment_status', 1); // Chỉ lấy reply đã duyệt
                },
                'rating' // Eager load quan hệ rating
            ])
            ->orderBy('comment_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments,
            'message' => 'Lấy danh sách bình luận thành công'
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        $comment = Comment::findOrFail($id);
        $comment->comment_status = $comment->comment_status == 1 ? 0 : 1;
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái bình luận thành công',
            'status' => $comment->comment_status
        ]);
    }
}
