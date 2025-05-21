@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-post-cate')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm danh mục bài viết
            </a>
            Liệt kê danh mục bài viết
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
                        <button class="btn btn-sm btn-default" type="button" onclick="searchPostCates()">Search</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="table-responsive">

            <table class="table table-striped b-t b-light">
                <thead>
                    <tr>
                        <th style="width:20px;">
                            <label class="i-checks m-b-none">
                                <input type="checkbox"><i></i>
                            </label>
                        </th>
                        <th>Tên danh mục bài viết</th>
                        <th>slug</th>
                        <th>Mô tả danh mục bài viết</th>
                        <th>Hiển thị</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody>


                </tbody>
            </table>
        </div>
        <div id="pagination" class="text-center" style="margin-top: 20px;"></div>

    </div>
</div>
<script>
    let allPostCates = [];
    let currentPage = 1;
    const perPage = 10;
    let searchQuery = "";

    function fetchPostCates() {
        const url = `{{ url('/api/postcates') }}?search=${encodeURIComponent(searchQuery)}`;
        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Lỗi khi kết nối API.');
                return response.json();
            })
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    allPostCates = data.data;
                    renderPostCates(currentPage);
                    updatePagination();
                } else {
                    alert("Dữ liệu không hợp lệ.");
                }
            })
            .catch(error => alert("Lỗi API: " + error.message));
    }

    function renderPostCates(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const postCatesToDisplay = allPostCates.slice(start, end);
        const tbody = document.querySelector("table tbody");
        tbody.innerHTML = "";

        if (postCatesToDisplay.length === 0) {
            tbody.innerHTML = "<tr><td colspan='6' class='text-center'>Không có danh mục nào.</td></tr>";
            return;
        }

        postCatesToDisplay.forEach(postcate => {
            tbody.innerHTML += `
                <tr>
                    <td><input type="checkbox"></td>
                    <td>${postcate.cate_post_name}</td>
                    <td>${postcate.cate_post_slug}</td>
                    <td>${postcate.cate_post_desc}</td>
                    <td>
                        <a href="javascript:void(0)" class="toggle-status" data-slug="${postcate.cate_post_slug}" data-status="${postcate.cate_post_status}">
                            ${postcate.cate_post_status == 1 ?
                                '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
                                '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>'
                            }
                        </a>
                    </td>
                    <td>
                        <a href="/admin/edit-post-cate/${postcate.cate_post_slug}">
                            <i class="fa fa-pencil-square-o text-success text-active"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="deletePostCate('${postcate.cate_post_slug}')">
                            <i class="fa fa-trash text-danger"></i>
                        </a>
                    </td>
                </tr>
            `;
        });

        document.querySelectorAll(".toggle-status").forEach(link => {
            link.addEventListener("click", function() {
                const slug = this.getAttribute("data-slug");
                const status = this.getAttribute("data-status");
                const newStatus = status == 0 ? 1 : 0;
                updatePostCateStatus(slug, newStatus, this);
            });
        });
    }

    function updatePagination() {
        const totalPages = Math.ceil(allPostCates.length / perPage);
        const paginationDiv = document.getElementById("pagination");
        paginationDiv.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {
            const link = document.createElement("a");
            link.href = "#";
            link.className = "page-link";
            link.innerText = i;
            if (i === currentPage) link.classList.add("active");
            link.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = i;
                renderPostCates(currentPage);
                updatePagination();
            });
            paginationDiv.appendChild(link);
        }
    }

    function searchPostCates() {
        searchQuery = document.getElementById("searchInput").value;
        currentPage = 1;
        fetchPostCates();
    }

    function updatePostCateStatus(id, newStatus, element) {
        const adminToken = localStorage.getItem("admin_token");
        if (!adminToken) {
            alert("Vui lòng đăng nhập.");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch(`{{ url('/api/postcates') }}/${id}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + adminToken
                },
                body: JSON.stringify({
                    cate_post_status: newStatus
                })
            })
            .then(res => {
                if (res.status === 401) {
                    alert("Bạn cần đăng nhập.");
                    window.location.href = "{{ url('admin-login') }}";
                    return;
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    alert("Cập nhật trạng thái thành công!");
                    element.setAttribute("data-status", newStatus);
                    element.innerHTML = newStatus == 1 ?
                        '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
                        '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>';
                } else {
                    alert("Cập nhật thất bại.");
                }
            })
            .catch(err => alert("Lỗi: " + err.message));
    }

    function deletePostCate(id) {
        const adminToken = localStorage.getItem("admin_token");
        if (!adminToken) {
            alert("Bạn cần đăng nhập.");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        if (confirm("Bạn chắc chắn muốn xóa danh mục này?")) {
            fetch(`{{ url('/api/postcates') }}/${id}`, {
                    method: "DELETE",
                    headers: {
                        "Authorization": "Bearer " + adminToken
                    }
                })
                .then(res => {
                    if (res.status === 401) {
                        alert("Bạn cần đăng nhập.");
                        window.location.href = "{{ url('admin-login') }}";
                        return;
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Xóa thành công!");
                        fetchPostCates();
                    } else {
                        alert("Không thể xóa: " + (data.message || ""));
                    }
                })
                .catch(err => alert("Lỗi khi xóa: " + err.message));
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        fetchPostCates();
    });
</script>

<style>
    #pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .page-link {
        margin: 5px;
        padding: 10px 15px;
        text-decoration: none;
        color: #007bff;
        border: 1px solid #007bff;
        border-radius: 5px;
    }

    .page-link:hover {
        background-color: #007bff;
        color: white;
    }

    .page-link.active {
        background-color: #007bff;
        color: white;
        border-color: #0056b3;
    }
</style>





@endsection