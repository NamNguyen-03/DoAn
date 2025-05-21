@extends('home.home_layout')
@section('content')

<section id="cart_items">
    <div class="container" style="width:100%;">
        <div class="">
            <ol class="breadcrumb">
                <li><a href="{{URL::to('/')}}">Trang chủ</a></li>
                <li class="active">Giỏ hàng</li>
            </ol>
        </div>

        <div class="table-responsive cart_info">
            <table class="table table-condensed">
                <thead>
                    <tr class="cart_menu">
                        <td class="image">Hình ảnh</td>
                        <td class="description">Tên sản phẩm</td>
                        <td class="price">Giá</td>
                        <td class="quantity">Số lượng</td>
                        <td class="total">Thành tiền</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody id="cart-body">
                    <!-- Dữ liệu sản phẩm sẽ được chèn vào đây -->
                </tbody>
            </table>
        </div>

        <div class="cart-actions">
            <button class="btn btn-default" id="clear-cart">Xóa tất cả</button>
            <h3>Tổng tiền: <span id="cart-total">0đ</span></h3>
            <a class="btn btn-primary" href="{{URL::to('/check-out')}}">Thanh toán</a>
        </div>
        <br>
    </div>
</section>

<script>
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');



    // function getProductSlug(productId) {
    //     return fetch(`/api/products/slug/${productId}`)
    //         .then(response => {
    //             if (!response.ok) {
    //                 throw new Error('Không thể lấy thông tin sản phẩm');
    //             }
    //             return response.json();
    //         })
    //         .then(data => {
    //             if (data.success) {
    //                 return data.data.product_slug; // Lấy slug từ API trả về
    //             } else {
    //                 throw new Error(data.message);
    //             }
    //         })
    //         .catch(error => {
    //             console.error('Lỗi khi lấy slug sản phẩm:', error);
    //             return null; // Nếu có lỗi, trả về null
    //         });
    // }

    function loadCart() {
        if (!userId) {
            document.getElementById("cart-body").innerHTML = "<tr><td colspan='6'>Bạn chưa đăng nhập!</td></tr>";
            return;
        }

        let cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];
        let cartBody = document.getElementById("cart-body");
        cartBody.innerHTML = "";

        if (cart.length === 0) {
            cartBody.innerHTML = "<tr><td colspan='6'>Giỏ hàng trống.</td></tr>";
            document.getElementById("cart-total").innerText = "0đ";
            return;
        }

        let totalPrice = 0;

        cart.forEach((item, index) => {
            let itemTotal = item.product_price * item.quantity;
            totalPrice += itemTotal;

            cartBody.innerHTML += `
            <tr>
                <td>
                    <a href="/product-details/${item.product_slug}">
                        <img src="{{ asset('uploads/product/') }}/${item.product_image}" width="70">
                    </a>
                </td>
                <td>
                    <a href="/product-details/${item.product_slug}">
                        ${item.product_name}
                    </a>
                </td>
                <td>${Number(item.product_price).toLocaleString()}đ</td>
                <td>
                    <input type="number" min="1" value="${item.quantity}" data-index="${index}" class="cart-quantity">
                </td>
                <td>${itemTotal.toLocaleString()}đ</td>
                <td>
                    <button class="btn btn-danger btn-sm remove-item" data-index="${index}">X</button>
                </td>
            </tr>
        `;
        });

        document.getElementById("cart-total").innerText = totalPrice.toLocaleString() + "đ";

        // Gán sự kiện thay đổi số lượng
        document.querySelectorAll(".cart-quantity").forEach(input => {
            input.addEventListener("change", updateQuantity);
        });

        // Gán sự kiện xóa sản phẩm
        document.querySelectorAll(".remove-item").forEach(button => {
            button.addEventListener("click", removeItem);
        });
    }


    function updateQuantity(event) {
        if (!userId) return;

        let cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];
        let index = event.target.getAttribute("data-index");
        let newQuantity = parseInt(event.target.value);

        if (newQuantity > 0) {
            cart[index].quantity = newQuantity;
        } else {
            cart.splice(index, 1); // Xóa sản phẩm nếu số lượng <= 0
        }

        localStorage.setItem(`cart_${userId}`, JSON.stringify(cart));
        loadCart(); // Cập nhật lại giao diện
    }

    function removeItem(event) {
        if (!userId) return;

        let cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];
        let index = event.target.getAttribute("data-index");

        cart.splice(index, 1);
        localStorage.setItem(`cart_${userId}`, JSON.stringify(cart));

        showNotification("Xóa sản phẩm thành công", "green");
        loadCart();
    }

    document.getElementById("clear-cart").addEventListener("click", () => {
        if (!userId) return;
        localStorage.removeItem(`cart_${userId}`);
        showNotification("Xóa giỏ hàng thành công", "green");
        loadCart();
    });

    document.addEventListener("DOMContentLoaded", loadCart);
</script>

@endsection