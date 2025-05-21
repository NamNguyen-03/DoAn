@extends('admin.admin_layout')
@section('admin_content')
<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-promotion-content')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm email content
            </a>
            Liệt kê email content
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
                        <button class="btn btn-sm btn-default" type="button" onclick="searchEmailContents()">Search</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="emailContentTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Envelope</th>
                        <th>Subject</th>
                        <th>Content</th>
                        <th>Thời gian tạo</th>
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
<script>
    let allContents = [];
    let currentPage = 1;
    const perPage = 10;
    let searchQuery = "";

    function fetchEmailContents() {
        fetch(`/api/promotions-content?search=${encodeURIComponent(searchQuery)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allContents = data.data;
                    renderEmailContents(currentPage);
                    updatePagination();
                }
            })
            .catch(error => {
                console.error("Lỗi api: " + error);
            })
    }

    function renderEmailContents(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const contentsToDisplay = allContents.slice(start, end);
        let tableBody = document.querySelector("#emailContentTable tbody");
        tableBody.innerHTML = "";
        contentsToDisplay.forEach((content, index) => {
            let row = ` <tr>
                <td>${start + index + 1}</td>
                <td>${content.envelope}</td>
                <td>${content.subject}</td>
                <td>${content.content.length>100?content.content.substring(0,200)+"...":content.content}</td>
                <td>${content.created_at}</td>
                <td>
                <a href="/admin/edit-promotion-content/${content.id}" class="active">
                        <i class="fa fa-pencil-square-o text-success text-active"></i>
                    </a>
                    <a href="javascript:void(0)" class="active" onclick="deleteEmailContent('${content.id}')">
                        <i class="fa fa-trash text"></i>
                    </a>
                </td>
            </tr>`;
            tableBody.innerHTML += row;
        })
    }

    function updatePagination() {
        const totalPages = Math.ceil(allContents.length / perPage);
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

    function searchEmailContents() {
        searchQuery = document.getElementById("searchInput").value;
        currentPage = 1;
        fetchEmailContents();

    }
    document.addEventListener("DOMContentLoaded", function() {
        fetchEmailContents();
    })

    function deleteEmailContent(id) {
        let adminToken = localStorage.getItem("admin_token");

        // Nếu không có token, chuyển hướng về trang đăng nhập
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng đến trang đăng nhập
            return; // Dừng lại và không tiếp tục gửi request
        }
        if (confirm("Bạn có chắc chắn muốn xóa content này không?")) {
            fetch(`{{ url('/api/promotions-content/') }}/${id}`, {
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
                        alert("Xóa email content thành công!");
                        fetchEmailContents();
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa email content."));
                    }
                })
                .catch(error => alert(error.message));
        }
    }
</script>

@endsection