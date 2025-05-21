@extends('home.user_layout')

@section('mini_content')
<section class="orders-section py-5">
    <div class="container" style="width:100%">
        <div class="user-info-container">
            <form class="user-form" onsubmit="event.preventDefault();" autocomplete="off">
                <div style="position: relative;">
                    <label for="old_password">Nhập mật khẩu cũ</label>
                    <input type="password" id="old_password" name="old_password" placeholder="Nhập mật khẩu cũ"
                        autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');">
                    <span class="toggle-password" data-target="old_password"><i class="fa-solid fa-eye"></i></span>
                </div>

                <div style="position: relative;">
                    <label for="new_password">Nhập mật khẩu mới</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới"
                        autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');">
                    <span id="new-password-check-icon" style="position: absolute; right: 40px; top: 38px; display: none; display: flex; align-items: center; gap: 5px;">
                        <i class="fa-solid"></i>
                        <span id="new-password-check-msg" style="font-size: 13px;"></span>
                    </span>
                    <span class="toggle-password" data-target="new_password"><i class="fa-solid fa-eye"></i></span>
                </div>

                <div style="position: relative;">
                    <label for="confirm_password">Xác nhận mật khẩu</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu mới"
                        autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');">
                    <span id="password-check-icon" style="position: absolute; right: 40px; top: 38px; display: none; display: flex; align-items: center; gap: 5px;">
                        <i class="fa-solid"></i>
                        <span id="password-check-msg" style="font-size: 13px;"></span>
                    </span>
                    <span class="toggle-password" data-target="confirm_password"><i class="fa-solid fa-eye"></i></span>
                </div>

                <button type="button" class="update_user_pass">Cập nhật</button>
            </form>

        </div>
    </div>
