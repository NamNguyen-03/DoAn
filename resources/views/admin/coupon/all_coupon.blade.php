@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-coupon')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm mã giảm giá
            </a>
            Liệt kê mã giảm giá
        </div>

        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs"></div>
            <div class="col-sm-4"></div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Tìm mã giảm giá...">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchCoupons()">Go</button>
                    </span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="couponTable">
                <thead>
                    <tr>
                        <th>Tên mã giảm giá</th>
                        <th>Mã giảm</th>
                        <th>Số lượng</th>
                        <th>Điều kiện</th>
                        <th>Số giảm</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div id="pagination" class="text-center" style="margin-top: 20px;"></div>
    </div>
</div>

<script>
    let allCoupons = [];
    let currentPage = 1;
    const perPage = 10;
    let searchQuery = "";

    function fetchCoupons() {
        const token = localStorage.getItem("admin_token");

        if (!token) {
            alert("Bạn chưa đăng nhập. Vui lòng đăng nhập lại.");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        const url = `{{ url('/api/coupons') }}?search=${encodeURIComponent(searchQuery)}`;

        fetch(url, {
                headers: {
                    "Authorization": "Bearer " + token
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    allCoupons = data.data;
                    renderCoupons(currentPage);
                    updatePagination();
                } else {
                    alert("Dữ liệu không hợp lệ!");
                }
            })
            .catch(error => {
                console.error("Lỗi khi fetch coupons:", error);
                alert("Không thể lấy dữ liệu từ server.");
            });
    }

    function renderCoupons(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const couponsToDisplay = allCoupons.slice(start, end);

        const tableBody = document.querySelector("#couponTable tbody");
        tableBody.innerHTML = "";

        if (couponsToDisplay.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='6' class='text-center'>Không có mã giảm giá nào.</td></tr>";
            return;
        }

        couponsToDisplay.forEach(coupon => {
            const conditionText = coupon.coupon_condition == 1 ? "Giảm theo %" : "Giảm theo tiền";

            const row = `
                <tr>
                    <td>${coupon.coupon_name}</td>
                    <td>${coupon.coupon_code}</td>
                    <td style="color:${coupon.coupon_qty==0?'red':(coupon.coupon_qty<5?'orange':'')}">${coupon.coupon_qty}</td>
                    <td>${conditionText}</td>
                    <td>${coupon.coupon_number}</td>
                    <td>
                        <a href="/admin/edit-coupon/${coupon.coupon_id}" class="active" style="margin-right: 10px;">
                            <i class="fa fa-pencil-square-o text-success text-active"></i>
                        </a>
                        <a href="javascript:void(0)" class="active" onclick="deleteCoupon(${coupon.coupon_id})">
                            <i class="fa fa-trash text-danger text"></i>
                        </a>
                    </td>
                </tr>`;
            tableBody.innerHTML += row;
        });
    }

    function updatePagination() {
        const totalPages = Math.ceil(allCoupons.length / perPage);
        const paginationDiv = document.getElementById("pagination");
        paginationDiv.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {
            const pageLink = document.createElement("a");
            pageLink.href = "#";
            pageLink.className = "page-link";
            pageLink.textContent = i;

            if (i === currentPage) {
                pageLink.classList.add("active");
            }

            pageLink.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = i;
                renderCoupons(currentPage);
                updatePagination();
            });

            paginationDiv.appendChild(pageLink);
        }
    }

    function searchCoupons() {
        searchQuery = document.getElementById("searchInput").value;
        currentPage = 1;
        fetchCoupons();
    }

    function deleteCoupon(couponId) {
        const token = localStorage.getItem("admin_token");
        if (!token) {
            alert("Bạn chưa đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        if (confirm("Bạn có chắc muốn xóa mã giảm giá này không?")) {
            fetch(`{{ url('/api/coupons') }}/${couponId}`, {
                    method: "DELETE",
                    headers: {
                        "Authorization": "Bearer " + token
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Đã xóa mã giảm giá thành công!");
                        fetchCoupons(); // Load lại danh sách
                    } else {
                        alert("Không thể xóa mã giảm giá: " + (data.message || "Lỗi không xác định."));
                    }
                })
                .catch(error => {
                    alert("Lỗi khi xóa mã giảm giá: " + error.message);
                });
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        fetchCoupons();

    });
</script>
<style>
    #pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .page-link {
        margin: 5px;
        padding: 10px 15px;
        text-decoration: none;
        color: #007bff;
        border: 1px solid #007bff;
        border-radius: 5px;
    }

    .page-link:hover {
        background-color: #007bff;
        color: white;
    }

    .page-link.active {
        background-color: #007bff;
        color: white;
        border: 1px solid #0056b3;
    }
</style>
@endsection