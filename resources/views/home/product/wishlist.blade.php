@extends('home.home_layout')
@section('content')
<section class="orders-section py-5">
    <div class="container" style="width:100%">

        <h2 style="margin-bottom: 20px;">Sản phẩm bạn đã thích</h2>

        <div class="product-list">



        </div>

    </div>

</section>

<script>
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    const userId = localStorage.getItem("user_id");
    document.addEventListener("DOMContentLoaded", () => {
        const userId = localStorage.getItem("user_id");

        if (!userId) {
            window.location.href = "/login";
            return;
        }

        reloadWishlist();
    });

    function reloadWishlist() {
        fetch(`/api/users/${userId}`)
            .then(res => res.json())
            .then(data => {
                const container = document.querySelector(".product-list");
                if (data.success && data.data.wishlist.length > 0) {
                    container.innerHTML = "";

                    data.data.wishlist.forEach(item => {
                        const product = item.product;
                        const price = Number(product.product_price).toLocaleString("vi-VN") + " ₫";
                        const productHtml = `
                        <div class="product-item">
                            <div class="product-left">
                                <img src="/uploads/product/${product.product_image}" alt="${product.product_name}">
                                <div class="product-info">
                                    <a href="/product-details/${product.product_slug}">${product.product_name}</a>
                                    <ul>
                                        <li>Mã sản phẩm: ${product.product_id}</li>
                                        <li>Số lượng: ${product.product_quantity}</li>
                                        <li>Đã bán: ${product.product_sold}</li>
                                    </ul>
                                </div>
                            </div>
                            <div>
                                <div class="product-price">${price}</div>
                                <button class="remove-btn" data-wishlist_id="${item.wishlist_id}" onclick="deleteWishlist(${item.wishlist_id})">✖</button>
                            </div>
                        </div>
                    `;
                        container.insertAdjacentHTML("beforeend", productHtml);
                    });
                } else {
                    container.innerHTML = "<p>Không có sản phẩm nào trong danh sách yêu thích.</p>";
                }
            })
            .catch(error => {
                console.error("Lỗi khi reload wishlist:", error);
                document.querySelector(".product-list").innerHTML = "<p>Có lỗi xảy ra khi tải lại danh sách yêu thích.</p>";
            });
    }

    function deleteWishlist(wishlistID) {


        if (!userId || !token) {
            alert("Vui lòng đăng nhập để xóa sản phẩm yêu thích.");
            return;
        }

        // if (!confirm("Bạn có chắc muốn xóa sản phẩm này khỏi danh sách yêu thích?")) return;


        swal({
            title: "Bạn xác nhận xóa sản phẩm này khỏi yêu thích?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Tiếp tục",
            cancelButtonText: "Không",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                fetch(`/api/wishlist/${wishlistID}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            // 'Authorization': 'Bearer ' + token
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            swal("Thành công", "Đã xóa sản phẩm khỏi yêu thích!", "success");
                            reloadWishlist();
                        } else {
                            swal("Lỗi", "Không thể xóa sản phẩm yêu thích!", "error");
                        }
                    })
                    .catch(err => {
                        console.error("Lỗi khi xoá sản phẩm yêu thích:", err);
                        swal("Lỗi", "Có lỗi xảy ra khi xoá!", "error");
                    });
            } else {
                swal("Đã hủy", "", "error");
            }
        });

    }
</script>

<style>
    .product-list {
        width: 100%;
    }

    .product-item {
        display: flex;
        border-bottom: 1px solid #ddd;
        padding: 15px 0;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .product-left {
        display: flex;
        gap: 15px;
        align-items: flex-start;
        flex: 1;
    }

    .product-left img {
        width: 120px;
        height: auto;
        border: 1px solid #ccc;
    }

    .product-info a {
        font-weight: bold;
        color: #0066cc;
        text-decoration: none;
    }

    .product-info a:hover {
        text-decoration: underline;
    }

    .product-info ul {
        padding-left: 20px;
        margin: 5px 0;
    }

    .product-price {
        color: red;
        font-weight: bold;
        font-size: 18px;
        white-space: nowrap;
        margin-bottom: 10px;
    }

    .remove-btn {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #999;
    }

    .remove-btn:hover {
        color: red;
    }
</style>
@endsection