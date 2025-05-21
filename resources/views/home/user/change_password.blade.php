@extends('home.home_layout')
@section('content')
<section id="form">
    <div class="container" style="width:100%">
        <div class="row">
            <div class="col-sm-12">
                <div class="forgot-pass-form">
                    <h2 class="h2dangnhap">Đặt lại mật khẩu?</h2>
                    <form id="forgot-pass-form">
                        <div class="form-group" style="position: relative;">
                            <label for="password">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-control" id="new_password" placeholder="Mật khẩu mới" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" required>
                            <span id="new-password-check-icon" style="position: absolute; right: 40px; top: 38px; display: none; align-items: center; gap: 5px;">
                                <i class="fa-solid"></i>
                                <span id="new-password-check-msg" style="font-size: 13px;"></span>
                            </span>
                            <span class="toggle-password" data-target="new_password"><i class="fa-solid fa-eye"></i></span>
                        </div>
                        <div class="form-group" style="position: relative;">
                            <label for="password">Nhập lại mật khẩu</label>
                            <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Nhập lại mật khẩu" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" required>
                            <span id="confirm-password-check-icon" style="position: absolute; right: 40px; top: 38px; display: none; align-items: center; gap: 5px;">
                                <i class="fa-solid"></i>
                                <span id="confirm-password-check-msg" style="font-size: 13px;"></span>
                            </span>
                            <span class="toggle-password" data-target="confirm_password"><i class="fa-solid fa-eye"></i></span>
                        </div>
                        <button type="submit" class="btn btn-success" disabled id="submit-change-btn">Xác nhận</button>
                    </form>
                    <p id="register-message" style="text-align: center;"></p>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    let newPassword = "";
    let confirmPassword = "";

    document.querySelectorAll(".toggle-password").forEach(icon => {
        icon.addEventListener("click", function() {
            const targetId = this.getAttribute("data-target");
            const input = document.getElementById(targetId);
            const iconElem = this.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                iconElem.classList.remove("fa-eye");
                iconElem.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                iconElem.classList.remove("fa-eye-slash");
                iconElem.classList.add("fa-eye");
            }
        });
    });

    // Validate new password
    document.getElementById('new_password').addEventListener('input', function() {
        newPassword = this.value.trim();
        const msgIcon = document.getElementById('new-password-check-icon');
        const msgText = document.getElementById('new-password-check-msg');
        const input = this;

        if (!newPassword) {
            msgIcon.style.display = 'none';
            input.style.border = '';
            return;
        }

        msgIcon.style.display = 'flex';

        if (newPassword.length < 6) {
            input.style.border = "2px solid #f88";
            msgText.innerText = "Mật khẩu phải có ít nhất 6 ký tự";
            msgIcon.querySelector("i").className = "fa-solid fa-triangle-exclamation text-danger";
        } else {
            input.style.border = "2px solid #4caf50";
            msgText.innerText = "Mật khẩu hợp lệ";
            msgIcon.querySelector("i").className = "fa-solid fa-check text-success";
        }
    });

    // Validate confirm password
    document.getElementById('confirm_password').addEventListener('input', function() {
        confirmPassword = this.value.trim();
        const msgIcon = document.getElementById('confirm-password-check-icon');
        const msgText = document.getElementById('confirm-password-check-msg');
        const input = this;

        if (!confirmPassword) {
            msgIcon.style.display = 'none';
            input.style.border = '';
            return;
        }

        msgIcon.style.display = 'flex';

        if (!newPassword || newPassword.length < 6) {
            input.style.border = "2px solid #f88";
            msgText.innerText = "Vui lòng nhập mật khẩu mới hợp lệ";
            msgIcon.querySelector("i").className = "fa-solid fa-triangle-exclamation text-danger";
        } else if (newPassword === confirmPassword) {
            input.style.border = "2px solid #4caf50";
            msgText.innerText = "";
            msgIcon.querySelector("i").className = "fa-solid fa-check text-success";
        } else {
            input.style.border = "2px solid #f88";
            msgText.innerText = "Mật khẩu không trùng";
            msgIcon.querySelector("i").className = "fa-solid fa-triangle-exclamation text-danger";
        }
    });

    function checkPasswordValidity() {
        const submitBtn = document.getElementById('submit-change-btn');

        if (
            newPassword.length >= 6 &&
            confirmPassword.length >= 6 &&
            newPassword === confirmPassword
        ) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    // Gọi lại khi input thay đổi
    document.getElementById('new_password').addEventListener('input', checkPasswordValidity);
    document.getElementById('confirm_password').addEventListener('input', checkPasswordValidity);
    document.getElementById('submit-change-btn').addEventListener('click', function() {
        const new_password = document.getElementById('new_password').value;
        const new_password_confirmation = document.getElementById('confirm_password').value;
        console.log(new_password + " " + new_password_confirmation);
        fetch(`/api/change-password`, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-Otp-Token': sessionStorage.getItem('otp_token'), // Lấy OTP token từ sessionStorage
                },
                body: JSON.stringify({
                    user_email: sessionStorage.getItem('user_email'), // Lấy email người dùng từ sessionStorage
                    new_password: new_password,
                    new_password_confirmation: new_password_confirmation
                })
            })
            .then(res => res.json()) // Chuyển đổi phản hồi từ server thành JSON
            .then(data => {
                if (data.success) {
                    showAlert("Thành công", "Mật khẩu đã được đổi thành công!", "success", "green");
                    window.location.href = '/login'
                } else {
                    showAlert("Thành công", data.message, "error", "red");
                }
            })
            .catch(error => {
                console.error('Có lỗi xảy ra:', error);
                showAlert("Thành công", 'Đã xảy ra lỗi khi thay đổi mật khẩu. Vui lòng thử lại!', "error", "red");
            });
    });

    function showAlert(title, text, type, color) {
        swal({
            title: title,
            text: `<span style='color:${color};'>${text}</span>`,
            type: type,
            html: true
        });
    }
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
        padding-right: 40px;
        /* Thêm khoảng trống bên phải cho icon */
    }

    .form-group {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        /* Đặt icon ở phía phải */
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 10;
        /* Đảm bảo icon nằm trên input */
    }

    .toggle-password i {
        font-size: 18px;
        color: #333;
        margin-top: 30px;
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

    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    input[type="password"]::-webkit-credentials-auto-fill-button,
    input[type="password"]::-webkit-clear-button {
        display: none !important;
    }
</style>


@endsection