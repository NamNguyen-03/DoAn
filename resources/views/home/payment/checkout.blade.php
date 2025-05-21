@extends('home.home_layout')
@section('content')

<section id="cart_items">
    <div class="container" style="width:100%;">
        <div class="">
            <ol class="breadcrumb">
                <li><a href="{{URL::to('/')}}">Trang chủ</a></li>
                <li class="active">Thanh toán giỏ hàng</li>
            </ol>
        </div>

        <div class="register-req">
            <p>Vui lòng đăng ký hoặc đăng nhập trước khi thanh toán và xem lịch sử mua hàng dễ dàng hơn.</p>
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

        </div>
        <div class="row">
            <!-- Cột 1: Thanh toán -->
            <div class="col-sm-4">
                <h3>Thanh toán</h3>
                <div class="total_area">
                    <ul>
                        <li>Tổng: <span></span></li>
                        <li>Thuế 8%: <span></span></li>
                        <li>Phí vận chuyển: <span id="shipping_fee"></span></li>
                        <li>Mã giảm giá: <span></span></li>
                        <li>Thành tiền: <span></span></li>
                    </ul>
                    <form method="POST">
                        <input type="text" class="form-control" name="coupon" id="coupon-input" placeholder="Nhập mã giảm giá"><br>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-default" id="apply-coupon">Thêm mã</button>
                            <a class="btn btn-warning" href="#" id="clear_coupon">Xóa mã</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cột 2: Tính phí vận chuyển -->
            <div class="col-sm-4">
                <h3>Tính phí ship</h3>
                <form class="shipping-form">
                    <div class="form-group">
                        <label>Chọn thành phố</label>
                        <select name="city" id="city" class="form-control select2 choose city">
                            <option value="">--Chọn--</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Chọn quận huyện</label>
                        <select name="province" id="province" class="form-control select2 choose province">
                            <option value="">--Chọn--</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Chọn xã phường</label>
                        <select name="ward" id="ward" class="form-control select2 choose ward">
                            <option value="">--Chọn--</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-info calculate_delivery">Tính phí</button>
                </form>
            </div>

            <!-- Cột 3: Thông tin giao hàng -->
            <div class="col-sm-4 checkout-section">
                <h3>Thông tin giao hàng</h3>
                <form method="POST">
                    <div class="form-box">
                        <input type="text" name="shipping_email" id="shipping_email" placeholder="Email*">
                    </div>
                    <div class="form-box">
                        <input type="text" name="shipping_name" id="shipping_name" placeholder="Họ và tên">
                    </div>
                    <div class="form-box">
                        <input type="text" name="shipping_phone" id="shipping_phone" placeholder="Số điện thoại">
                    </div>
                    <div class="form-box">
                        <textarea id="shipping_address" name="shipping_address" rows="3" placeholder="Địa chỉ"></textarea>
                    </div>
                    <div class="form-box">
                        <textarea name="shipping_note" rows="3" id="shipping_note" placeholder="Ghi chú"></textarea>
                    </div>




                    <div class="form-box">
                        <label>Hình thức thanh toán:</label>
                        <select name="payment_method" class="payment_select" id="payment_method">
                            <option value="0">---Chọn---</option>
                            <option value="1">VNPAY</option>
                            <option value="2">Tiền mặt</option>
                        </select>
                    </div>

                    <div class="button-box">
                        <button type="button" class="confirm-order-btn">Xác nhận đơn hàng</button>
                    </div>
                </form>


            </div>
        </div>
        <br>
    </div>
