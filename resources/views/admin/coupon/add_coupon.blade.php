@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{url('/admin/dashboard') }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{url('/admin/all-coupon')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách mã
                </a>
                Thêm mã giảm giá
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form role="form" class="form-validate" action="" id="addCouponForm" method="post">

                        <div class="form-group">
                            <label for="exampleInputEmail1">Tên mã giảm giá</label>
                            <input type="text" name="coupon_name" class="form-control" id="coupon_name" placeholder="Tên mã giảm">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Mã giảm giá</label>
                            <input type="text" name="coupon_code" class="form-control" id="coupon_code" placeholder="Mã giảm giá">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Số lượng mã</label>
                            <input type="text" name="coupon_qty" class="form-control" id="coupon_qty" placeholder="Mã giảm giá">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Ngày hết hạn</label>
                            <div class="input-clearable" style="width:20%">
                                <input type="text" id="coupon_date" name="coupon_date" class="form-control">
                                <span class="clear-btn" onclick="document.getElementById('coupon_date').value = ''">&times;</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Tính năng</label>
                            <select name="coupon_condition" class="form-control input-sm m-bot15">
                                <option value="0">---Chọn---</option>
                                <option value="1">Giảm theo %</option>
                                <option value="2">Giả theo tiền</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Nhập số % hoặc tiền giảm</label>
                            <input type="text" name="coupon_number" class="form-control" id="coupon_number" placeholder="Mã giảm giá">
                        </div>
                        <button type="submit" name="add_coupon" class="btn btn-info">Thêm mã</button>
                    </form>
                </div>

            </div>
        </section>

    </div>
</div>
<script>
    document.querySelector("#addCouponForm").addEventListener("submit", function(event) {
        event.preventDefault();
        let formData = new FormData(this);

        let adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";

            return;
        }
        const couponCondition = formData.get("coupon_condition");
        if (couponCondition === "0") {
            alert("Vui lòng chọn tính năng giảm giá hợp lệ!");
            return;
        }
        console.log(Object.fromEntries(formData))
        fetch("{{ url('/api/coupons') }}", {
                method: "POST",
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + adminToken
                }
            })
            .then(response => {
                if (response.status === 401) {
                    alert("Chưa đăng nhập, vui lòng đăng nhập!");
                    window.location.href = "{{ url('admin-login') }}";
                    return;
                }

                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    alert("Thêm coupon thành công");
                    window.location.href = "{{ url('/admin/all-coupon') }}";
                } else {
                    alert("Có lỗi xảy ra");
                }
            })
            .catch(error => console.error("Lỗi khi thêm coupon:", error));
    });
    $(document).ready(function() {
        $("#coupon_date").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: 1
        });
    });
</script>
@endsection