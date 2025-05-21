<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $data = $request->validate([
            'product_id' => 'required|exists:tbl_product,product_id',
            'customer_id' => 'required|exists:users,id'
        ]);

        $exists = Wishlist::where('product_id', $data['product_id'])
            ->where('customer_id', $data['customer_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm đã tồn tại trong yêu thích.'
            ], 200);
        }

        $wishlist = new Wishlist();
        $wishlist->product_id = $data['product_id'];
        $wishlist->customer_id = $data['customer_id'];
        $wishlist->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào yêu thích.'
        ]);
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
        $wishlist = Wishlist::find($id);

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm yêu thích.'
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa sản phẩm yêu thích thành công.'
        ], 200);
    }
}
