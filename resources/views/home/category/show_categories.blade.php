@extends('home.home_layout')
@section('sidebar_content')
@include('home.home_sidebar')
@endsection
@section('content')
<div class="col-sm-9 padding-right">
    <div class="features_items" style="min-height: 300px;">
        <h2 class="title text-center" id="category-parent-title"></h2>
        <!-- Hiển thị danh mục con -->
        <div class="category-parent-slider-container">
            <div id="category_child" class="category_child-slider">

            </div>
        </div>


        <br>

        <!-- Slider sản phẩm của từng danh mục con -->
        <div id="product_slider_container">
            <!-- JS sẽ render nhiều slider sản phẩm tương ứng ở đây -->
        </div>
    </div>
</div>
<script>
    const categorySlug = `{{$category_slug}}`
    document.addEventListener("DOMContentLoaded", function() {
        fetch(`/api/get-category-parent/${categorySlug}`)
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    const data = response.data;
                    console.log(data);

                    const categoryChildContainer = document.getElementById('category_child');
                    const productSliderContainer = document.getElementById('product_slider_container');
                    document.getElementById('category-parent-title').innerHTML = data[0].category_name;

                    data.forEach(parent => {
                        // Lọc và sắp xếp category con
                        const visibleChildren = (parent.children || [])
                            .filter(child => child.category_status === 1)
                            .sort((a, b) => a.category_order - b.category_order);

                        visibleChildren.forEach(child => {
                            // 1. Render tên danh mục con
                            const childDiv = document.createElement('div');
                            childDiv.className = 'category-child_name';
                            childDiv.textContent = child.category_name;
                            childDiv.addEventListener('click', () => {
                                window.location.href = `/category/${child.category_slug}`;
                            });
                            categoryChildContainer.appendChild(childDiv);

                            // 2. Render slider sản phẩm tương ứng
                            const sliderSection = document.createElement('div');
                            sliderSection.innerHTML = `
                        <div class="product_slider_header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0;">
                            <h2 style="margin: 0; font-size: 30px;">${child.category_name}</h2>
                            <a href="/category/${child.category_slug}" style="color: #1a73e8; text-decoration: none;">Xem tất cả</a>
                        </div>
                        <div class="swiper product_swiper">
                            <div class="swiper-wrapper">
                                ${child.products.map(product => `
                                    <div class="swiper-slide">
                                        <div class="product-image-wrapper" style="height:530px">
                                            <div class="single-products">
                                                <div class="productinfo text-center">
                                                    <form>
                                                        <input type="hidden" value="${product.product_id}" class="cart_product_id_${product.product_id}">
                                                        <input type="hidden" value="${product.product_name}" class="cart_product_name_${product.product_id}">
                                                        <input type="hidden" value="${product.product_slug}" class="cart_product_slug_${product.product_id}">
                                                        <input type="hidden" value="${product.product_image}" class="cart_product_image_${product.product_id}">
                                                        <input type="hidden" value="${product.product_quantity}" class="cart_product_quantity_${product.product_id}">
                                                        <input type="hidden" value="${product.product_price}" class="cart_product_price_${product.product_id}">
                                                        <input type="hidden" value="1" class="cart_product_qty_${product.product_id}">

                                                        <a href="/product-details/${product.product_slug}">
                                                            <img src="/uploads/product/${product.product_image}" alt="${product.product_name}" />
                                                            <h2>${new Intl.NumberFormat('vi-VN').format(product.product_price)}đ</h2>
                                                            <p>${product.product_name.length > 50 ? product.product_name.substr(0, 50) : product.product_name}</p>
                                                        </a>
                                                        <button data-id_product="${product.product_id}" type="button" class="btn btn-default add-to-cart">Thêm giỏ hàng</button>
                                                        <button type="button" class="btn btn-default quick-view"
                                                            data-toggle="modal" data-target="#quickview"
                                                            onclick="loadQuickViewProduct('${encodeURIComponent(product.product_slug)}')">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="choose">
                                                <ul class="nav nav-pills nav-justified">
                                                    <li><a href="#" class="add-to-wishlist" data-product_id="${product.product_id}"><i class="fa fa-heart"></i> Yêu thích</a></li>
                                                    <li><a href="#" class="add-to-compare" data-product_id="${product.product_id}" data-product_image="${product.product_image}" data-product_name="${product.product_name}"><i class="fa fa-plus-square"></i> So sánh</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    `;

                            productSliderContainer.appendChild(sliderSection);

                            new Swiper('.product_swiper', {
                                slidesPerView: 4,
                                spaceBetween: 20,
                                navigation: {
                                    nextEl: '.swiper-button-next',
                                    prevEl: '.swiper-button-prev',
                                },
                                breakpoints: {
                                    0: {
                                        slidesPerView: 1
                                    },
                                    768: {
                                        slidesPerView: 2
                                    },
                                    1024: {
                                        slidesPerView: 4
                                    },
                                }
                            });
                        });
                    });
                }
            })
            .catch(error => console.error('Lỗi khi gọi API:', error));
    });
</script>
<style>
    .category-parent-slider-container {
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .category_child-slider {
        display: inline-flex;
        gap: 10px;
    }

    .category-child_name {
        flex: 0 0 auto;
        padding: 8px 16px;
        border: 1px solid #ccc;
        border-radius: 20px;
        background-color: #f8f8f8;
        cursor: pointer;
        white-space: nowrap;
        transition: background-color 0.3s;
    }

    .category-child_name:hover {
        background-color: #e0e0e0;
    }
</style>
@endsection