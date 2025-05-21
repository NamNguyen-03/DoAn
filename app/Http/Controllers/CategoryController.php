<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Hiển thị danh sách danh mục.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $categories = Category::with('parent', 'children', 'products'); // Eager load quan hệ cha

        if ($search) {
            $categories->where(function ($query) use ($search) {
                $query->where('category_name', 'LIKE', "%$search%")
                    ->orWhere('category_desc', 'LIKE', "%$search%");
            });
        }

        $categories = $categories->latest()->get();
        if (!$categories) {
            return response()->json([
                'success' => false,
                'message' => 'Không lấy được danh sách danh mục thành công'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Lấy danh sách danh mục thành công'
        ]);
    }


    /**
     * Lưu danh mục mới vào database.
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
            'category_name' => 'required|string|max:255',
            'category_desc' => 'nullable|string',
            'category_status' => 'required|integer',
            'category_slug' => 'nullable|string|max:255',
            'category_parent' => 'nullable|integer'
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Thêm danh mục thành công'
        ], 201);
    }


    /**
     * Hiển thị chi tiết danh mục.
     */
    // public function show($id)
    // {
    //     $category = Category::with(['parent', 'children', 'products'])->find($id);

    //     if (!$category) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Không tồn tại danh mục'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $category,
    //         'message' => 'Lấy danh mục thành công'
    //     ]);
    // }
    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'products']);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Lấy danh mục thành công'
        ]);
    }


    /**
     * Cập nhật thông tin danh mục.
     */
    public function update(Request $request, Category $category)
    {
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        // Kiểm tra nếu danh mục không tồn tại
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại danh mục'
            ], 404);
        }

        // Cập nhật thông tin danh mục
        $category->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Cập nhật danh mục thành công'
        ]);
    }

    /**
     * Xóa danh mục.
     */
    public function destroy(Request $request, Category $category)
    {
        // Kiểm tra token
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }

        // Kiểm tra nếu danh mục không tồn tại
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại danh mục'
            ], 404);
        }

        // Kiểm tra nếu danh mục có danh mục con
        if ($category->children->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa danh mục vì đang có danh mục con.'
            ], 400);
        }

        // Xóa danh mục
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa danh mục thành công'
        ]);
    }
    public function getBrandsBySlug($slug)
    {
        // Tìm category theo slug
        $category = Category::where('category_slug', $slug)->firstOrFail();

        // Lấy tất cả các brand liên quan tới sản phẩm thuộc category
        $brands = Product::where('category_id', $category->category_id)
            ->with('brand')
            ->get()
            ->pluck('brand') // Lấy brand từ quan hệ
            ->filter()       // Loại bỏ null nếu có
            ->unique('brand_id') // Loại trùng brand theo brand_id
            ->values()
            ->map(function ($brand) {
                return [
                    'brand_id' => $brand->brand_id,
                    'name' => $brand->brand_name,
                    'slug' => $brand->brand_slug
                ];
            });

        return response()->json($brands);
    }
    public function getProductsByCategoryAndBrand($category_slug)
    {
        try {
            // Tìm category theo slug
            $category = Category::where('category_slug', $category_slug)->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy danh mục',
                    'data' => null
                ], 404);
            }

            $brand_slug = request()->query('brand');
            $brand = null;

            if ($brand_slug) {
                $brand = Brand::where('brand_slug', $brand_slug)->first();

                if (!$brand) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy thương hiệu',
                        'data' => null
                    ], 404);
                }
            }

            $products = Product::where('category_id', $category->category_id)
                ->when($brand, function ($query) use ($brand) {
                    return $query->where('brand_id', $brand->brand_id);
                })
                ->with(['brand', 'category'])
                ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Không có sản phẩm nào phù hợp',
                    'data' => [],
                    'category' => $category->category_name,
                    'brand' => $brand->brand_name
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách sản phẩm thành công',
                'data' => $products,
                'category' => $category->category_name,
                'brand' => $brand->brand_name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getCategoryParent($category_slug)
    {
        $categories = Category::with(['children.products'])
            ->where('category_parent', 0)
            ->where('category_status', 1)
            ->where('category_slug', $category_slug)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Lấy category prarent thành công'
        ]);
    }
    // public function updateOrder(Request $request)
    // {
    //     $parents = $request->input('parents', []);
    //     $childrenMap = $request->input('children', []);

    //     // Cập nhật thứ tự cho danh mục cha
    //     foreach ($parents as $parentOrder => $parentId) {
    //         Category::where('category_id', $parentId)
    //             ->update(['category_order' => $parentOrder]);

    //         // Nếu có con thì cập nhật thứ tự cho từng con theo thứ tự trong mảng
    //         if (isset($childrenMap[$parentId])) {
    //             foreach ($childrenMap[$parentId] as $childOrder => $childId) {
    //                 Category::where('category_id', $childId)
    //                     ->update(['category_order' => $childOrder]);
    //             }
    //         }
    //     }

    //     return response()->json(['message' => 'Cập nhật thứ tự danh mục thành công!']);
    // }
    public function updateOrder(Request $request)
    {
        try {
            $parents = $request->input('parents', []);
            $childrenMap = $request->input('children', []);

            foreach ($parents as $item) {
                $parentId = $item['category_id'] ?? null;
                $order = $item['category_order'] ?? null;

                if (!$parentId || $order === null) continue;

                // Cập nhật danh mục cha
                Category::where('category_id', $parentId)->update([
                    'category_order' => $order,
                    'category_parent' => 0 // đảm bảo là cha
                ]);

                // Nếu có con thì cập nhật thứ tự từng con
                if (isset($childrenMap[$parentId]) && is_array($childrenMap[$parentId])) {
                    foreach ($childrenMap[$parentId] as $index => $childId) {
                        Category::where('category_id', $childId)->update([
                            'category_order' => $index + 1,
                            // 'category_parent' => $parentId
                        ]);
                    }
                }
            }

            return response()->json(['message' => 'Cập nhật thứ tự danh mục thành công!']);
        } catch (\Exception $e) {
            Log::error('Lỗi updateOrder: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi máy chủ: ' . $e->getMessage()], 500);
        }
    }
}
