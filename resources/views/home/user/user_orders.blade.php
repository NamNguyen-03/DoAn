@extends('home.user_layout')
@section('mini_content')
<section class="orders-section py-5">
    <div class="container" style="width:100%">
        <h4 class="mb-4 text-center">Danh sách đơn hàng đã đặt</h4>
        <div class="table-responsive">
            <table class="table table-bordered text-center shadow-sm">
                <thead class="table-light text-center">
                    <tr>
                        <th class="text-center">STT</th>
                        <th class="text-center">Tên người nhận</th>
                        <th class="text-center">Ngày đặt hàng</th>
                        <th class="text-center">Số lượng sản phẩm</th>
                        <th class="text-center">Tình trạng</th>
                        <th class="text-center" style="width: 40px;"></th>
                    </tr>
                </thead>
                <tbody id="order-table-body">
                    <!-- Dữ liệu sẽ được render bằng JS -->
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.getElementById('order-table-body');


        if (!userId || !token) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-danger">Vui lòng đăng nhập để xem đơn hàng.</td></tr>';
            return;
        }

        fetch(`/api/users/${userId}`)
            .then(res => res.json())
            .then(data => {
                const orders = data.data.orders;

                if (!orders || orders.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-muted">Chưa có đơn hàng nào.</td></tr>';
                    return;
                }

                orders.forEach((order, index) => {
                    const row = document.createElement('tr');
                    const customerName = order.shipping?.customer_name || 'Không có';
                    const createdAt = order.created_at ? new Date(order.created_at).toLocaleString() : '---';
                    const productCount = order.order_details ? order.order_details.length : 0;
                    const orderStatus = (() => {
                        switch (order.order_status) {
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
                            default:
                                return 'Không xác định';
                        }
                    })();
                    row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${customerName}</td>
                    <td>${createdAt}</td>
                    <td>${productCount}</td>
                    <td>${orderStatus}</td>
                    <td>
                        <a href="/order_details/${order.order_code}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                `;
                    tbody.appendChild(row);
                });
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="5" class="text-danger">Lỗi khi tải đơn hàng.</td></tr>';
            });
    });
</script>

<style>
    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .order-card {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        border-left: 5px solid #28a745;
        transition: all 0.3s ease;
    }

    .order-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .order-info div {
        margin-bottom: 8px;
        font-size: 14px;
    }

    .order-info strong {
        color: #333;
        width: 130px;
        display: inline-block;
    }

    .order-actions {
        text-align: right;
    }

    .order-actions .btn {
        border-radius: 30px;
        padding: 6px 18px;
        font-size: 14px;
    }

    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
        background-color: #fff;
    }

    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .table tbody tr:hover {
        background-color: #f1f3f5;
        transition: background-color 0.2s ease;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }
</style>
@endsection