</section>
<script>
    function initSelect2() {
        $('.select2').select2({
            width: '100%',
            placeholder: "--Chọn--",
            allowClear: true
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        initSelect2(); // Khởi tạo ban đầu
    });
</script>

<script>
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    const userId = localStorage.getItem("user_id") || sessionStorage.getItem('user_id');

    // Hàm hiển thị thông báo
    function showNotification(message, bgColor) {
        let alertBox = document.createElement("div");
        alertBox.textContent = message;
        alertBox.style.position = "fixed";
        alertBox.style.top = "10px";
        alertBox.style.left = "50%";
        alertBox.style.transform = "translateX(-50%)";
        alertBox.style.background = bgColor;
        alertBox.style.color = "white";
        alertBox.style.padding = "10px 20px";
        alertBox.style.borderRadius = "5px";
        alertBox.style.zIndex = "9999";
        alertBox.style.boxShadow = "0px 4px 6px rgba(0,0,0,0.1)";
        document.body.appendChild(alertBox);

        setTimeout(function() {
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 500);
        }, 3000);
    }

    document.addEventListener("DOMContentLoaded", function() {
        const registerReqDiv = document.querySelector(".register-req");
        if (!token) {
            showNotification("Vui lòng đăng nhập trước khi thanh toán", "red"); // Màu xanh lá
        } else {
            // Ẩn thông báo đăng ký nếu đã có token
            if (registerReqDiv) {
                registerReqDiv.style.display = "none";
            }
            loadCheckoutCart();


        }
    });



    function loadCheckoutCart() {

        const tbody = document.querySelector(".cart_info tbody");
        const totalArea = document.querySelector(".total_area ul");

        if (!userId || !token) {
            tbody.innerHTML = "<tr><td colspan='6'>Bạn chưa đăng nhập!</td></tr>";
            return;
        }

        let cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];
        tbody.innerHTML = "";

        if (cart.length === 0) {
            tbody.innerHTML = "<tr><td colspan='6'>Giỏ hàng trống.</td></tr>";

            // return;
        }

        let totalPrice = 0;

        cart.forEach((item, index) => {
            let itemTotal = item.product_price * item.quantity;
            totalPrice += itemTotal;

            tbody.innerHTML += `
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

        // Gán sự kiện sau khi render
        document.querySelectorAll(".cart-quantity").forEach(input => {
            input.addEventListener("change", updateQuantity);
        });

        document.querySelectorAll(".remove-item").forEach(button => {
            button.addEventListener("click", removeItem);
        });

        // Tính tổng tiền
        let discountDisplay = 0;

        let discountAmount = Number(localStorage.getItem(`discount_amount_${userId}`)) || 0;
        let discountType = Number(localStorage.getItem(`discount_type_${userId}`)) || 0;
        if (discountType === 1) {
            discountDisplay = Math.round((totalPrice * discountAmount) / 100);
        } else {
            discountDisplay = discountAmount;
        }
        let totalAfterDis = totalPrice - discountDisplay;

        let tax = totalAfterDis * 0.08;
        let shippingFee = Number(localStorage.getItem(`shipping_fee_${userId}`)) || 0;






        let finalTotal = totalAfterDis + tax + shippingFee;
        if (finalTotal < 0) finalTotal = 0;

        totalArea.innerHTML = `
        <li>Tổng: <span>${totalPrice.toLocaleString()}đ</span></li>
        <li>Thuế 8%: <span>${tax.toLocaleString()}đ</span></li>
        <li>Phí vận chuyển: <span id="shipping_fee">${shippingFee.toLocaleString()}đ</span></li>
        <li>Mã giảm giá: <span id="discount">-${discountDisplay.toLocaleString()}đ</span></li>
        <li>Thành tiền: <span>${finalTotal.toLocaleString()}đ</span></li>
    `;

        getAndDisplayAddress(userId);
    }

    function getAndDisplayAddress(userId) {
        // Lấy địa chỉ từ localStorage (giả sử localStorage lưu ở key "shipping_address_userId")
        const addressKey = `shipping_address_${userId}`;
        const address = localStorage.getItem(addressKey);

        if (address) {
            // Làm sạch các thành phần trong địa chỉ
            const cleanedAddress = address.split(",").map(item => cleanName(item)).join(", ");

            // Hiển thị địa chỉ đã làm sạch vào textarea
            const shippingAddressTextarea = document.getElementById("shipping_address");
            if (shippingAddressTextarea) {
                shippingAddressTextarea.value = cleanedAddress;
            }
        } else {
            console.error("Không tìm thấy địa chỉ trong localStorage");
        }
    }

    // Cập nhật số lượng
    function updateQuantity(event) {
        if (!userId) return;

        let cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];
        let index = event.target.getAttribute("data-index");
        let newQuantity = parseInt(event.target.value);

        if (newQuantity > 0) {
            cart[index].quantity = newQuantity;
        } else {
            cart.splice(index, 1);
        }

        localStorage.setItem(`cart_${userId}`, JSON.stringify(cart));
        loadCheckoutCart();
    }

    function removeItem(event) {
        if (!userId) return;

        let cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];
        let index = event.target.getAttribute("data-index");

        cart.splice(index, 1);
        localStorage.setItem(`cart_${userId}`, JSON.stringify(cart));
        loadCheckoutCart();
        showNotification("Xóa sản phẩm thành công", "green");

    }

    document.getElementById("clear-cart").addEventListener("click", () => {
        if (!userId) return;

        localStorage.removeItem(`cart_${userId}`);
        loadCheckoutCart();
    });

    const host = "https://provinces.open-api.vn/api/";
    let fixedAddressPart = "";

    const callAPI = (api, target) => {
        fetch(api)
            .then(response => response.json())
            .then(data => {
                if (target === "city") {
                    renderSelect(data, "city");
                } else if (target === "province") {
                    renderSelect(data.districts, "province");
                } else if (target === "ward") {
                    renderSelect(data.wards, "ward");
                }
            })
            .catch(error => console.error("Fetch error:", error));
    };

    const renderSelect = (array, selectId) => {
        const select = $("#" + selectId);
        if (select.hasClass("select2-hidden-accessible")) {
            select.select2("destroy");
        }
        let html = '<option value="" disabled selected>--Chọn--</option>';
        array.forEach(item => {
            html += `<option data-id="${item.code}" value="${item.name}">${item.name}</option>`;
        });
        select.html(html);
        select.select2({
            width: '100%',
            placeholder: "--Chọn--",
            allowClear: true
        });
    };

    const resetSelect = (selectId) => {
        const select = $("#" + selectId);
        if (select.hasClass("select2-hidden-accessible")) {
            select.select2("destroy");
        }
        select.html('<option value="" disabled selected>--Chọn--</option>');
        select.select2({
            width: '100%',
            placeholder: "--Chọn--",
            allowClear: true
        });
    };

    const cleanName = (name) => {
        return name.replace(/Tỉnh|Thành phố|Quận|Huyện|Thị xã|Thị trấn|Phường|Xã/gi, "").trim();
    };


    document.addEventListener("DOMContentLoaded", () => {
        callAPI(`${host}?depth=1`, "city");

        $("#city").on("change", function() {
            const cityId = $(this).find(":selected").data("id");
            callAPI(`${host}p/${cityId}?depth=2`, "province");
            resetSelect("province");
            resetSelect("ward");
        });

        $("#province").on("change", function() {
            const districtId = $(this).find(":selected").data("id");
            callAPI(`${host}d/${districtId}?depth=2`, "ward");
            resetSelect("ward");
        });

        $("#ward").on("change", function() {});
    });
    //XÁC NHẬN THANH TOÁN
    //nút xác nhận thanh toán
    let isSubmitting = false; // Biến khóa click

    document.querySelector('.confirm-order-btn').addEventListener('click', () => {
        if (isSubmitting) return;

        if (!userId) {
            showNotification("Vui lòng đăng nhập trước khi thanh toán", "red");
            setTimeout(() => {
                window.location.href = "/home-login";
            }, 2000);
            return;
        }

        const cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];
        const couponCode = localStorage.getItem(`coupon_code_${userId}`) || null;
        const discountText = document.getElementById('discount').textContent || "0";
        const discountValue = parseInt(discountText.replace(/[^\d]/g, '')) || 0;
        const shippingFee = Number(localStorage.getItem(`shipping_fee_${userId}`)) || 0;

        if (!shippingFee) {
            swal({
                title: "Cảnh báo",
                text: "<span style='color:red;'>Vui lòng tính phí vận chuyển trước khi thanh toán!</span>",
                type: "warning",
                html: true
            });
            return;
        }

        const shippingName = document.getElementById('shipping_name').value.trim();
        const shippingEmail = document.getElementById('shipping_email').value.trim();
        const shippingPhone = document.getElementById('shipping_phone').value.trim();
        const shippingAddress = document.getElementById('shipping_address').value.trim();
        const shippingNote = document.getElementById('shipping_note').value.trim();
        const paymentMethod = parseInt(document.getElementById('payment_method').value);

        if (!shippingName || !shippingEmail || !shippingPhone || !shippingAddress || paymentMethod === 0) {
            swal({
                title: "Cảnh báo",
                text: "<span style='color:red;'>Vui lòng điền đầy đủ thông tin và chọn phương thức thanh toán trước khi xác nhận!</span>",
                type: "warning",
                html: true
            });
            return;
        }

        const orderDetails = cart.map(item => ({
            product_id: parseInt(item.product_id),
            product_name: item.product_name,
            product_price: Number(item.product_price),
            product_quantity: parseInt(item.quantity)
        }));

        const totalProductPrice = cart.reduce((sum, item) => {
            return sum + Number(item.product_price) * parseInt(item.quantity);
        }, 0);

        const subtotalAfterDiscount = totalProductPrice - discountValue;
        const tax = subtotalAfterDiscount * 0.08;
        const totalAmount = Math.round(subtotalAfterDiscount + tax + shippingFee);

        const body = {
            customer_id: parseInt(userId),
            order_coupon: couponCode ? couponCode + "-" + discountValue : null,
            order_ship: shippingFee,
            order_status: 0,
            order_total: totalAmount,
            shipping: {
                customer_name: shippingName,
                shipping_address: shippingAddress,
                shipping_phone: shippingPhone,
                shipping_email: shippingEmail,
                shipping_method: paymentMethod,
                shipping_note: shippingNote
            },
            order_details: orderDetails
        };

        swal({
            title: "Bạn xác nhận đặt hàng?",
            text: "",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Tiếp tục",
            cancelButtonText: "Không",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                if (isSubmitting) return; // kiểm tra lại lần nữa
                isSubmitting = true;

                // Disable nút confirm ngay lập tức
                const confirmBtn = document.querySelector('.sweet-alert .confirm');
                if (confirmBtn) {
                    confirmBtn.disabled = true;
                    confirmBtn.innerText = "Đang xử lý...";
                }
                console.log(body);
                localStorage.setItem(`body_${userId}`, JSON.stringify(body));
                // if (paymentMethod === 1) {
                //     fetch('/api/create-vnpay-url', {
                //             method: 'POST',
                //             headers: {
                //                 'Content-Type': 'application/json',
                //                 'Authorization': `Bearer ${token}`
                //             },
                //             body: JSON.stringify(body)
                //         })
                //         .then(res => res.json())
                //         .then(data => {
                //             if (data.code === '00' && data.vnpUrl) {
                //                 localStorage.setItem(`body_${userId}`) = body;
                //                 window.location.href = data.vnpUrl;
                //             } else {
                //                 showNotification("Không tạo được liên kết thanh toán VNPAY!", "red");
                //                 isSubmitting = false;
                //             }
                //         })
                //         .catch(error => {
                //             console.error("Lỗi tạo VNPAY URL:", error);
                //             showNotification("Lỗi khi kết nối VNPAY!", "red");
                //             isSubmitting = false;
                //         });

                // } else if (paymentMethod === 2) {
                //     fetch('/api/orders', {
                //             method: 'POST',
                //             headers: {
                //                 'Content-Type': 'application/json',
                //                 'Authorization': `Bearer ${token}`
                //             },
                //             body: JSON.stringify(body)
                //         })
                //         .then(res => res.json())
                //         .then(data => {
                //             if (data.success) {
                //                 swal({
                //                     title: "Thành công",
                //                     text: "<span style='color:green;'>Đặt hàng thành công!</span>",
                //                     type: "success",
                //                     html: true
                //                 });
                //                 localStorage.removeItem(`cart_${userId}`);
                //                 localStorage.removeItem(`coupon_code_${userId}`);
                //                 localStorage.removeItem(`discount_amount_${userId}`);
                //                 localStorage.removeItem(`discount_type_${userId}`);
                //                 localStorage.removeItem(`shipping_fee_${userId}`);
                //                 localStorage.removeItem(`shipping_address_${userId}`);

                //                 const orderCode = data.data.order_code
                //                 sendOrderEmail(shippingEmail, orderCode);
                //                 setTimeout(() => {
                //                     window.location.href = "/check-out-completed";
                //                 }, 1000);
                //             } else {
                //                 showNotification("Có lỗi xảy ra khi đặt hàng!", "red");
                //                 console.error(data);
                //                 isSubmitting = false;
                //             }
                //         })
                //         .catch(error => {
                //             console.error("Lỗi khi gửi đơn hàng:", error);
                //             showNotification("Lỗi máy chủ. Vui lòng thử lại sau.", "red");
                //             isSubmitting = false;
                //         });
                // }


            } else {
                swal("Đã hủy", "", "error");
            }
        });
    });

    function sendOrderEmail(email, orderCode) {
        fetch(`/api/send-order-email`, {
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${token}`
                },
                body: JSON.stringify({
                    email: email,
                    order_code: orderCode
                })
            }).then(res => res.json())
            .then(data => {
                console.log("Email đã được gửi")
            })
            .catch(error => {
                console.error("Lỗi: " + error)
            })
    }


    document.querySelector('.calculate_delivery').addEventListener('click', () => {
        const cleanedCity = cleanName($("#city").val());
        const cleanedDistrict = cleanName($("#province").val());
        const cleanedWard = cleanName($("#ward").val());
        const city = $("#city option:selected").text();
        const district = $("#province option:selected").text();
        const ward = $("#ward option:selected").text();
        // Lấy phí vận chuyển cũ nếu có

        fetch(`/api/shipping-fee?city=${encodeURIComponent(cleanedCity)}&district=${encodeURIComponent(cleanedDistrict)}&ward=${encodeURIComponent(cleanedWard)}`)
            .then(res => res.json())
            .then(data => {
                if (typeof data.fee === "number") {
                    const newFee = data.fee;
                    const fullAddress = `${city}, ${district}, ${ward}`;
                    localStorage.setItem(`shipping_address_${userId}`, fullAddress);
                    localStorage.setItem(`shipping_fee_${userId}`, newFee);
                    loadCheckoutCart();
                    showNotification("Tính phí vận chuyển thành công!", "green");
                } else if (data.error) {
                    document.querySelector("#shipping_fee").textContent = `Lỗi: ${data.message}`;
                } else {
                    document.querySelector("#shipping_fee").textContent = "Không xác định phí";
                    console.warn("Phản hồi không có phí hợp lệ:", data);
                }
            })
            .catch((error) => {
                document.querySelector("#shipping_fee").textContent = "Lỗi khi gửi yêu cầu";
                console.error("Shipping fee error:", error);
            });
    });

    document.getElementById("apply-coupon").addEventListener("click", function() {
        const code = document.getElementById("coupon-input").value.trim();
        if (!code) {
            showNotification("Vui lòng nhập mã giảm giá", "orange");
            return;
        }

        fetch(`/api/apply-coupon`, {
                method: "POST",
                headers: {
                    'Content-type': 'application/json'
                },
                body: JSON.stringify({
                    coupon_code: code,
                    user_id: userId
                }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.expired == false) {
                        localStorage.setItem(`coupon_code_${userId}`, code);
                        localStorage.setItem(`discount_amount_${userId}`, data.amount);
                        localStorage.setItem(`discount_type_${userId}`, data.type);
                        loadCheckoutCart();
                        showNotification(`Áp dụng mã thành công: -${data.amount.toLocaleString()}${data.type == 2 ? 'đ' : '%'}`, "green");
                    } else {
                        showNotification(data.message, "red");

                    }

                } else {
                    showNotification(data.message || "Mã giảm giá không hợp lệ", "red");
                }
                document.getElementById("coupon-input").value = '';

            })
            .catch(err => {
                console.error("Coupon error:", err);
                showNotification("Lỗi khi kiểm tra mã giảm giá", "red");
            });
    });


    document.getElementById("clear_coupon").addEventListener("click", function(e) {
        e.preventDefault();

        let couponCode = localStorage.getItem(`coupon_code_${userId}`);
        let discountAmount = Number(localStorage.getItem(`discount_amount_${userId}`)) || 0;
        let discountType = Number(localStorage.getItem(`discount_type_${userId}`)) || 0;

        if (!couponCode) {
            showNotification("Chưa có mã giảm giá nào được áp dụng!", "orange");
            return;
        }

        localStorage.removeItem(`coupon_code_${userId}`);
        localStorage.removeItem(`discount_amount_${userId}`);
        localStorage.removeItem(`discount_type_${userId}`);

        loadCheckoutCart();

        document.querySelector("#coupon-input").value = "";

        showNotification("Đã xóa mã giảm giá", "blue");
    });
</script>



<style>
    .total_area ul {
        list-style: none;
        padding: 0;
        margin: 0 0 15px 0;
    }

    .total_area ul li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        background-color: #f0f0f0;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 15px;
    }

    .total_area form .form-control {
        width: 100%;
    }

    .total_area .d-flex {
        display: flex;
        gap: 10px;
        margin-top: 5px;
    }

    .total_area .btn {
        padding: 6px 12px;
        font-size: 14px;
    }
</style>


@endsection