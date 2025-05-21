@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{ url('/admin/dashboard') }}">
                <img src="{{ asset('backend/images/back.png') }}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{ url('/admin/add-banner') }}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm banner
            </a>
            Liệt kê banner
        </div>
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <!-- Optional bulk actions can be added here -->
            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchBanners()">Search</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="bannerTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên banner</th>
                        <th>Hình ảnh</th>
                        <th>Mô tả</th>
                        <th>Hiển thị</th>
                        <th>Hành động</th>
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
    let searchQuery = "";
    let allBanners = [];
    let currentPage = 1;
    const itemsPerPage = 10;

    function fetchBanners() {
        const url = `{{ url('/api/banners') }}?search=${encodeURIComponent(searchQuery)}`;
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('API lỗi hoặc không thể kết nối.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    allBanners = data.data;
                    renderPaginatedBanners();
                } else {
                    console.error("Dữ liệu không hợp lệ:", data);
                    alert("Lỗi khi lấy dữ liệu. Kiểm tra lại API.");
                }
            })
            .catch(error => {
                console.error("Lỗi khi gọi API:", error);
                alert("Không thể lấy dữ liệu từ server.");
            });
    }

    function renderPaginatedBanners() {
        let startIndex = (currentPage - 1) * itemsPerPage;
        let endIndex = startIndex + itemsPerPage;
        let paginatedBanners = allBanners.slice(startIndex, endIndex);

        let tableBody = document.querySelector("#bannerTable tbody");
        tableBody.innerHTML = "";

        if (paginatedBanners.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='6' class='text-center'>Không có banner nào.</td></tr>";
            return;
        }

        paginatedBanners.forEach((banner, index) => {
            let row = `
                <tr>
                    <td>${startIndex + index + 1}</td>
                    <td>${banner.banner_name || 'Không có tên'}</td>
                    <td><img src="{{ asset('uploads/banner/') }}/${banner.banner_image}" height="70" width="110"></td>
                    <td>${banner.banner_desc || 'Không có mô tả'}</td>
                    <td>
                        <a href="javascript:void(0)" class="toggle-status" data-id="${banner.banner_id}" data-status="${banner.banner_status}">
                            ${banner.banner_status == 1 ?
                                '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
                                '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>'}
                        </a>
                    </td>
                    <td>
                        <a href="/admin/edit-banner/${banner.banner_id}" class="active">
                            <i class="fa fa-pencil-square-o text-success text-active"></i>
                        </a>
                        <a href="javascript:void(0)" class="active" onclick="deleteBanner(${banner.banner_id})">
                            <i class="fa fa-trash text"></i>
                        </a>
                    </td>
                </tr>`;
            tableBody.innerHTML += row;
        });

        document.querySelectorAll('.toggle-status').forEach(el => {
            el.addEventListener('click', function() {
                let bannerId = this.getAttribute("data-id");
                let currentStatus = this.getAttribute("data-status");
                let newStatus = currentStatus == 1 ? 0 : 1;
                updateBannerStatus(bannerId, newStatus, this);
            });
        });

        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(allBanners.length / itemsPerPage);
        let pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {
            let pageLink = document.createElement("a");
            pageLink.href = "javascript:void(0)";
            pageLink.textContent = i;
            pageLink.classList.add("page-link");
            if (i === currentPage) {
                pageLink.classList.add("active");
            }
            pageLink.addEventListener("click", () => {
                currentPage = i;
                renderPaginatedBanners();
            });
            pagination.appendChild(pageLink);
        }
    }

    function searchBanners() {
        searchQuery = document.getElementById('searchInput').value;
        currentPage = 1;
        fetchBanners();
    }

    function deleteBanner(bannerId) {
        let adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa banner này không?")) {
            fetch(`{{ url('/api/banners/') }}/${bannerId}`, {
                    method: "DELETE",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer " + adminToken
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        alert("Chưa đăng nhập, vui lòng đăng nhập!");
                        window.location.href = "{{ url('admin-login') }}";
                        return;
                    }

                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Xóa banner thành công!");
                        fetchBanners();
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa."));
                    }
                })
                .catch(error => alert(error.message));
        }
    }

    function updateBannerStatus(bannerId, newStatus, element) {
        let adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch(`{{ url('/api/banners/') }}/${bannerId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken
                },
                body: JSON.stringify({
                    banner_status: newStatus
                })
            })
            .then(response => {
                if (response.status === 401) {
                    alert("Chưa đăng nhập, vui lòng đăng nhập!");
                    window.location.href = "{{ url('admin-login') }}";
                    return;
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert("Cập nhật trạng thái thành công!");
                    element.setAttribute("data-status", newStatus);
                    element.innerHTML = newStatus == 1 ?
                        '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
                        '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>';
                } else {
                    alert("Lỗi từ server: " + (data.message || "Không thể cập nhật."));
                }
            })
            .catch(error => alert(error.message));
    }

    document.addEventListener("DOMContentLoaded", function() {
        fetchBanners();
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
        border: 1px solid #0056b3;
    }
</style>
@endsection