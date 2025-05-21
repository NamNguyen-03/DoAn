<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PostCateController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckSuperAdmin;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PromotionContentController;
use App\Http\Controllers\ContactEmailsController;
use App\Http\Middleware\CheckOtpApiToken;

Route::apiResource('users', UserController::class)->only(['store', 'index', 'show']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::apiResource('admins', AdminController::class)->only(['index', 'show']);
Route::post('/admin-login', [AdminAuthController::class, 'login']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('brands', BrandController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('banners', BannerController::class)->only(['index', 'show']);
Route::apiResource('coupons', CouponController::class)->only(['index', 'show']);
Route::apiResource('postcates', PostCateController::class)->only(['index', 'show']);
Route::apiResource('posts', PostController::class)->only(['index', 'show']);
Route::get('/galleries/{id}', [GalleryController::class, 'getGalleryByProduct']);
Route::get('/products/related/{category_id}', [ProductController::class, 'getRelatedProducts']);
Route::get('/shipping-fee', [ShippingController::class, 'calculate']);
Route::post('/apply-coupon', [CouponController::class, 'applyCoupon']);
Route::apiResource('comments', CommentController::class)->only(['index', 'show']);
Route::get('/comments/product/{product_id}', [CommentController::class, 'getByProductId']);
Route::put('/comments/status/{id}', [CommentController::class, 'updateStatus']);
Route::get('/ratings', [RatingController::class, 'getAverageRating']);
Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
Route::get('/products/slug/{product_id}', [ProductController::class, 'getProductById']);
Route::apiResource('/wishlist', WishlistController::class);
Route::apiResource('/videos', VideoController::class)->only(['index', 'show']);
Route::apiResource('/statistics', StatisticController::class)->only(['index', 'show']);
Route::post('/send-otp', [MailController::class, 'sendOtpToEmail']);
Route::post('/verify-otp', [MailController::class, 'verifyOtp']);
Route::post('/login/google', [SocialController::class, 'googleLogin']);
Route::post('/login/google/callback', [SocialController::class, 'handleGoogleLogin']);
Route::post('/login/facebook', [SocialController::class, 'loginWithFacebook']);
Route::post('/facebook-data-delete', [SocialController::class, 'handleDataDeletion']);
Route::get('/data-deletion-status', [SocialController::class, 'getDeletionStatus']);
Route::get('/category/{category_slug}', [CategoryController::class, 'getProductsByCategoryAndBrand']);
Route::get('/category/{category_slug}/brands', [CategoryController::class, 'getBrandsBySlug']);
Route::get('/get-products', [ProductController::class, 'getByIds']);
Route::get('/get-category-parent/{category_slug}', [CategoryController::class, 'getCategoryParent']);
Route::apiResource('/contact-us', ContactEmailsController::class)->only(['index', 'show', 'store']);
Route::apiResource('/promotions-content', PromotionContentController::class)->only(['index', 'show']);

// Các route cần đăng nhập bằng Sanctum
Route::middleware('auth:admins')->group(function () {
    Route::apiResource('users', UserController::class)->only(['update', 'destroy']);
    Route::post('/admin-logout', [AdminAuthController::class, 'logout']);
    Route::apiResource('admins', AdminController::class)->only(['update', 'destroy']);
    Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('brands', BrandController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy']);
    Route::put('/products/{id}/status', [ProductController::class, 'updateProductStatus']);
    Route::apiResource('banners', BannerController::class)->only(['store', 'update', 'destroy']);
    Route::put('/banners/{id}/status', [BannerController::class, 'updateBannerStatus']);
    Route::apiResource('coupons', CouponController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('postcates', PostCateController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('posts', PostController::class)->only(['store', 'update', 'destroy']);
    Route::put('/posts/{id}/status', [PostController::class, 'updatePostStatus']);
    Route::post('/gallery/{id}/upload-multiple', [GalleryController::class, 'uploadMultiple']);
    Route::delete('/gallery/delete/{id}', [GalleryController::class, 'deleteGallery']);
    Route::apiResource('comments', CommentController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('orders', OrderController::class)->only(['store', 'update']);
    Route::put('/order/{order_code}/status', [OrderController::class, 'updateOrderStatus']);
    Route::apiResource('/videos', VideoController::class)->only(['store', 'update', 'destroy']);
    Route::post('/admin/change-password', [AdminAuthController::class, 'changeAdminPassword']);
    Route::post('/admin/verify-password', [AdminAuthController::class, 'verifyAdminPass']);
    Route::apiResource('/contact-us', ContactEmailsController::class)->only(['destroy']);
    Route::apiResource('/promotions-content', PromotionContentController::class)->only(['store', 'update', 'destroy']);
    Route::post('/promotions-send', [MailController::class, 'promotionSend']);
    Route::post('/categories/update-order', [CategoryController::class, 'updateOrder']);
    Route::post('/brands/update-order', [BrandController::class, 'updateBrandOrder']);
});
Route::middleware(['auth:admins', CheckSuperAdmin::class,])->group(function () {
    Route::post('/create-admin', [AdminController::class, 'createAdmin']);
    Route::apiResource('orders', OrderController::class)->only(['destroy']);
});
Route::middleware('auth:api')->group(function () {
    Route::apiResource('orders', OrderController::class)->only(['store', 'update']);
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);
    Route::post('/user/verify-password', [AuthController::class, 'verifyPass']);
    Route::post('/send-order-email', [MailController::class, 'sendOrderEmail']);
    Route::post('/create-vnpay-url', [OrderController::class, 'createVnpayUrl']);
});
Route::middleware([CheckOtpApiToken::class])->group(function () {
    Route::post('/change-password', [AuthController::class, 'changeForgotPassword']);
});
// Route::get('/categories/{category_id}/products', [ProductController::class, 'getProductsByCategory']);
// Route::get('/brands/{brand_id}/products', [ProductController::class, 'getProductsByBrand']);






// Route::get('/admin', [AdminAuthController::class, 'admin']);
// Route::post('/admin-logout', [AdminAuthController::class, 'logout']);






// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
