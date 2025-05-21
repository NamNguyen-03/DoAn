@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-brand')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm thương hiệu
            </a>
            Liệt kê thương hiệu
        </div>

        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <button id="showAllBtn">Hiện tất cả thương hiệu</button>

            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchBrands()">Search</button>
                    </span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="brandTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên thương hiệu</th>
                        <th>Slug</th>
                        <th>Mô tả thương hiệu</th>
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
    let allBrands = []; // Store all brands
    let currentPage = 1; // Track the current page
    let perPage = 10; // Number of items per page
    let searchQuery = ""; // Search query
    let showAll = false;
    const adminToken = localStorage.getItem("admin_token");

    function fetchBrands() {
        const url = `{{ url('/api/brands') }}?search=${encodeURIComponent(searchQuery)}`; // Include search query in the API call
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('API lỗi hoặc không thể kết nối.');
                }
                return response.json();
            })
            .then(data => {
                console.log(data); // Log to check if the data is correct
                if (data.success && Array.isArray(data.data)) {
                    allBrands = data.data; // Store all brands
                    renderBrands(currentPage); // Render brands for the first page
                    updatePagination(); // Update pagination controls
                } else {
                    console.error("Dữ liệu trả về không hợp lệ:", data);
                    alert("Lỗi khi lấy dữ liệu. Vui lòng kiểm tra lại API.");
                }
            })
            .catch(error => {
                console.error("Lỗi khi gọi API:", error);
                alert("Không thể lấy dữ liệu từ server.");
            });
    }

    function renderBrands(page) {
        const start = (page - 1) * perPage; // Calculate start index
        const end = start + perPage; // Calculate end index
        const brands = allBrands
            .sort((a, b) => a.brand_order - b.brand_order);
        const brandsToDisplay = brands.slice(start, end); // Get brands for the current page

        let tableBody = document.querySelector("#brandTable tbody");
        tableBody.innerHTML = ""; // Clear existing rows

        if (brandsToDisplay.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='5' class='text-center'>Không có thương hiệu nào.</td></tr>";
            return;
        }

        brandsToDisplay.forEach((brand, index) => {
            let row = `
                    <tr data-id="${brand.brand_id}">
                        <td>${start + index + 1}</td>
                        <td>${brand.brand_name}</td>
                        <td>${brand.brand_slug}</td>
                        <td>${brand.brand_desc}</td>
                        <td>
                            <a href="javascript:void(0)" class="toggle-status" data-slug="${brand.brand_slug}" data-status="${brand.brand_status}">
                                ${brand.brand_status == 1 ? 
                                    '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' : 
                                    '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>'}
                            </a>
                        </td>
                        <td>
                            <a href="/admin/edit-brand/${brand.brand_slug}" class="active">
                                <i class="fa fa-pencil-square-o text-success text-active"></i>
                            </a>
                            <a href="javascript:void(0)" class="active" onclick="deleteBrand('${brand.brand_slug}')">
                                <i class="fa fa-trash text"></i>
                            </a>
                        </td>
                    </tr>`;
            tableBody.innerHTML += row;
        });


        // Add event listeners for status toggle
        document.querySelectorAll(".toggle-status").forEach(link => {
            link.addEventListener("click", function() {
                let brandSlug = this.getAttribute("data-slug");
                let currentStatus = this.getAttribute("data-status");
                let newStatus = currentStatus == 1 ? 0 : 1;
                updateBrandStatus(brandSlug, newStatus, this);
            });
        });
    }

    function updatePagination() {
        const totalPages = Math.ceil(allBrands.length / perPage);
        const paginationDiv = document.getElementById("pagination");
        paginationDiv.innerHTML = ""; // Clear existing pagination

        for (let i = 1; i <= totalPages; i++) {
            const pageLink = document.createElement("a");
            pageLink.href = "#"; // Use # for the link
            pageLink.className = "page-link"; // Add the class for styling
            pageLink.innerText = i; // Set the page number as the link text

            // Add an event listener for click events
            pageLink.addEventListener("click", function(e) {
                e.preventDefault(); // Prevent default anchor behavior
                currentPage = i; // Update current page
                renderBrands(currentPage); // Render brands for the new page
                updatePagination(); // Update pagination controls
            });

            // Highlight the active page
            if (i === currentPage) {
                pageLink.classList.add("active"); // Add active class for styling
            }

            paginationDiv.appendChild(pageLink); // Append the link to the pagination container
        }
    }

    function searchBrands() {
        searchQuery = document.getElementById('searchInput').value; // Update search query
        currentPage = 1; // Reset to the first page
        fetchBrands(); // Fetch brands with the search query
    }

    document.addEventListener("DOMContentLoaded", function() {
        fetchBrands(); // Call when the page loads


    });
    document.getElementById("showAllBtn").addEventListener("click", () => {
        showAll = !showAll;
        document.getElementById("showAllBtn").innerText = showAll ? "Phân trang lại" : "Hiện tất cả danh mục";
        if (showAll) {
            perPage = 1000;
        } else {
            perPage = 10;
        }
        renderBrands(1);

    });
