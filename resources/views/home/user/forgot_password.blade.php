@extends('home.home_layout')
@section('content')
<section id="form">
    <div class="container" style="width:100%">
        <div class="row">
            <div class="col-sm-12">
                <div class="forgot-pass-form">
                    <h2 class="h2dangnhap">Quên mật khẩu?</h2>
                    <p class="instruction-text">Vui lòng nhập email đã đăng kí với tài khoản để nhận mã xác nhận.</p>
                    <form id="forgot-pass-form">
                        <div class="form-group d-flex align-items-center">
                            <div class="form-group">
                                <input type="email" id="customer_email" required placeholder="Email" class="form-control" style="margin-top:30px" />
                            </div>
                            <button type="button" id="send-code-btn" class="btn btn-primary" style="margin-bottom:15px">Gửi mã</button>
                        </div>
                        <input type="text" id="verification_code" placeholder="Nhập mã xác nhận" class="form-control" style="display:none" />
                        <button type="submit" class="btn btn-success" disabled id="submit-btn">Xác nhận</button>
                    </form>
                    <p id="register-message" style="text-align: center;"></p>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    document.getElementById('send-code-btn').addEventListener('click', function() {
        const email = document.getElementById('customer_email').value.trim();
        const message = document.getElementById('register-message');

        if (!email) {
            message.textContent = 'Vui lòng nhập email hợp lệ.';
            message.style.color = 'red';
            return;
        }

        // Gửi yêu cầu gửi mã OTP đến email
        fetch('/api/send-otp', {
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",

                },
                body: JSON.stringify({
                    email: email
                })
            })
            .then(response => {
                if (!response.ok) throw response;
                return response.json();
            })
            .then(data => {
                // Hiển thị input nhập mã
                document.getElementById('verification_code').style.display = 'block';

                // Bỏ disabled nút xác nhận
                document.getElementById('submit-btn').disabled = false;

                // Hiển thị thông báo thành công
                message.textContent = data.message || 'Mã xác nhận đã được gửi đến email của bạn.';
                message.style.color = 'green';
            })
            .catch(async (error) => {
                const errData = await error.json();
                message.textContent = errData.message || 'Lỗi khi gửi mã xác nhận.';
                message.style.color = 'red';
            });
    });

    document.getElementById('forgot-pass-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const email = document.getElementById('customer_email').value.trim();
        const code = document.getElementById('verification_code').value.trim();
        const message = document.getElementById('register-message');

        if (!code) {
            message.textContent = 'Vui lòng nhập mã xác nhận.';
            message.style.color = 'red';
            return;
        }

        // Gửi yêu cầu xác thực mã OTP
        fetch('/api/verify-otp', {
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",

                },
                body: JSON.stringify({
                    email: email,
                    otp: code
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    message.textContent = data.message || 'Xác nhận thành công! Tiếp tục đổi mật khẩu.';
                    message.style.color = 'green';
                    sessionStorage.setItem('otp_token', data.otp_token);
                    sessionStorage.setItem('user_email', email);
                    window.location.href = '/change-password';

                } else {
                    message.textContent = data.message || 'Xác nhận thất bại!';
                    message.style.color = 'red';
                }

                // TODO: Redirect đến trang đổi mật khẩu nếu cần
            })
            .catch(async (error) => {
                const errData = await error.json();
                message.textContent = errData.message || 'Sai mã xác nhận.';
                message.style.color = 'red';
            });
    });
</script>


<style>
    #form {
        margin-top: 70px;
        padding: 60px 0;
    }

    .forgot-pass-form {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 40px;
        max-width: 400px;
        margin: 0 auto;
        text-align: center;
    }

    .h2dangnhap {
        margin-bottom: 25px;
        font-size: 28px;
        color: #333;
        font-weight: 600;
    }

    .instruction-text {
        margin-bottom: 20px;
        font-size: 16px;
        color: #777;
    }

    .form-control {
        border-radius: 6px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.12);
        padding: 12px 15px;
        font-size: 16px;
        margin-bottom: 15px;
    }

    .btn {
        border-radius: 6px;
        font-size: 14px;
        width: 30%;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
        font-weight: 600;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    #verification-code-container {
        margin-top: 10px;
        max-width: 250px;
    }

    #register-message {
        font-size: 14px;
        margin-top: 15px;
        color: #d9534f;
        font-weight: 500;
    }
</style>


@endsection