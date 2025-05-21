<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Middleware\CheckOtpVerified;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('home.home'); // Trả về giao diện Blade
})->name('home.home');
Route::get('/users', function () {
    return view('users.all_user'); // Trả về giao diện Blade
})->name('users.all_user');
Route::get('/users/{id}', function () {
    return view('users.user_details'); // Trả về giao diện Blade
})->name('users.user_details');



//ADMIN


Route::get('/admin-login', function () {
    return view('admin.login.admin_login');
})->name('login');
Route::get('/admin-register', function () {
    return view('admin.login.admin_register'); // Trả về giao diện Blade
})->name('admin.login.admin_register');





// Route cho trang dashboard


Route::get('/admin/dashboard', function () {
    return view('admin.dashboard'); // Trả về giao diện Blade
})->name('admin.dashboard');
Route::get('/admin', function () {
    return view('admin.dashboard'); // Trả về giao diện Blade
})->name('admin.dashboard');
Route::get('/admin/profile', function () {
    return view('admin.admin.admin_profile'); // Trả về giao diện Blade
})->name('admin.admin.admin_profile');




// Category
Route::get('/admin/all-category', function () {
    return view('admin.category.all_category'); // Trả về giao diện Blade
})->name('admin.category.all_category');
Route::get('/admin/add-category', function () {
    return view('admin.category.add_category'); // Trả về giao diện Blade
})->name('admin.category.add_category');
Route::get('/admin/edit-category/{category_slug}', function ($category_slug) {
    return view('admin.category.edit_category', ['category_slug' => $category_slug]);
})->name('admin.category.edit_category');

// Brand
Route::get('/admin/all-brand', function () {
    return view('admin.brand.all_brand'); // Trả về giao diện Blade
})->name('admin.brand.all_brand');
Route::get('/admin/add-brand', function () {
    return view('admin.brand.add_brand'); // Trả về giao diện Blade
})->name('admin.brand.add_brand');
Route::get('/admin/edit-brand/{brand_slug}', function ($brand_slug) {
    return view('admin.brand.edit_brand', ['brand_slug' => $brand_slug]);
})->name('admin.brand.edit_brand');

// Product
Route::get('/admin/all-product', function () {
    return view('admin.product.all_product'); // Trả về giao diện Blade
})->name('admin.product.all_product');
Route::get('/admin/add-product', function () {
    return view('admin.product.add_product');
})->name('admin.product.add_product');
Route::get('/admin/edit-product/{product_slug}', function ($product_slug) {
    return view('admin.product.edit_product', ['product_slug' => $product_slug]);
})->name('admin.product.edit_product');
Route::get('/admin/product-gallery/{id}', function ($id) {
    return view('admin.gallery.add_gallery', ['product_id' => $id]);
})->name('admin.gallery.add_gallery');


// Banner
Route::get('/admin/all-banner', function () {
    return view('admin.banner.all_banner'); // Trả về giao diện Blade
})->name('admin.banner.all_banner');
Route::get('/admin/add-banner', function () {
    return view('admin.banner.add_banner'); // Trả về giao diện Blade
})->name('admin.banner.add_banner');
Route::get('/admin/edit-banner/{id}', function ($id) {
    return view('admin.banner.edit_banner', ['banner_id' => $id]); // Trả về giao diện Blade
})->name('admin.banner.edit_banner');


//  Coupon
Route::get('/admin/all-coupon', function () {
    return view('admin.coupon.all_coupon'); // Trả về giao diện Blade
})->name('admin.coupon.all_coupon');
Route::get('/admin/add-coupon', function () {
    return view('admin.coupon.add_coupon'); // Trả về giao diện Blade
})->name('admin.coupon.add_coupon');
Route::get('/admin/edit-coupon/{id}', function ($id) {
    return view('admin.coupon.edit_coupon', ['coupon_id' => $id]); // Trả về giao diện Blade
})->name('admin.coupon.edit_coupon');

// Shipping
Route::get('/admin/all-shipping', function () {
    return view('admin.shipping.all_shipping'); // Trả về giao diện Blade
})->name('admin.shipping.all_shipping');
Route::get('/admin/add-shipping', function () {
    return view('admin.shipping.add_shipping'); // Trả về giao diện Blade
})->name('admin.shipping.add_shipping');
Route::get('/admin/edit-shipping/{id}', function ($id) {
    return view('admin.shipping.edit_shipping', ['shipping_id' => $id]); // Trả về giao diện Blade
})->name('admin.shipping.edit_shipping');

