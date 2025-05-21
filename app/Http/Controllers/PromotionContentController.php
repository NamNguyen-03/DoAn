<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Start building the query
        $query = Promotion::query();

        // Apply search conditions if a search term is provided
        if ($search) {
            $query->where('subject', 'LIKE', "%$search%")
                ->orWhere('content', 'LIKE', "%$search%")
                ->orWhere('envelope', 'LIKE', "%$search%");
        }

        $content = $query->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $content,
            'message' => 'Lấy promotion content thành công'
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
        $request->validate([
            'envelope' => 'required|string',
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);
        $promotion = Promotion::create([
            'envelope' => $request->envelope,
            'subject' => $request->subject,
            'content' =>  $request->content,
            'created_at' => now('Asia/Ho_Chi_Minh')
        ]);
        return response()->json([
            'success' => true,
            'data' => $promotion,
            'message' => 'Thêm promotion content thành công'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promotion = Promotion::find($id);

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại promotion content'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $promotion,
            'message' => 'Lấy promotion content thành công'
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
        $request->validate([
            'envelope' => 'required|string',
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại promotion content'
            ], 404);
        }

        $promotion->update([
            'envelope' => $request->envelope,
            'subject' => $request->subject,
            'content' => $request->content,
            'update_at' => now('Asia/Ho_Chi_Minh')
        ]);

        return response()->json([
            'success' => true,
            'data' => $promotion,
            'message' => 'Cập nhật promotion content thành công'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promotion = Promotion::find($id);

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại promotion content'
            ], 404);
        }

        $promotion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa promotion content thành công'
        ]);
    }
}
