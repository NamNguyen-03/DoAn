@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-product')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm sản phẩm
            </a>
            Liệt kê sản phẩm
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
                        <button class="btn btn-sm btn-default" type="button" onclick="searchProducts()">Search</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="productTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sản phẩm</th>
                        <th>SL kho</th>
                        <th>Giá sản phẩm</th>
                        <th>Hình ảnh</th>
                        <th>Thư viện ảnh</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th>Mô tả</th>
                        <th>Nội dung</th>
                        <th>Hiển thị</th>
                        <th style="width:30px;"></th>
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
    let allProducts = []; // Lưu trữ tất cả sản phẩm
    let currentPage = 1; // Theo dõi trang hiện tại
    const perPage = 10; // Số lượng sản phẩm trên mỗi trang
    let searchQuery = ""; // Từ khóa tìm kiếm

    // Fetch sản phẩm từ API
    function fetchProducts() {
        const url = `{{ url('/api/products') }}?search=${encodeURIComponent(searchQuery)}`;
        fetch(url)
            .then(response => response.json())
            .then(abc => {
                if (abc.success) {
                    allProducts = abc.data; // Lưu trữ tất cả sản phẩm
                    renderProducts(currentPage); // Hiển thị sản phẩm cho trang hiện tại
                    updatePagination(); // Cập nhật điều khiển phân trang
                }
            })
            .catch(error => console.error("Lỗi khi lấy sản phẩm:", error));
    }

    // Hiển thị sản phẩm trong bảng
    function renderProducts(page) {
        const start = (page - 1) * perPage; // Tính chỉ số bắt đầu
        const end = start + perPage; // Tính chỉ số kết thúc
        const productsToDisplay = allProducts.slice(start, end); // Lấy sản phẩm cho trang hiện tại

        let tableBody = document.querySelector("#productTable tbody");
        tableBody.innerHTML = ""; // Xóa các hàng hiện có

        productsToDisplay.forEach((product, index) => {
            let row = `
            <tr>
                <td>${start + index + 1}</td>
                <td>${product.product_name.length > 100 ? product.product_name.substring(0, 100) + '...' : product.product_name}</td>
                <td>${product.product_quantity}</td>
                <td>${product.product_price.toLocaleString()} đ</td>
                <td><img src="{{asset('uploads/product/')}}/${product.product_image}" width="50"></td>
                <td><a href="/admin/product-gallery/${product.product_id}">Xem</a></td>
                <td>${product.category.category_name}</td> <!-- Lấy category_name từ product -->
                <td>${product.brand.brand_name}</td> <!-- Lấy brand_name từ product -->
                <td>${product.product_desc.length > 100 ? product.product_desc.substring(0, 100) + '...' : product.product_desc}</td>
                <td>${product.product_content.length > 100 ? product.product_content.substring(0, 100) + '...' : product.product_content}</td>
                <td>
                    <a href="javascript:void(0)" class="toggle-status" data-slug="${product.product_slug}" data-status="${product.product_status}">
                        ${product.product_status == 1 ? 
                            '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' : 
                            '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>'}
                    </a>
                </td>
                <td>
                    <a href="/admin/edit-product/${product.product_slug}" class="active">
                        <i class="fa fa-pencil-square-o text-success text-active"></i>
                    </a>
                    <a href="javascript:void(0)" class="active" onclick="deleteProduct('${product.product_slug}')">
                        <i class="fa fa-trash text"></i>
                    </a>
                </td>
            </tr>`;
            tableBody.innerHTML += row;
        });

        // Xử lý sự kiện đổi trạng thái sản phẩm
        document.querySelectorAll(".toggle-status").forEach(link => {
            link.addEventListener("click", function() {
                let productSlug = this.getAttribute("data-slug");
                let currentStatus = this.getAttribute("data-status");
                let newStatus = currentStatus == 1 ? 0 : 1;
                updateProductStatus(productSlug, newStatus, this);
            });
        });
    }

    // Cập nhật điều khiển phân trang
    function updatePagination() {
        const totalPages = Math.ceil(allProducts.length / perPage);
        const paginationDiv = document.getElementById("pagination");
        paginationDiv.innerHTML = ""; // Xóa điều khiển phân trang hiện có

        for (let i = 1; i <= totalPages; i++) {
            const pageLink = document.createElement("a");
            pageLink.href = "#"; // Sử dụng # cho liên kết
            pageLink.className = "page-link"; // Thêm lớp cho kiểu dáng
            pageLink.innerText = i; // Đặt số trang làm văn bản liên kết

            // Thêm sự kiện cho các sự kiện click
            pageLink.addEventListener("click", function(e) {
                e.preventDefault(); // Ngăn chặn hành vi mặc định của liên kết
                currentPage = i; // Cập nhật trang hiện tại
                renderProducts(currentPage); // Hiển thị sản phẩm cho trang mới
                updatePagination(); // Cập nhật điều khiển phân trang
            });

            // Nổi bật trang hiện tại
            if (i === currentPage) {
                pageLink.classList.add("active"); // Thêm lớp active cho kiểu dáng
            }

            paginationDiv.appendChild(pageLink); // Thêm liên kết vào container phân trang
        }
    }

    // Tìm kiếm sản phẩm
    function searchProducts() {
        searchQuery = document.getElementById('searchInput').value; // Cập nhật từ khóa tìm kiếm
        currentPage = 1; // Đặt lại về trang đầu tiên
        fetchProducts(); // Gọi lại API với từ khóa tìm kiếm
    }

    document.addEventListener("DOMContentLoaded", function() {
        fetchProducts();
    });

    function updateProductStatus(productSlug, newStatus, element) {
        let adminToken = localStorage.getItem("admin_token");

        // Nếu không có token, chuyển hướng về trang đăng nhập
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng đến trang đăng nhập
            return; // Dừng lại và không tiếp tục gửi request
        }
        fetch(`{{ url('/api/products/') }}/${productSlug}`, { // Ensure the correct endpoint
                method: "PUT", // Use uppercase for the method
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken // Include CSRF token if needed
                },
                body: JSON.stringify({
                    product_status: newStatus // Send only the product_status
                })
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

    function deleteProduct(productSlug) {
        let adminToken = localStorage.getItem("admin_token");

        // Nếu không có token, chuyển hướng về trang đăng nhập
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng đến trang đăng nhập
            return; // Dừng lại và không tiếp tục gửi request
        }

        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
            fetch(`{{ url('/api/products/') }}/${productSlug}`, {
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
                        alert("Xóa sản phẩm thành công!");
                        fetchProducts();
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa sản phẩm."));
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