//Post_category
Route::get('/admin/all-post-cate', function () {
    return view('admin.post_category.all_post_cate'); // Trả về giao diện Blade
})->name('admin.post_category.all_post_cate');
Route::get('/admin/add-post-cate', function () {
    return view('admin.post_category.add_post_cate'); // Trả về giao diện Blade
})->name('admin.post_category.add_post_cate');
Route::get('/admin/edit-post-cate/{cate_post_slug}', function ($cate_post_slug) {
    return view('admin.post_category.edit_post_cate', ['cate_post_slug' => $cate_post_slug]); // Trả về giao diện Blade
})->name('admin.post_category.edit_post_cate');


//Post
Route::get('/admin/all-post', function () {
    return view('admin.post.all_post'); // Trả về giao diện Blade
})->name('admin.post.all_post');
Route::get('/admin/add-post', function () {
    return view('admin.post.add_post'); // Trả về giao diện Blade
})->name('admin.post.add_post');
Route::get('/admin/edit-post/{post_slug}', function ($post_slug) {
    return view('admin.post.edit_post', ['post_slug' => $post_slug]); // Trả về giao diện Blade
})->name('admin.post.edit_post');

//Comment
Route::get('/admin/all-comment', function () {
    return view('admin.comment.all_comment'); // Trả về giao diện Blade
})->name('admin.comment.all_comment');
//Order
Route::get('/admin/orders', function () {
    return view('admin.order.orders'); // Trả về giao diện Blade
})->name('admin.order.orders');
Route::get('/admin/order-details/{order_code}', function ($order_code) {
    return view('admin.order.order_details', ['order_code' => $order_code]); // Trả về giao diện Blade
})->name('admin.order.order_details');

Route::get('/admin/add-video', function () {
    return view('admin.videos.add_video'); // Trả về giao diện Blade
})->name('admin.videos.add_video');
Route::get('/admin/all-videos', function () {
    return view('admin.videos.all_videos'); // Trả về giao diện Blade
})->name('admin.videos.all_videos');
Route::get('/admin/edit-video/{video_slug}', function ($video_slug) {
    return view('admin.videos.edit_video', ['video_slug' => $video_slug]); // Trả về giao diện Blade
})->name('admin.videos.edit_video');

Route::get('/orders/{order_code}/pdf', [OrderController::class, 'downloadPdf']);

Route::get('/admin/all-promotion-content', function () {
    return view('admin.promotional.promotion_content');
})->name('admin.promotional.promotion_content');

Route::get('/admin/promotion-email', function () {
    return view('admin.promotional.customer_promotional');
})->name('admin.promotional.customer_promotional');

Route::get('/admin/add-promotion-content', function () {
    return view('admin.promotional.add_promotion_content');
})->name('admin.promotional.add_promotion_content');

Route::get('/admin/edit-promotion-content/{id}', function ($id) {
    return view('admin.promotional.edit_promotion_content', ['id' => $id]);
})->name('admin.promotional.edit_promotion_content');

//ADMIN

//HOME
// Route::get('/home', function () {
//     return view('home.home'); // Trả về giao diện Blade
// })->name('home.home');
Route::get('/products', function () {
    return view('home.product.all_product'); // Trả về giao diện Blade
})->name('home.product.all_product');

Route::get('/login', function () {
    return view('home.user.home_login'); // Trả về giao diện Blade
})->name('home.user.home_login');

Route::get('/cart', function () {
    return view('home.cart.show_cart'); // Trả về giao diện Blade
})->name('home.cart.show_cart');


Route::get('/category/{category_slug}', function ($category_slug) {
    return view('home.category.show_category_product', ['category_slug' => $category_slug]); // Trả về giao diện Blade
})->name('home.category.show_category_product');

Route::get('/brand/{brand_slug}', function ($brand_slug) {
    return view('home.brand.show_brand_product', ['brand_slug' => $brand_slug]); // Trả về giao diện Blade
})->name('home.brand.show_brand_product');

Route::get('/search', function () {
    return view('home.product.search_product'); // Trả về giao diện Blade
})->name('home.product.search_product');
Route::get('/tag/{tag}', function ($tag) {
    return view('home.product.tag_product', ['tag' => $tag]); // Trả về giao diện Blade
})->name('home.product.tag_product');

Route::get('/post-cate/{cate_post_slug}', function ($cate_post_slug) {
    return view('home.post.post_cate', ['cate_post_slug' => $cate_post_slug]); // Trả về giao diện Blade
})->name('home.post.post_cate');

Route::get('/post/{post_slug}', function ($post_slug) {
    return view('home.post.post', ['post_slug' => $post_slug]); // Trả về giao diện Blade
})->name('home.post.post');

Route::get('/product-details/{product_slug}', function ($product_slug) {
    return view('home.product.details_product', ['product_slug' => $product_slug]);
})->name('home.product.details_product');

