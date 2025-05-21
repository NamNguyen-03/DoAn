@extends('app')

@section('title', 'Thông tin người dùng')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Thông tin Người Dùng</h2>

    <div id="user-info" class="card p-3">
        <p><strong>ID:</strong> <span id="user-id"></span></p>
        <p><strong>Tên:</strong> <span id="user-name"></span></p>
        <p><strong>Email:</strong> <span id="user-email"></span></p>
    </div>

    <p id="error-message" class="text-danger" style="display: none;">Không tìm thấy thông tin người dùng.</p>

    <a href="{{ route('users.all_user') }}" class="btn btn-primary mt-3">Quay lại</a>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let userId = window.location.pathname.split("/").pop(); // Lấy ID từ URL

        fetch(`/api/users/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("user-id").textContent = data.data.id;
                    document.getElementById("user-name").textContent = data.data.name;
                    document.getElementById("user-email").textContent = data.data.email;
                } else {
                    document.getElementById("error-message").style.display = "block";
                    document.getElementById("user-info").style.display = "none";
                }
            })
            .catch(error => console.error("Lỗi API:", error));
    });
</script>
@endsection