</section>
<script>
    const userToken = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    const newPasswordInput = document.getElementById('new_password');
    const oldPasswordInput = document.getElementById('old_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const icon = document.getElementById('password-check-icon');
    const iconI = icon.querySelector('i');
    const msgSpan = document.getElementById('password-check-msg');
    const newPasswordIcon = document.getElementById('new-password-check-icon');
    const newPasswordI = newPasswordIcon.querySelector('i');
    const newPasswordMsg = document.getElementById('new-password-check-msg');
    let verifyPass = false;

    newPasswordInput.addEventListener('input', function() {
        const oldPassword = oldPasswordInput.value.trim();
        const newPassword = newPasswordInput.value.trim();

        // Reset mặc định
        newPasswordInput.style.backgroundColor = '';
        newPasswordInput.style.borderColor = '';
        newPasswordIcon.style.display = 'none';
        newPasswordMsg.textContent = '';
        verifyPass = false;

        if (!newPassword) return;

        if (newPassword.length < 6) {
            newPasswordInput.style.backgroundColor = '#fff3cd';
            newPasswordInput.style.borderColor = '#ffc107';
            newPasswordIcon.style.display = 'flex';
            newPasswordI.className = 'fa-solid fa-exclamation';
            newPasswordIcon.style.color = '#ffc107';
            newPasswordMsg.textContent = 'Mật khẩu ít nhất 6 kí tự';
            return;
        }

        if (newPassword === oldPassword) {
            newPasswordInput.style.backgroundColor = '#f8d7da';
            newPasswordInput.style.borderColor = '#dc3545';
            newPasswordIcon.style.display = 'flex';
            newPasswordI.className = 'fa-solid fa-x';
            newPasswordIcon.style.color = 'red';
            newPasswordMsg.textContent = 'Mật khẩu mới không được trùng với mật khẩu cũ';
            return;
        }

        // Mật khẩu hợp lệ
        newPasswordInput.style.backgroundColor = '#d4edda';
        newPasswordInput.style.borderColor = '#28a745';
        newPasswordIcon.style.display = 'flex';
        newPasswordI.className = 'fa-solid fa-check';
        newPasswordIcon.style.color = 'green';
        newPasswordMsg.textContent = 'Mật khẩu mới hợp lệ';
    });

    // Xử lý khi nhập xác nhận mật khẩu
    confirmPasswordInput.addEventListener('input', function() {
        const newPassword = newPasswordInput.value.trim();
        const oldPassword = oldPasswordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();

        // Reset mặc định
        icon.style.display = '';
        iconI.className = '';
        icon.style.color = '';
        msgSpan.textContent = '';
        confirmPasswordInput.style.backgroundColor = '';
        confirmPasswordInput.style.borderColor = '';
        verifyPass = false;

        if (!confirmPassword) return;

        if (!newPassword || newPassword.length < 6 || newPassword === oldPassword) {
            confirmPasswordInput.style.backgroundColor = '#f8d7da';
            confirmPasswordInput.style.borderColor = '#dc3545';
            icon.style.display = 'inline';
            iconI.className = 'fa-solid fa-exclamation';
            icon.style.color = '#ffc107';
            msgSpan.textContent = 'Vui lòng nhập mật khẩu mới hợp lệ';
            return;
        }

        if (confirmPassword === newPassword) {
            confirmPasswordInput.style.backgroundColor = '#d4edda';
            confirmPasswordInput.style.borderColor = '#28a745';
            icon.style.display = 'inline';
            iconI.className = 'fa-solid fa-check';
            icon.style.color = 'green';
            msgSpan.textContent = 'Mật khẩu hợp lệ';
        } else {
            confirmPasswordInput.style.backgroundColor = '#f8d7da';
            confirmPasswordInput.style.borderColor = '#dc3545';
            icon.style.display = 'inline';
            iconI.className = 'fa-solid fa-x';
            icon.style.color = 'red';
            msgSpan.textContent = 'Mật khẩu không trùng';
        }
    });

    document.querySelectorAll('input[type="password"]').forEach(input => {
        input.addEventListener('input', function() {
            const eyeIcon = document.querySelector(`.toggle-password[data-target="${this.id}"]`);
            if (this.value) {
                eyeIcon.style.display = 'block';
            } else {
                eyeIcon.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    document.querySelector('.update_user_pass').addEventListener('click', function() {
        const oldPassword = oldPasswordInput.value.trim();
        const newPassword = newPasswordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();
        let verifyPass = true;
        let errorMsg = '';
        if (!oldPassword || !newPassword || !confirmPassword) {
            errorMsg = 'Vui lòng nhập đầy đủ các trường.';
            verifyPass = false;
        } else if (newPassword.length < 6) {
            errorMsg = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            verifyPass = false;
        } else if (newPassword === oldPassword) {
            errorMsg = 'Mật khẩu mới không được trùng với mật khẩu cũ.';
            verifyPass = false;
        } else if (newPassword !== confirmPassword) {
            errorMsg = 'Mật khẩu xác nhận không khớp.';
            verifyPass = false;
        }
        if (!verifyPass) {
            showAlert("Xác thực", errorMsg, "info", "red");
        } else if (newPassword && confirmPassword) {
            updateUserPassword(oldPassword, newPassword, confirmPassword);
        } else {
            showAlert("Lỗi", errorMsg, "info", "red");
        }
    });

    function updateUserPassword(oldPassword, newPassword, confirmPassword) {
        fetch(`/api/user/change-password/`, {
                method: 'POST',
                headers: {
                    "Authorization": "Bearer " + userToken,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    current_password: oldPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert("Success", "Đổi mật khẩu thành công.", "success", "green");
                    window.location.href = '/account/info';

                } else {
                    showAlert("Error", "Đổi mật khẩu thất bại." + data.message, "error", "red");

                }
            })
    }

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
    .user-info-container {
        max-width: 600px;
        margin: 30px auto;
        padding: 20px;
        border-radius: 12px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        font-family: sans-serif;
    }

    .user-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }

    .user-header img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-right: 20px;
        border: 2px solid #ccc;
    }

    .user-header h2 {
        margin: 0;
        font-size: 1em;
        color: #333;
    }

    .user-form label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #555;
    }

    .user-form input {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1em;
    }

    .user-form button {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 1em;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .user-form button:hover {
        background-color: #0056b3;
    }

    .toggle-password {
        position: absolute;
        top: 38px;
        right: 10px;
        cursor: pointer;
        color: #888;
        z-index: 10;
        display: none;
    }

    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }

    input[type="password"]::-webkit-credentials-auto-fill-button,
    input[type="password"]::-webkit-clear-button,
    input[type="password"]::-webkit-inner-spin-button {
        display: none !important;
    }
</style>

@endsection