</script>
<script>
    $(function() {
        $("#brandTable tbody").sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                let sortedData = [];
                let offset = perPage * (currentPage - 1); // Số phần tử đã bỏ qua trước đó

                $("#brandTable tbody tr").each(function(index) {
                    let brandId = $(this).data("id");
                    let order = offset + index + 1; // Thứ tự mới (bắt đầu từ 1)

                    sortedData.push({
                        brand_id: brandId,
                        brand_order: order
                    });
                });
                console.log(sortedData);

                // Gửi thứ tự mới về server
                fetch(`{{ url('/api/brands/update-order') }}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Authorization": "Bearer " + adminToken,
                        },
                        body: JSON.stringify({
                            sorted: sortedData
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Cập nhật thứ tự thành công");
                            fetchBrands(); // Load lại để cập nhật STT nếu cần
                        } else {
                            alert("Cập nhật thất bại");
                        }
                    })
                    .catch(error => {
                        console.error("Lỗi:", error);
                        alert("Lỗi khi cập nhật thứ tự.");
                    });
            }

        });
        $("#brandTable tbody").disableSelection();
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
<script>
    function updateBrandStatus(brandSlug, newStatus, element) {

        fetch(`{{ url('/api/brands/') }}/${brandSlug}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken,
                },
                body: JSON.stringify({
                    brand_status: newStatus
                })
            })
            .then(response => {
                // Kiểm tra nếu mã lỗi là 401
                if (response.status === 401) {
                    alert("Bạn cần đăng nhập để thực hiện hành động này.");
                    window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng về trang đăng nhập
                    return; // Dừng lại và không xử lý tiếp
                }

                return response.json(); // Nếu không phải lỗi 401, tiếp tục xử lý dữ liệu
            })
            .then(data => {
                if (data && data.success) {
                    alert("Cập nhật trạng thái thành công!");
                    element.setAttribute("data-status", newStatus);
                    element.innerHTML = newStatus == 1 ?
                        '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
                        '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>';
                } else {
                    alert("Lỗi từ server: " + (data.message || "Không thể cập nhật."));
                }
            })
            .catch(error => alert("Lỗi khi cập nhật trạng thái: " + error.message));
    }
</script>

<script>
    function deleteBrand(brandSlug) {
        if (!adminToken) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa thương hiệu này không?")) {
            fetch(`{{ url('/api/brands/') }}/${brandSlug}`, {
                    method: "DELETE",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer " + adminToken,
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        alert("Bạn cần đăng nhập để thực hiện thao tác này!");
                        window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng đến trang đăng nhập
                        return; // Dừng lại và không xử lý tiếp
                    }

                    return response.json(); // Nếu không phải lỗi 401, tiếp tục xử lý dữ liệu
                })
                .then(data => {
                    if (data.success) {
                        alert("Xóa thương hiệu thành công!");
                        fetchBrands(); // Tải lại danh sách
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa."));
                    }
                })
                .catch(error => alert("Lỗi khi xóa thương hiệu: " + error.message));
        }
    }
</script>
@endsection