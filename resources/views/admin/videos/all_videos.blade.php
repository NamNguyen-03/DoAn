@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-video')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm Video
            </a>
            Liệt kê Videos
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
                        <button class="btn btn-sm btn-default" type="button" onclick="searchVideos()">Search</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="productTable">
                <thead>
                    <tr>
                        <th style="width: 40px;">STT</th>
                        <th style="width: 120px;">Title</th>
                        <th style="width: 120px;">Slug</th>
                        <th style="width: 180px;">Video Desc</th>
                        <th style="width: 150px;">Hình ảnh</th>
                        <th style="width: 300px;">Video</th>
                        <th style="width: 80px;">Hiển thị</th>
                        <th style="width:40px;"></th>
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
    let allVideos = [];
    let currentPage = 1;
    const itemsPerPage = 10;
    let adminToken = localStorage.getItem("admin_token");

    function extractYoutubeId(link) {
        try {
            const url = new URL(link);
            return url.searchParams.get("v") || url.pathname.split("/").pop();
        } catch (error) {
            return "";
        }
    }

    function fetchVideos() {
        const url = `{{ url('/api/videos') }}?search=${encodeURIComponent(searchQuery)}`;
        fetch(url, {
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem('admin_token')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('API lỗi hoặc không thể kết nối.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    allVideos = data.data;
                    renderPaginatedVideos();
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

    function renderPaginatedVideos() {
        let startIndex = (currentPage - 1) * itemsPerPage;
        let endIndex = startIndex + itemsPerPage;
        let paginatedVideos = allVideos.slice(startIndex, endIndex);

        let tableBody = document.querySelector("#productTable tbody");
        tableBody.innerHTML = "";

        if (paginatedVideos.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='8' class='text-center'>Không có video nào.</td></tr>";
            return;
        }

        paginatedVideos.forEach((video, index) => {
            const videoId = extractYoutubeId(video.video_link);
            const iframe = `<iframe width="100%" height="240" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`;
            const imageThumb = video.video_thumb ? `<img src="{{ asset('uploads/video_thumbs') }}/${video.video_thumb}" width="100%">` : '';
            // const statusText = video.video_status == 1 ? "Hiển thị" : "Ẩn";

            const row = `
            <tr>
                <td>${startIndex + index + 1}</td>
                <td style="word-break: break-word;">${video.video_title}</td>
                <td style="word-break: break-word;">${video.video_slug}</td>
                <td style="word-break: break-word;">${video.video_desc.length > 100 ? video.video_desc.substring(0, 100) + '...' : video.video_desc}</td>
                <td>${imageThumb}</td>
                <td>${iframe}</td>
                <td>
                        <a href="javascript:void(0)" class="toggle-status" data-slug="${video.video_slug}" data-status="${video.video_status}">
                            ${video.video_status == 1 ?
                                '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
                                '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>'}
                        </a>
                    </td>
                <td>
                    <a href="/admin/edit-video/${video.video_slug}" class="active">
                        <i class="fa fa-pencil-square-o text-success text-active"></i>
                    </a>
                    <a href="javascript:void(0)" class="active" onclick="deleteVideos('${video.video_slug}')">
                        <i class="fa fa-trash text"></i>
                    </a>
                    
                </td>
            </tr>
        `;
            tableBody.insertAdjacentHTML("beforeend", row);
        });
        document.querySelectorAll('.toggle-status').forEach(el => {
            el.addEventListener('click', function() {
                let videoSlug = this.getAttribute("data-slug");
                let currentStatus = this.getAttribute("data-status");
                let newStatus = currentStatus == 1 ? 0 : 1;
                updateVideoStatus(videoSlug, newStatus, this);
            });
        });
        renderPagination();
    }



    function renderPagination() {
        const totalPages = Math.ceil(allVideos.length / itemsPerPage);
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
                renderPaginatedVideos();
            });
            pagination.appendChild(pageLink);
        }
    }

    function searchVideos() {
        searchQuery = document.getElementById('searchInput').value;
        currentPage = 1;
        fetchVideos();
    }

    document.addEventListener("DOMContentLoaded", function() {
        fetchVideos();
    });

    function updateVideoStatus(videoSlug, newStatus, element) {

        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch(`{{ url('/api/videos') }}/${videoSlug}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken
                },
                body: JSON.stringify({
                    video_status: newStatus
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

    function deleteVideos(videoSlug) {
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }
        if (confirm("Bạn có chắc chắn muốn xóa video này không?")) {
            fetch(`{{ url('/api/videos') }}/${videoSlug}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": "Bearer " + adminToken
                    },

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
                        alert("Xóa video thành công!");
                        fetchVideos();
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa video."));
                    }
                })
                .catch(error => alert(error.message));

        }
    }
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