@extends('home.user_layout')

@section('mini_content')
<section class="orders-section py-5">
    <div class="container" style="width:100%">
        <div class="user-info-container">
            <form class="user-form" onsubmit="event.preventDefault();" autocomplete="off">
                <label for="fullname">Họ và tên</label>
                <input type="text" id="fullname" placeholder="Nhập họ và tên">

                <label for="phone">Số điện thoại</label>
                <input type="tel" id="phone" placeholder="Nhập số điện thoại">

                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Nhập email">

                <div id="password-group" style="display:none; display: flex; align-items: center; gap: 10px;">
                    <div style="flex:1; position: relative;">
                        <label for="password">Xác nhận mật khẩu</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập mật khẩu để xác nhận"
                            autocomplete="new-password" readonly
                            onfocus="this.removeAttribute('readonly');">
                        <span id="password-check-icon" style="position: absolute; right: 10px; top: 38px; display: none; color: green;">
                            <i class="fa-solid fa-check"></i>
                        </span>

                    </div>

                    <button type="button" id="confirm-password-btn" style="margin-top:5px">Xác nhận</button>
                </div>

                <button type="button" class="update_user_info">Cập nhật</button>
            </form>
        </div>
    </div>
</section>

<script>
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');
    const userEmail = localStorage.getItem('user_email') || sessionStorage.getItem('user_email');
    let verified = false;
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

    document.addEventListener("DOMContentLoaded", function() {
        if (userId) {
            fetchUserInfo();
        } else {
            showAlert("Cảnh báo", "Vui lòng đăng nhập trước!", "warning", 'red');
        }
    });

    function fetchUserInfo() {
        fetch(`/api/users/${userId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    document.getElementById('fullname').value = user.name;
                    document.getElementById('phone').value = user.phone;
                    document.getElementById('email').value = user.email;
                } else {
                    showAlert("Cảnh báo", "Không thể lấy thông tin người dùng!", "warning", 'red');
                }
            })
            .catch(error => {
                console.error('Lỗi khi lấy thông tin:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại sau.');
            });
    }

    document.querySelector('.update_user_info').addEventListener('click', function() {
        if (!verified) {
            document.getElementById('password-group').style.display = 'flex';
            showAlert("Xác thực", "Vui lòng nhập mật khẩu để xác nhận trước khi cập nhật.", "info", "blue");
        } else {
            updateUserInfo();
        }
    });

    document.getElementById('confirm-password-btn').addEventListener('click', function() {
        const password = document.getElementById('confirm_password').value;
        const confirmInput = document.getElementById('confirm_password');
        const checkIcon = document.getElementById('password-check-icon');

        confirmInput.style.backgroundColor = '';
        checkIcon.style.display = 'none';

        if (!password) {
            showAlert("Lỗi", "Vui lòng nhập mật khẩu!", "warning", "red");
            return;
        }

        verifyPassword(userEmail, password)
            .then(success => {
                if (success) {
                    verified = true;
                    showAlert("Thành công", "Xác nhận mật khẩu thành công!", "success", "green");

                    confirmInput.style.backgroundColor = '#d4edda'; // Xanh lá nhạt
                    checkIcon.style.display = 'inline'; // Hiện icon
                } else {
                    verified = false;
                    showAlert("Thất bại", "Mật khẩu không đúng!", "error", "red");
                }
            });
    });


    function verifyPassword(email, password) {
        return fetch('/api/user/verify-password', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + token,
                },
                body: JSON.stringify({
                    email,
                    password
                })
            })
            .then(res => res.json())
            .then(data => data.success)
            .catch(error => {
                console.error('Lỗi xác thực:', error);
                return false;
            });
    }

    function updateUserInfo() {
        fetch(`/api/users/${userId}`, {
                method: 'PATCH',
                headers: {
                    "Authorization": "Bearer " + token,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    name: document.getElementById('fullname').value,
                    phone: document.getElementById('phone').value,
                    email: document.getElementById('email').value,
                    password: document.getElementById('confirm_password').value
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert("Thành công", "Cập nhật thông tin thành công!", "success", "green");
                    localStorage.setItem('user_name', data.data.name);
                    localStorage.setItem('user_email', data.data.email);
                    window.location.href = '/account/info';
                } else {
                    showAlert("Lỗi", "Cập nhật thất bại!", "error", "red");
                }
            })
            .catch(error => {
                console.error('Lỗi cập nhật:', error);
                alert('Có lỗi xảy ra khi cập nhật.');
            });
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
</style>
@endsection