@extends('app')

@section('title', 'Danh Sách Người Dùng')

@section('content')
<h2>Danh sách Users (JSON API)</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody id="user-table-body">
        <!-- Dữ liệu sẽ được thêm vào đây bằng JavaScript -->
    </tbody>
</table>

<div id="pagination"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetchUsers();

        function fetchUsers(page = 1) {
            fetch(`/api/users?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderUsers(data.data.data); // Lấy danh sách users từ API
                        renderPagination(data.data);
                    }
                })
                .catch(error => console.error('Lỗi API:', error));
        }

        function renderUsers(users) {
            let tableBody = document.getElementById("user-table-body");
            tableBody.innerHTML = "";

            users.forEach(user => {
                tableBody.innerHTML += `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>
                                <a href="/users/${user.id}" class="btn btn-info btn-sm">Xem</a>
                            </td>
                        </tr>
                    `;
            });
        }

        function renderPagination(paginationData) {
            let paginationDiv = document.getElementById("pagination");
            paginationDiv.innerHTML = "";

            if (paginationData.last_page > 1) {
                paginationData.links.forEach(link => {
                    if (link.url) {
                        paginationDiv.innerHTML += `<button onclick="fetchUsers(${new URL(link.url).searchParams.get('page')})">${link.label}</button> `;
                    }
                });
            }
        }
    });
</script>
@endsection