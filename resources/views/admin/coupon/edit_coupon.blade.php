@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ URL::previous() }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px; width: 40px; height: 40px;">
                </a>
                Cập nhật mã giảm giá
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="updateCouponForm" role="form" class="form-validate" action="" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="coupon_name">Tên mã giảm giá</label>
                            <input type="text" name="coupon_name" class="form-control" id="coupon_name" placeholder="Tên mã giảm" required>
                        </div>
                        <div class="form-group">
                            <label for="coupon_code">Mã giảm giá</label>
                            <input type="text" name="coupon_code" class="form-control" id="coupon_code" placeholder="Mã giảm giá" required>
                        </div>
                        <div class="form-group">
                            <label for="coupon_qty">Số lượng mã</label>
                            <input type="number" name="coupon_qty" class="form-control" id="coupon_qty" placeholder="Số lượng" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Ngày hết hạn</label>
                            <div class="input-clearable" style="width:20%">
                                <input type="text" id="coupon_date" name="coupon_date" class="form-control">
                                <span class="clear-btn" onclick="document.getElementById('coupon_date').value = ''">&times;</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="coupon_condition">Tính năng</label>
                            <select name="coupon_condition" class="form-control input-sm m-bot15" id="coupon_condition" required>
                                <option value="0">---Chọn---</option>
                                <option value="1">Giảm theo %</option>
                                <option value="2">Giảm theo tiền</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="coupon_number">Nhập số % hoặc tiền giảm</label>
                            <input type="text" name="coupon_number" class="form-control" id="coupon_number" placeholder="Số giảm" required>
                        </div>

                        <button type="submit" class="btn btn-info">Cập nhật</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const couponId = "{{ $coupon_id }}";

        if (!couponId) {
            alert("Không tìm thấy ID mã giảm giá.");
            window.location.href = "/admin/all-coupon";
            return;
        }

        // Lấy dữ liệu không cần token
        fetch(`/api/coupons/${couponId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    populateCouponForm(data.data);
                } else {
                    alert("Không tìm thấy mã giảm giá.");
                    window.location.href = "/admin/all-coupon";
                }
            })
            .catch(error => {
                console.error("Lỗi khi lấy mã giảm giá:", error);
                alert("Lỗi khi tải dữ liệu.");
            });

        function populateCouponForm(coupon) {
            document.getElementById("coupon_name").value = coupon.coupon_name || "";
            document.getElementById("coupon_code").value = coupon.coupon_code || "";
            document.getElementById("coupon_qty").value = coupon.coupon_qty || "";
            document.getElementById("coupon_condition").value = coupon.coupon_condition || "0";
            document.getElementById("coupon_number").value = coupon.coupon_number || "";
            document.getElementById("coupon_date").value = coupon.coupon_date || "";

        }

        // Submit form với token
        document.getElementById("updateCouponForm").addEventListener("submit", function(e) {
            e.preventDefault();

            const token = localStorage.getItem("admin_token");
            if (!token) {
                alert("Bạn cần đăng nhập để cập nhật mã giảm giá.");
                window.location.href = "/admin-login";
                return;
            }

            const body = {
                coupon_name: document.getElementById("coupon_name").value,
                coupon_code: document.getElementById("coupon_code").value,
                coupon_qty: document.getElementById("coupon_qty").value,
                coupon_condition: document.getElementById("coupon_condition").value,
                coupon_number: document.getElementById("coupon_number").value,
                coupon_date: document.getElementById("coupon_date").value
            };

            fetch(`/api/coupons/${couponId}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${token}`
                    },
                    body: JSON.stringify(body)
                })
                .then(res => {
                    if (res.status === 401) {
                        alert("Token không hợp lệ. Vui lòng đăng nhập lại.");
                        window.location.href = "/admin-login";
                        return;
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Cập nhật mã giảm giá thành công!");
                        window.location.href = "/admin/all-coupon";
                    } else {
                        alert("Cập nhật thất bại: " + (data.message || "Lỗi không xác định."));
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi cập nhật mã giảm giá:", error);
                    alert("Có lỗi xảy ra khi cập nhật.");
                });
        });
    });
    $(document).ready(function() {
        $("#coupon_date").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: 1
        });
    });
</script>

@endsection