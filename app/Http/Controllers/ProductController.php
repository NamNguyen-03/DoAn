<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\Gallery;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm.
     */
    public function index(Request $request)
    {
        $search = $request->get('search'); // ?search=
        $tag = $request->get('tag');       // ?tag=

        $products = Product::with(['category', 'brand', 'ratings', 'galleries'])->latest();

        if ($search) {
            $products->where(function ($query) use ($search) {
                $query->where('product_name', 'LIKE', "%$search%")
                    ->orWhere('product_desc', 'LIKE', "%$search%");
            });
        }

        if ($tag) {
            $products->where('product_tags', 'LIKE', "%$tag%");
        }

        $products = $products->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Lấy danh sách sản phẩm thành công'
        ]);
    }



    /**
     * Lưu sản phẩm mới vào database.
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
            'product_name'     => 'required|string|max:255',
            'product_quantity' => 'required|integer|min:1',
            'product_slug'     => 'required|string|max:255|unique:tbl_product,product_slug',
            'category_id'      => 'required|integer|exists:tbl_category,category_id',
            'brand_id'         => 'required|integer|exists:tbl_brand,brand_id',
            'product_desc'     => 'nullable|string',
            'product_tags'     => 'nullable|string',
            'product_content'  => 'nullable|string',
            'product_price'    => 'required|numeric|min:0',
            'product_price_in' => 'required|numeric|min:0',
            'product_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_status'   => 'required|integer|in:0,1'
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
            File:
            copy(
                public_path('uploads/product/' . $imageName),
                public_path('uploads/gallery/' . $imageName)
            );
        }

        // Tạo sản phẩm mới với product_sold mặc định là 0
        $product = Product::create([
            'product_name'     => $request->product_name,
            'product_quantity' => $request->product_quantity,
            'product_slug'     => $request->product_slug,
            'category_id'      => $request->category_id,
            'brand_id'         => $request->brand_id,
            'product_desc'     => $request->product_desc,
            'product_tags'     => $request->product_tags,
            'product_content'  => $request->product_content,
            'product_price'    => $request->product_price,
            'product_price_in' => $request->product_price_in,
            'product_image'    => $imageName, // Nếu không có ảnh, giá trị sẽ là null
            'product_status'   => $request->product_status,
            'product_sold'     => 0 // Thêm giá trị mặc định cho product_sold
        ]);
        if ($imageName) {
            Gallery::create([
                'gallery_name'  => $imageName,
                'gallery_image' => $imageName,
                'product_id'    => $product->product_id
            ]);
        }
        return response()->json([
            'success' => true,
            'data'    => $product,
            'message' => 'Thêm sản phẩm thành công!'
        ], 201);
    }


    /**
     * Hiển thị chi tiết sản phẩm.
     */
    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'ratings', 'galleries', 'comment.replies']);

        $averageRating = $product->ratings->avg('rating');

        return response()->json([
            'success' => true,
            'data' => $product->append('average_rating'),
            'average_rating' => $averageRating,
            'message' => 'Lấy sản phẩm thành công'
        ]);
    }
    public function getProductById($id)
    {
        try {
            // Lấy sản phẩm theo ID, kèm theo quan hệ brand, category, ratings và galleries
            $product = Product::with(['brand', 'category', 'ratings', 'galleries', 'comment.replies'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product, // Trả về sản phẩm với trường average_rating đã được tính toán trong model
                'message' => 'Lấy thông tin sản phẩm thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Cập nhật thông tin sản phẩm.
     */

    // public function update(Request $request, $id)
    // {
    //     if (!$request->bearerToken()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
    //         ], 401);
    //     }

    //     $product = Product::findOrFail($id);

    //     $validatedData = $request->validate([
    //         'product_name'     => 'required|string|max:255',
    //         'product_price'    => 'required|numeric',
    //         'product_quantity' => 'required|integer',
    //         'product_slug'     => 'required|string|unique:tbl_product,product_slug,' . $id . ',product_id',
    //         'product_desc'     => 'nullable|string',
    //         'product_content'  => 'nullable|string',
    //         'category_id'      => 'required|integer',
    //         'brand_id'         => 'required|integer',
    //         'product_status'   => 'required|boolean',
    //         'product_tags'     => 'nullable|string',
    //         'product_image'    => 'nullable|image|max:2048',
    //     ]);

    //     // Nếu có ảnh mới
    //     if ($request->hasFile('product_image')) {
    //         $oldImage = $product->product_image;

    //         // Xoá ảnh cũ nếu có
    //         if ($oldImage) {
    //             $oldProductPath = public_path('uploads/product/' . $oldImage);
    //             $oldGalleryPath = public_path('uploads/gallery/' . $oldImage);

    //             if (file_exists($oldProductPath)) {
    //                 unlink($oldProductPath);
    //             }
    //             if (file_exists($oldGalleryPath)) {
    //                 unlink($oldGalleryPath);
    //             }
    //         }

    //         // Upload ảnh mới
    //         $image = $request->file('product_image');
    //         $newImageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
    //         $image->move(public_path('uploads/product'), $newImageName);

    //         // Copy sang thư mục gallery
    //         File:
    //         copy(
    //             public_path('uploads/product/' . $newImageName),
    //             public_path('uploads/gallery/' . $newImageName)
    //         );

    //         // Cập nhật bản ghi gallery (nếu có)
    //         $gallery = Gallery::where('gallery_name', $oldImage)->first();
    //         if ($gallery) {
    //             $gallery->gallery_name  = $newImageName;
    //             $gallery->gallery_image = $newImageName;
    //             $gallery->save();
    //         }

    //         // Cập nhật vào validatedData
    //         $validatedData['product_image'] = $newImageName;
    //     }

    //     try {
    //         $product->update($validatedData);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Cập nhật sản phẩm thất bại!',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Sản phẩm đã được cập nhật thành công!',
    //         'data'    => $product
    //     ]);
    // }

    public function update(Request $request, Product $product)
    {


        // Tất cả các rule, sẽ lọc lại theo các field được gửi lên
        $rules = [
            'product_name'     => 'string|max:255',
            'product_price'    => 'numeric',
            'product_price_in' => 'numeric',
            'product_quantity' => 'integer',
            'product_sold'     => 'integer',
            'product_slug'     => 'string|unique:tbl_product,product_slug,' . $product->product_id . ',product_id',
            'product_desc'     => 'nullable|string',
            'product_content'  => 'nullable|string',
            'category_id'      => 'integer',
            'brand_id'         => 'integer',
            'product_status'   => 'boolean',
            'product_tags'     => 'nullable|string',
            'product_image'    => 'nullable|image|max:2048',
        ];

        // Lấy rule tương ứng với field có mặt trong request
        $filteredRules = array_filter($rules, function ($key) use ($request) {
            return $request->has($key);
        }, ARRAY_FILTER_USE_KEY);

        // Validate các field gửi lên
        $validator = Validator::make($request->all(), $filteredRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        // Xử lý ảnh nếu có
        if ($request->hasFile('product_image')) {
            $oldImage = $product->product_image;

            // Xóa ảnh cũ nếu tồn tại
            if ($oldImage) {
                $oldProductPath = public_path('uploads/product/' . $oldImage);
                $oldGalleryPath = public_path('uploads/gallery/' . $oldImage);

                if (file_exists($oldProductPath)) unlink($oldProductPath);
                if (file_exists($oldGalleryPath)) unlink($oldGalleryPath);
            }

            // Lưu ảnh mới
            $image = $request->file('product_image');
            $newImageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/product'), $newImageName);

            // Copy sang gallery
            File::copy(
                public_path('uploads/product/' . $newImageName),
                public_path('uploads/gallery/' . $newImageName)
            );

            // Cập nhật gallery nếu có
            $gallery = Gallery::where('gallery_name', $oldImage)->first();
            if ($gallery) {
                $gallery->gallery_name = $newImageName;
                $gallery->gallery_image = $newImageName;
                $gallery->save();
            }

            $validatedData['product_image'] = $newImageName;
        }

        try {
            $product->update($validatedData);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật sản phẩm thất bại!',
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã được cập nhật thành công!',
            'data'    => $product
        ]);
    }

    // //update Status
    // public function updateProductStatus(Request $request, $id)
    // {

    //     // Find the product by ID or return a 404 error if not found
    //     $product = Product::findOrFail($id);

    //     // Validate the incoming request data
    //     $validatedData = $request->validate([
    //         'product_status' => 'required|boolean', // Ensure this is validated
    //     ]);

    //     try {
    //         // Update the product status
    //         $product->update($validatedData);
    //     } catch (\Exception $e) {
    //         // Handle any exceptions that occur during the update
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Cập nhật trạng thái sản phẩm thất bại!',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }

    //     // Return a success response with the updated product data
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Trạng thái sản phẩm đã được cập nhật thành công!',
    //         'data' => $product
    //     ]);
    // }
    /**
     * Xóa sản phẩm.
     */
    public function destroy(Request $request, Product $product)
    {

        // Xoá ảnh sản phẩm
        if ($product->product_image) {
            $imagePath = public_path('uploads/product/' . $product->product_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Xoá ảnh gallery
        $galleries = Gallery::where('product_id', $product->product_id)->get();
        foreach ($galleries as $gallery) {
            $galleryImagePath = public_path('uploads/gallery/' . $gallery->gallery_image);
            if (file_exists($galleryImagePath)) {
                unlink($galleryImagePath);
            }
            $gallery->delete();
        }

        // Xoá sản phẩm
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa sản phẩm và ảnh liên quan thành công'
        ]);
    }


    // public function getProductsByCategory($category_id)
    // {
    //     try {
    //         // Kiểm tra xem category có tồn tại không
    //         $category = Category::find($category_id);

    //         if (!$category) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Category not found'
    //             ], 404);
    //         }

    //         // Lấy sản phẩm thuộc category này
    //         $products = Product::where('category_id', $category_id)->get();

    //         if ($products->isEmpty()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'No products found for this category',
    //                 'data' => []
    //             ], 200);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'category_id' => $category_id,
    //             'data' => $products
    //         ], 200);
    //     } catch (\Exception $e) {
    //         // Log lỗi để biết nguyên nhân
    //         Log::error("Error fetching products for category {$category_id}: " . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Internal Server Error'
    //         ], 500);
    //     }
    // }
    // public function getProductsByBrand($brand_id)
    // {
    //     try {
    //         // Kiểm tra xem category có tồn tại không
    //         $brand = Brand::find($brand_id);

    //         if (!$brand) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Category not found'
    //             ], 404);
    //         }

    //         // Lấy sản phẩm thuộc category này
    //         $products = Product::where('brand_id', $brand_id)->get();

    //         if ($products->isEmpty()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'No products found for this category',
    //                 'data' => []
    //             ], 200);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'brand_id' => $brand_id,
    //             'data' => $products
    //         ], 200);
    //     } catch (\Exception $e) {
    //         // Log lỗi để biết nguyên nhân
    //         Log::error("Error fetching products for category {$brand_id}: " . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Internal Server Error'
    //         ], 500);
    //     }
    // }
    public function getRelatedProducts($category_id)
    {
        $products = Product::with('category', 'brand')
            ->where('category_id', $category_id)
            ->inRandomOrder()
            ->take(9)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
    public function getByIds(Request $request)
    {
        $ids = $request->query('ids');

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'Tham số "ids" là bắt buộc và phải là mảng.'], 400);
        }

        $products = Product::whereIn('product_id', $ids)->get();

        return response()->json($products);
    }
}