Route::get('/check-out', function () {
    return view('home.payment.checkout'); // Trả về giao diện Blade
})->name('home.payment.checkout');

Route::get('/check-out-completed', function () {
    return view('home.payment.checkout_completed'); // Trả về giao diện Blade
})->name('home.payment.checkout');

Route::get('/orders', function () {
    return view('home.payment.orders'); // Trả về giao diện Blade
})->name('home.payment.orders');

Route::get('/order_details/{order_code}', function ($order_code) {
    return view('home.payment.user_order_details', ['order_code' => $order_code]);
})->name('home.payment.user_order_details');

Route::get('/account/info', function () {
    return view('home.user.user_info'); // Trả về giao diện Blade
})->name('home.user.user_info');

Route::get('/account/info-edit', function () {
    return view('home.user.edit_user_info'); // Trả về giao diện Blade
})->name('home.user.edit_user_info');

Route::get('/account/user_comments', function () {
    return view('home.user.user_comments'); // Trả về giao diện Blade
})->name('home.user.comments');

Route::get('/account/user_orders', function () {
    return view('home.user.user_orders'); // Trả về giao diện Blade
})->name('home.user.orders');

Route::get('/account/user_wishlist', function () {
    return view('home.user.user_wishlist'); // Trả về giao diện Blade
})->name('home.user.wishlist');

Route::get('/wishlist', function () {
    return view('home.product.wishlist'); // Trả về giao diện Blade
})->name('home.product.wishlist');

Route::get('/account/user_changePassword', function () {
    return view('home.user.user_changepass'); // Trả về giao diện Blade
})->name('home.user.changePassword');

Route::get('/videos', function () {
    return view('home.videos.all_videos'); // Trả về giao diện Blade
})->name('home.videos.all_videos');

Route::get('/video/{video_slug}', function ($video_slug) {
    return view('home.videos.video', ['video_slug' => $video_slug]); // Trả về giao diện Blade
})->name('home.videos.video');

Route::get('/forgot-password', function () {
    return view('home.user.forgot_password'); // Trả về giao diện Blade
})->name('home.user.forgot_password');

Route::get('/change-password', function () {
    return view('home.user.change_password'); // Trả về giao diện Blade
})->name('home.email.change_password');
Route::get('/change-password', function () {
    return view('home.user.change_password'); // Trả về giao diện Blade
})->name('home.email.change_password');

Route::view('/privacy-policy', 'privacy-policy');
// Route::get('/category/{category_slug}?brands={brand_slug}', function ($category_slug) {
//     return view('home.category.category_brands', ['category_slug' => $category_slug]); // Trả về giao diện Blade
// })->name('home.category.category_brands');
Route::get('/category/{category_slug}/brand/{brand_slug}', function ($category_slug, $brand_slug) {
    return view('home.category.category_brands', [
        'category_slug' => $category_slug,
        'brand_slug' => $brand_slug
    ]);
})->name('home.category.category_brands');

Route::get('/compare', function () {
    return view('home.product.compare_product'); // Trả về giao diện Blade
})->name('home.product.compare_product');

Route::get('/category-parent/{category_slug}', function ($category_slug) {
    return view('home.category.show_categories', ['category_slug' => $category_slug,]);
})->name('home.category.show_categories');


Route::get('/introduction', function () {
    return view('home.post.intro'); // Trả về giao diện Blade
})->name('home.post.intro');

Route::get('/track-order', function () {
    return view('home.track_order'); // Trả về giao diện Blade
})->name('home.track_order');

Route::get('/contact-us', function () {
    return view('home.contact.contact_us'); // Trả về giao diện Blade
})->name('home.contact.contact_us');

Route::get('/thank-you', function () {
    return view('home.contact.contact_thankyou'); // Trả về giao diện Blade
})->name('home.contact.contact_thankyou');

Route::get('/thank-you', function () {
    return view('home.contact.contact_thankyou'); // Trả về giao diện Blade
})->name('home.contact.contact_thankyou');

Route::get('/terms', function () {
    return view('home.footer.terms'); // Trả về giao diện Blade
})->name('home.footer.terms');

Route::get('/buy-guide', function () {
    return view('home.footer.buy_guide'); // Trả về giao diện Blade
})->name('home.footer.buy_guide');

Route::get('/payment-guide', function () {
    return view('home.footer.payment_guide'); // Trả về giao diện Blade
})->name('home.footer.payment_guide');

Route::get('/feedback', function () {
    return view('home.footer.feedback'); // Trả về giao diện Blade
})->name('home.footer.feedback');

Route::get('/complaint', function () {
    return view('home.footer.complaint'); // Trả về giao diện Blade
})->name('home.footer.complaint');
