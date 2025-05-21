@extends('home.home_layout')
@section('content')

<section id="thank_you" class="thank-you-section" style="padding: 80px 20px; ">
    <div class="container d-flex justify-content-center" style="width:100%; text-align: center;">
        <div class="card shadow-lg text-center p-5" style="max-width: 700px; border-radius: 20px; ">
            <div class="mb-4">
                <img src="{{ asset('frontend/images/completed.png') }}" alt="Cảm ơn" style="width: 300px;">
            </div>
            <h2 style="color: #28a745; font-size: 2.5rem; font-weight: bold;">
                Cảm ơn bạn đã đặt hàng tại cửa hàng chúng tôi!
            </h2>
            <p style="font-size: 1.2rem; margin-top: 20px; color: #333;">
                Đơn hàng của bạn đã được tiếp nhận. Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận và giao hàng.
            </p>
            <p style="font-size: 1.1rem; color: #6c757d; margin-top: 10px;">
                Bạn có thể kiểm tra thông tin đơn hàng trong mục
                <a href="{{ url('orders') }}" style="color: #007bff; text-decoration: underline;">Lịch sử đơn hàng</a>.
            </p>
            <div style="margin-top: 30px;">
                <a href="{{ url('/') }}" class="btn btn-success btn-lg" style="border-radius: 50px; padding: 12px 30px;">
                    Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
</section>
<script>
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    const userId = localStorage.getItem("user_id") || sessionStorage.getItem('user_id');


    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        const userId = localStorage.getItem("user_id") || sessionStorage.getItem('user_id');
        const params = new URLSearchParams(window.location.search);

        const hasVNPayParams = params.has('vnp_Amount') && params.has('vnp_ResponseCode');
        const responseCode = params.get('vnp_ResponseCode');
        let orderCode = params.get('vnp_TxnRef');
        const savedOrderBodyJSON = localStorage.getItem(`body_${userId}`);
        let savedOrderBody = null;
        if (savedOrderBodyJSON) {
            try {
                savedOrderBody = JSON.parse(savedOrderBodyJSON);
            } catch (e) {
                console.error("Lỗi parse JSON order body từ localStorage", e);
            }
        }

        if (hasVNPayParams && responseCode === '00') {
            if (!savedOrderBody) {
                alert("Không tìm thấy thông tin đơn hàng trong bộ nhớ.");
                return;
            }
            console.log(savedOrderBody);

            fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(savedOrderBody)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Thanh toán VNPay và tạo đơn hàng thành công!");
                        // Xóa hết localStorage liên quan
                        localStorage.removeItem(`cart_${userId}`);
                        localStorage.removeItem(`coupon_code_${userId}`);
                        localStorage.removeItem(`discount_amount_${userId}`);
                        localStorage.removeItem(`discount_type_${userId}`);
                        localStorage.removeItem(`shipping_fee_${userId}`);
                        localStorage.removeItem(`shipping_address_${userId}`);
                        localStorage.removeItem(`body_${userId}`);

                        window.location.href = "/check-out-completed";
                    } else {
                        alert("Lỗi khi tạo đơn hàng sau thanh toán VNPay.");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Lỗi kết nối server khi tạo đơn hàng.");
                });

        } else {
            console.log("Thanh toán tiền mặt, không gọi API tạo đơn ở trang này");
        }
    });
</script>
<style>
    .thank-you-section {
        padding: 60px 0;
    }

    .thank-you-message {
        background-color: #fff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-primary:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>

@endsection