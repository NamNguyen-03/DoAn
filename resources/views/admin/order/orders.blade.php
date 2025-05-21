@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{ url('/admin') }}">
                <img src="{{ asset('backend/images/back.png') }}" alt="Back"
                    style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <span>Liệt kê đơn hàng</span>
        </div>

        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <!-- Optional bulk actions -->
            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchOrders()">Go</button>
                    </span>
                </div>
            </div>

        </div>

        <div class="table-responsive">
            <table class="table table-striped b-t b-light">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>OrderCode</th>
                        <th>Tên khách</th>
                        <th>Địa chỉ</th>
                        <th>Thời gian đặt</th>
                        <th>Tình trạng</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <!-- Dữ liệu sẽ được render bằng fetch -->
                </tbody>
            </table>
        </div>
        <div id="pagination" class="text-center" style="margin-top: 20px;"></div>

    </div>
</div>

<script>
    let searchQuery = '';
    let ordersData = []; // Lưu toàn bộ đơn hàng từ API
    const ordersPerPage = 10;
    let currentPage = 1;

    function getStatusText(status) {
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
                return 'Đã hoàn';
            default:
                return 'Không rõ';
        }
    }

    function renderOrdersPage(page) {
        const tbody = document.getElementById('orderTableBody');
        tbody.innerHTML = '';

        const start = (page - 1) * ordersPerPage;
        const end = start + ordersPerPage;
        const paginatedOrders = ordersData.slice(start, end);

        if (paginatedOrders.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align: center;">Không có đơn hàng nào.</td></tr>`;
            return;
        }

        paginatedOrders.forEach((order, index) => {
            const customerName = order.shipping?.customer_name || '---';
            const shippingAddress = order.shipping?.shipping_address || '---';
            const createdAt = order.created_at || '---';
            const orderStatusText = getStatusText(order.order_status);
            const orderCode = order.order_code || '---';
            let statusColor = '';
            switch (order.order_status) {
                case 0:
                    statusColor = '#ff8040';
                    break;
                case 1:
                    statusColor = '#00a7d1';
                    break;
                case 2:
                    statusColor = '#0050b9';
                    break;
                case 3:
                    statusColor = '#06b900';
                    break;
                case 4:
                    statusColor = 'grey';
                    break;
            }
            const row = `
            <tr>
                <td>${start + index + 1}</td>
                <td>${orderCode}</td>
                <td>${customerName}</td>
                <td>${shippingAddress}</td>
                <td>${createdAt}</td>
                <td style="color:${statusColor}">${orderStatusText}</td>
                <td>
                    <a href="/admin/order-details/${orderCode}" class="active">
                        <i class="fa fa-eye text-success text-active" style="font-size: 18px;"></i>
                    </a>
                    <a onclick="deleteOrder('${order.order_code}')" href="javascript:void(0)" class="active">
                        <i class="fa fa-trash text"></i>
                    </a>
                </td>
            </tr>
        `;
            tbody.innerHTML += row;
        });

        renderPagination();
    }

    function renderPagination() {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        const totalPages = Math.ceil(ordersData.length / ordersPerPage);

        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('a');
            pageBtn.textContent = i;
            pageBtn.href = 'javascript:void(0)';
            pageBtn.className = 'page-link' + (i === currentPage ? ' active' : '');
            pageBtn.onclick = () => {
                currentPage = i;
                renderOrdersPage(currentPage);
            };
            pagination.appendChild(pageBtn);
        }
    }

    function fetchOrders() {
        const url = `{{ url('/api/orders') }}?search=${encodeURIComponent(searchQuery)}`;

        fetch(url)
            .then(res => res.json())
            .then(resData => {
                if (resData.success) {
                    ordersData = resData.data;
                    currentPage = 1;
                    renderOrdersPage(currentPage);
                } else {
                    document.getElementById('orderTableBody').innerHTML = `
                        <tr><td colspan="7" style="text-align: center;">${resData.message || 'Không có đơn hàng nào.'}</td></tr>
                    `;
                    document.getElementById('pagination').innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Lỗi khi tải đơn hàng:', error);
                document.getElementById('orderTableBody').innerHTML = `
                    <tr><td colspan="7" style="text-align: center;">Đã xảy ra lỗi khi tải đơn hàng.</td></tr>
                `;
                document.getElementById('pagination').innerHTML = '';
            });
    }

    function searchOrders() {
        searchQuery = document.getElementById('searchInput').value;
        currentPage = 1;
        fetchOrders();
    }

    // Tự fetch tất cả đơn hàng khi trang load
    document.addEventListener('DOMContentLoaded', () => {
        fetchOrders();
    });

    // Cho phép nhấn Enter để tìm
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            searchOrders();
        }
    });

    function deleteOrder(orderCode) {
        const adminToken = localStorage.getItem("admin_token");
        if (!adminToken) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa đơn hàng này không?")) {
            fetch(`{{ url('/api/orders/') }}/${orderCode}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": "Bearer " + adminToken,
                        // "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        alert("Token không hợp lệ. Bạn cần đăng nhập lại.");
                        window.location.href = "/admin-login";
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Xóa đơn hàng thành công!");
                        fetchOrders();
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa đơn hàng."));
                    }
                })
                .catch(error => alert(error.message));
        }
    }
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