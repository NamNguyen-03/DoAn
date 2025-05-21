@extends('admin.admin_layout')
@section('admin_content')
<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-product')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Các nội dung email
            </a>
            Các email đăng kí nhận khuyến mãi
        </div>
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <!-- <select class="input-sm form-control w-sm inline v-middle">
                    <option value="0">Bulk action</option>
                    <option value="1">Delete selected</option>
                    <option value="2">Bulk edit</option>
                    <option value="3">Export</option>
                </select>
                <button class="btn btn-sm btn-default">Apply</button> -->
            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchEmails()">Search</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="emailTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Lời nhắn</th>
                        <th>Thời gian gửi</th>
                        <th>Gửi khuyến mãi email</th>
                        <th style="width:30px;">Xóa email</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

        <div id="pagination" style="margin-top: 20px;"></div>
    </div>
</div>
<!-- Modal gửi email -->
<div class="modal fade" id="emailContentModal" tabindex="-1" role="dialog" aria-labelledby="emailContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Chọn nội dung email để gửi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 30px">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Phong bì</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="emailContentTableBody">
                        <!-- Nội dung sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    let allEmails = [];
    let currentPage = 1;
    const perPage = 10;
    let searchQuery = "";

    function fetchEmails() {
        fetch(`/api/contact-us?search=${encodeURIComponent(searchQuery)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allEmails = data.data;
                    renderEmails(currentPage);
                    updatePagination();
                }
            })
            .catch(error => {
                console.error("Lỗi api: " + error);
            })
    }

    function renderEmails(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const emailsToDisplay = allEmails.slice(start, end);
        let tableBody = document.querySelector("#emailTable tbody");
        tableBody.innerHTML = "";
        emailsToDisplay.forEach((email, index) => {
            let row = ` <tr>
                <td>${start + index + 1}</td>
                <td>${email.customer_name?email.customer_name:"Không có"}</td>
                <td>${email.email}</td>
                <td>${email.message}</td>
                <td>${email.created_at}</td>
                <td><a href="javascript:void(0)" class="active" onclick="sendMail('${email.email}')">
                        <i class="fa-solid fa-envelope"></i>
                    </a>
                </td>
                <td>
                    <a href="javascript:void(0)" class="active" onclick="deleteEmail('${email.email}')">
                        <i class="fa fa-trash text"></i>
                    </a>
                </td>
            </tr>`;
            tableBody.innerHTML += row;
        })
    }

    function updatePagination() {
        const totalPages = Math.ceil(allEmails.length / perPage);
        const paginationDiv = document.getElementById("pagination");
        paginationDiv.innerHTML = "";
        for (let i = 1; i <= totalPages; i++) {
            const pageLink = document.createElement('a');
            pageLink.href = "#";
            pageLink.className = "page-link";
            pageLink.innerText = i;
            pageLink.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = i;
                renderEmails(currentPage);
                updatePagination();

            })
            if (i === currentPage) {
                pageLink.classList.add("active");

            }
            paginationDiv.append(pageLink);
        }
    }

    function searchEmails() {
        searchQuery = document.getElementById("searchInput").value;
        currentPage = 1;
        fetchEmails();

    }
    document.addEventListener("DOMContentLoaded", function() {
        fetchEmails();
    })

    function deleteEmail(email) {
        let adminToken = localStorage.getItem("admin_token");

        // Nếu không có token, chuyển hướng về trang đăng nhập
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng đến trang đăng nhập
            return; // Dừng lại và không tiếp tục gửi request
        }
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
            fetch(`{{ url('/api/contact-us/') }}/${email}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": "Bearer " + adminToken
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        // Nếu mã lỗi là 401, hiển thị thông báo và chuyển hướng về trang đăng nhập
                        alert("Chưa đăng nhập, vui lòng đăng nhập!");
                        window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng đến trang đăng nhập
                        return; // Dừng lại và không xử lý thêm
                    }

                    return response.json(); // Nếu không phải lỗi 401, tiếp tục xử lý dữ liệu
                })
                .then(data => {
                    if (data.success) {
                        alert("Xóa email thành công!");
                        fetchEmails();
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa email."));
                    }
                })
                .catch(error => alert(error.message));
        }
    }

    function sendMail(email) {
        const adminToken = localStorage.getItem("admin_token");
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch(`/api/promotions-content`, {
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    let tableBody = document.getElementById("emailContentTableBody");
                    tableBody.innerHTML = "";
                    data.data.forEach((content, index) => {
                        let row = `<tr>
                    <td>${index + 1}</td>
                    <td>${content.envelope}</td>
                    <td>${content.subject}</td>
                    <td>${content.content.length>100?content.content.substring(0,200)+"...":content.content}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="sendSelectedEmail('${email}', ${content.id})">Dùng</button>
                    </td>
                </tr>`;
                        tableBody.innerHTML += row;
                    });

                    $('#emailContentModal').modal('show');
                } else {
                    alert("Không thể lấy danh sách nội dung email.");
                }
            })
            .catch(error => {
                console.error("Lỗi khi lấy nội dung email:", error);
                alert("Lỗi khi tải nội dung email.");
            });
    }

    function sendSelectedEmail(email, contentId) {
        const adminToken = localStorage.getItem("admin_token");
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch(`/api/promotions-send`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken
                },
                body: JSON.stringify({
                    email: email,
                    content_id: contentId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Gửi email thành công!");
                    $('#emailContentModal').modal('hide');
                } else {
                    alert("Lỗi gửi email: " + (data.message || "Không rõ lỗi."));
                }
            })
            .catch(error => {
                console.error("Lỗi gửi email:", error);
                alert("Lỗi gửi email.");
            });
    }
</script>
<style>
    .modal-dialog {
        max-width: 900px;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .table td {
        vertical-align: middle;
    }
</style>

@endsection