@extends('home.user_layout')
@section('mini_content')
<section class="user-info-section">
    <div class="user-container">
        <div class="user-header">
            <img src="{{asset('frontend/images/avatar1.png')}}" alt="Avatar người dùng">
            <h2>Thông tin khách hàng</h2>
        </div>

        <div class="user-details">
            <p id="user-name"><strong>Họ và tên:</strong></p>
            <p id="user-phone"><strong>Số điện thoại:</strong> </p>
            <p id="user-mail"><strong>Email:</strong> </p>
        </div>

        <div class="btn-wrap">
            <a href="{{ route('home.user.edit_user_info') }}" class="btn-edit">Chỉnh sửa thông tin</a>

        </div>
    </div>
</section>
<br>

<script>
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');
    document.addEventListener("DOMContentLoaded", function() {

        // console.log(userId)
        if (userId) {
            fetchUserInfo(userId);
        } else {
            showAlert("Cảnh báo", "Vui lòng đăng nhập trước!", "warning", 'red');
        }

    });

    function fetchUserInfo(userId) {
        fetch(`/api/users/${userId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const user = data.data
                    document.getElementById('user-name').innerHTML = `<strong>Họ và tên:</strong> ${user.name}`;
                    document.getElementById('user-phone').innerHTML = `<strong>Số điện thoại:</strong> ${user.phone}`;
                    document.getElementById('user-mail').innerHTML = `<strong>Email:</strong> ${user.email}`;

                } else {
                    showAlert("Cảnh báo", "Không thể lấy thông tin người dùng!", "warning", 'red');
                }
            })
            .catch(error => {
                console.error('Error fetching user data:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại sau.');
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
    .user-info-section {
        padding: 40px 0;
        background: #f9f9f9;
    }

    .user-container {
        max-width: 600px;
        margin: 0 auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .user-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .user-header img {
        width: 100px;
        border-radius: 50%;
        margin-bottom: 10px;
    }

    .user-header h2 {
        font-size: 24px;
        margin: 0;
    }

    .user-details p {
        font-size: 16px;
        margin: 10px 0;
        padding-left: 10px;
    }

    .btn-wrap {
        text-align: center;
        margin-top: 30px;
    }

    .btn-edit {
        padding: 12px 20px;
        background: #007bff;
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 16px;
        cursor: pointer;
    }

    .btn-edit:hover {
        background: #0056b3;
    }
</style>

@endsection