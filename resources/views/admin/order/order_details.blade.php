@extends('admin.admin_layout')
@section('admin_content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{ url('/admin/orders') }}">
                <img src="{{ asset('backend/images/back.png') }}" alt="Back"
                    style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <span>Chi tiết đơn hàng</span> <img id="statusImage" src="" alt="Status Image" style="width: 60px; height: 60px; margin-left: 10px;">
            <button class="btn btn-primary" id="print_pdf" style="float:right;margin-right:10px;margin-top:12px">In PDF</button>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <h4><strong>Mã đơn hàng:</strong> <span id="orderCode"></span></h4>
                    <h4><strong>Tên khách hàng:</strong> <span id="customerName"></span></h4>
                    <h4><strong>Địa chỉ:</strong> <span id="orderAddress"></span></h4>
                    <h4><strong>Phí ship:</strong> <span id="orderShip"></span></h4>
                    <h4><strong>Thời gian đặt:</strong> <span id="orderDate"></span></h4>
                </div>
                <div class="col-md-6">
                    <h4>
                        <strong>Tình trạng:</strong>
                        <span id="orderStatus"></span>

                    </h4>
                    <h4><strong>Phương thức thanh toán:</strong> <span id="paymentMethod"></span></h4>
                    <h4><strong>Mã giảm giá:</strong> <span id="orderCoupon"></span></h4>
                    <h4><strong>Tổng tiền(Đã bào gồm 8% thuế):</strong> <span id="orderTotal"></span></h4>
                    <div id="actionButtons" style="margin-top: 20px;"></div>
                </div>


            </div>

            <h4>Sản phẩm trong đơn hàng:</h4>
            <table class="table table-striped b-t b-light">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng trong kho</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
                <tbody id="orderItemsTableBody">
                    <!-- Các sản phẩm sẽ được render ở đây -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const orderCode = "{{ $order_code }}"; // Nhận order_code từ Blade
        if (!orderCode) {
            alert("Không tìm thấy đơn hàng.");
            window.location.href = "/admin/orders";
            return;
        }

        // Gọi hàm fetchOrderDetails để lấy dữ liệu đơn hàng
        fetchOrderDetails(orderCode);
    });

    // Hàm để fetch dữ liệu đơn hàng
    function fetchOrderDetails(orderCode) {
        fetch(`/api/orders/${orderCode}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const order = data.data;
                    const orderDetails = order.order_details;
                    let productTotal = 0;

                    // Tính tổng tiền sản phẩm
                    orderDetails.forEach(item => {
                        productTotal += item.product_price * item.product_quantity;
                    });
                    console.log(productTotal);

                    // Mã giảm giá (giá trị là số tiền hoặc %)
                    let discount = 0;
                    let coupon = "";
                    if (order.order_coupon) {
                        const parts = order.order_coupon.split("-");
                        if (parts.length === 2 && !isNaN(parts[1])) {
                            coupon = parts[0];
                            discount = parseFloat(parts[1]);
                        }
                    }

                    const afterDiscount = productTotal - discount;

                    // Tính thuế 8%
                    const tax = afterDiscount * 0.08;

                    // Phí ship
                    const ship = parseFloat(order.order_ship);

                    // Tổng tiền cuối cùng
                    const grandTotal = afterDiscount + tax + ship;

                    // Hiển thị tổng tiền
                    document.getElementById("orderTotal").textContent = grandTotal.toLocaleString("vi-VN") + " đ";

                    // Các thông tin đơn hàng khác
                    document.getElementById("orderCode").textContent = order.order_code;
                    document.getElementById("customerName").textContent = order.shipping.customer_name;
                    document.getElementById("orderAddress").textContent = order.shipping.shipping_address;
                    document.getElementById("orderShip").textContent = ship.toLocaleString("vi-VN") + " đ";
                    document.getElementById("orderDate").textContent = order.created_at;
                    document.getElementById("orderStatus").textContent = getOrderStatus(order.order_status);
                    document.getElementById("paymentMethod").textContent = order.shipping.shipping_method === 1 ? 'Thanh toán VNPAY (Đã thanh toán)' : order.shipping.shipping_method === 0 ? 'Thanh toán khi nhận hàng' : "";
                    document.getElementById("orderCoupon").textContent = discount > 0 ?
                        `${coupon} (-${discount.toLocaleString("vi-VN")} đ)` :
                        'Không có';
                    displayActionButtons(order.order_status);
                    console.log(order.order_status);
                    const statusImage = document.getElementById('statusImage');
                    const statusImages = {
                        0: "{{ asset('backend/images/processing.png') }}",
                        1: "{{ asset('backend/images/processed.png') }}",
                        2: "{{ asset('backend/images/delivering.png') }}",
                        3: "{{ asset('backend/images/completed.png') }}",
                        4: "{{ asset('backend/images/canceled.png') }}",
                        5: "{{ asset('backend/images/returned.png') }}"
                    };
                    statusImage.src = statusImages[parseInt(order.order_status)] || "{{ asset('backend/images/unknown.png') }}";
                    // Hiển thị sản phẩm trong đơn hàng
                    const orderItemsTableBody = document.getElementById("orderItemsTableBody");
                    orderItemsTableBody.innerHTML = ""; // Clear bảng trước khi thêm
                    orderDetails.forEach((item, index) => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.product_name}</td>
                            <td>${item.products.product_quantity}</td>
                            <td>${item.product_quantity}</td>
                            <td>${parseInt(item.product_price).toLocaleString("vi-VN")} đ</td>
                            <td>${(item.product_quantity * item.product_price).toLocaleString("vi-VN")} đ</td>
                        `;
                        orderItemsTableBody.appendChild(row);
                    });
                } else {
                    console.error("Lỗi khi lấy thông tin đơn hàng:", data.message);
                }
            })
            .catch(err => {
                console.error("Lỗi kết nối API:", err);
            });
    }

    // Hàm để chuyển đổi trạng thái đơn hàng từ mã (0, 1, 2...) thành tên trạng thái
    function getOrderStatus(status) {
        switch (status) {
            case 0:
                return 'Đang xử lý';
            case 1:
                return 'Đã xác nhận';
            case 2:
                return 'Đang giao';
            case 3:
                return 'Hoàn tất';
            case 4:
                return 'Đã hủy';
            case 5:
                return 'Hoàn hàng';
            default:
                return 'Không rõ';
        }
    }

    function displayActionButtons(status) {
        const actionButtonsDiv = document.getElementById("actionButtons");

        // Xóa tất cả nút cũ trước khi thêm các nút mới
        actionButtonsDiv.innerHTML = '';

        if (status === 0) { // Đang xử lý
            actionButtonsDiv.innerHTML = `
                    <button class="btn btn-success" onclick="orderStatusUpdate(1)">Xác nhận</button>
                    <button class="btn btn-danger" onclick="orderStatusUpdate(4)">Hủy đơn</button>
                `;
        } else if (status === 1) { // Đã xác nhận
            actionButtonsDiv.innerHTML = `
                    <button class="btn btn-primary" onclick="orderStatusUpdate(2)">Giao hàng</button>
                    <button class="btn btn-danger" onclick="orderStatusUpdate(4)">Hủy đơn</button>
                `;
        } else if (status === 2) { // Đang giao
            actionButtonsDiv.innerHTML = `
                    <button class="btn btn-success" onclick="orderStatusUpdate(3)">Hoàn tất</button>
                    <button class="btn btn-danger" onclick="orderStatusUpdate(5)">Hoàn hàng</button>
                `;
        }
    }

    function orderStatusUpdate(status) {
        const orderCode = "{{ $order_code }}";
        let adminToken = localStorage.getItem('admin_token');
        if (!adminToken) {
            alert("Vui lòng đăng nhập lại.");
            return;
        }

        // Thêm xác nhận nếu status là 4 (hủy đơn)
        if (status == 4) {
            const confirmCancel = confirm("Bạn có chắc chắn muốn hủy đơn hàng này?");
            if (!confirmCancel) {
                return;
            }
        } else if (status == 5) {
            const confirmCancel = confirm("Bạn có chắc chắn muốn hoàn đơn hàng này?");
            if (!confirmCancel) {
                return;
            }
        }

        fetch(`/api/order/${orderCode}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${adminToken}`
                },
                body: JSON.stringify({
                    order_status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (status == 1) {
                        alert("Đơn hàng đã được xác nhận!");
                    } else if (status == 2) {
                        alert("Đơn hàng đã giao!");
                    } else if (status == 3) {
                        alert("Đơn hàng đã hoàn tất!");
                    } else if (status == 4) {
                        alert("Đơn hàng đã hủy!");
                    } else if (status == 5) {
                        alert("Đơn hàng đã hoàn!");
                    }
                    window.location.reload();
                } else {
                    alert("Có lỗi khi xác nhận đơn hàng.");
                    console.error(data.message);
                }
            })
            .catch(err => {
                console.error("Lỗi kết nối API:", err);
            });
    }

    document.getElementById("print_pdf").addEventListener("click", function() {
        const orderCode = "{{ $order_code }}";
        window.open(`/orders/${orderCode}/pdf`, '_blank');
    });
</script>
<style>
    .row h4 {
        margin-bottom: 15px;
        /* Thêm khoảng cách dưới mỗi h4 */
    }

    /* Căn chỉnh tốt hơn cho các phần tử h4 */
    .row h4 strong {
        font-weight: bold;
    }

    /* Tạo không gian cho các cột trong .row */
    .col-md-6 {
        margin-bottom: 20px;
        /* Cách đều các phần tử trong các cột */
    }
</style>
@endsection