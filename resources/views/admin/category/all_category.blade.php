@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-category')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm danh mục
            </a>
            Liệt kê danh mục sản phẩm
        </div>
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <button id="showAllBtn">Hiện tất cả danh mục</button>
            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchCategories()">Go</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="categoryTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên danh mục</th>
                        <th>Danh mục cha</th>
                        <th>Slug</th>
                        <th>Mô tả danh mục</th>
                        <th>Hiển thị</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="sortableParents">
                </tbody>
            </table>
        </div>

        <div id="pagination" class="text-center" style="margin-top: 20px;"></div>
    </div>
</div>

<script>
    let allCategories = []; // Lưu trữ tất cả danh mục
    let currentPage = 1; // Theo dõi trang hiện tại
    let perPage = 3; // Số lượng danh mục trên mỗi trang
    let searchQuery = ""; // Từ khóa tìm kiếm
    const adminToken = localStorage.getItem("admin_token");
    let showAll = false;

    function searchCategories() {
        searchQuery = document.getElementById('searchInput').value; // Cập nhật từ khóa tìm kiếm
        currentPage = 1; // Đặt lại về trang đầu tiên
        fetchCategories(); // Gọi lại API với từ khóa tìm kiếm
    }

    function fetchCategories() {
        const url = `{{ url('/api/categories') }}?search=${encodeURIComponent(searchQuery)}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allCategories = data.data; // Lưu trữ tất cả danh mục
                    renderCategories(currentPage); // Hiển thị danh mục cho trang hiện tại

                    // Cập nhật điều khiển phân trang
                } else {
                    console.error(data.message); // In ra thông báo lỗi nếu có
                }
            })
            .catch(error => console.error("Lỗi khi lấy danh mục:", error));
    }


    function renderCategories(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;

        // 1. Lọc các danh mục cha (category_parent == 0)
        const parents = allCategories
            .filter(category => category.category_parent == 0)
            .sort((a, b) => a.category_order - b.category_order); // Sắp xếp cha theo category_order

        const categoriesToDisplay = parents.slice(start, end);

        let tableBody = document.querySelector("#categoryTable tbody");
        tableBody.innerHTML = "";

        categoriesToDisplay.forEach(parent => {
            const parentRow = generateCategoryRow(parent, false);
            tableBody.innerHTML += parentRow;

            // 2. Tìm các danh mục con tương ứng với cha này
            const children = allCategories
                .filter(category => category.category_parent == parent.category_id)
                .sort((a, b) => a.category_order - b.category_order); // Sắp xếp con theo category_order

            children.forEach(child => {
                const childRow = generateCategoryRow(child, true);
                tableBody.innerHTML += childRow;
            });
        });

        enableDragAndDrop(); // Gọi hàm để bật kéo thả
        updatePagination();
        // Gán lại event toggle-status
        document.querySelectorAll(".toggle-status").forEach(link => {
            link.addEventListener("click", function() {
                let categorySlug = this.getAttribute("data-slug");
                let currentStatus = this.getAttribute("data-status");
                let newStatus = currentStatus == 1 ? 0 : 1;
                updateCategoryStatus(categorySlug, newStatus, this);
            });
        });
    }


    function generateCategoryRow(category, isChild = false) {
        const indentStyle = isChild ? 'background-color:rgb(224, 252, 253);' : '';
        const parentName = category.parent ? category.parent.category_name : "Không có";
        const rowClass = isChild ? "group-child" : "";
        const parentAttr = isChild ? `data-parent-id="${category.category_parent}"` : "";


        return `
                 <tr data-id="${category.category_id}" ${parentAttr} style="${indentStyle}" class="${rowClass}">
                    <td style="color: ${isChild ? 'inherit' : 'red'}; font-weight: ${isChild ? 'normal' : 'bold'};font-size:${isChild?'12px':'18px'}">
                        ${category.category_order}
                    </td>
                    <td>${category.category_name}</td>
                    <td>${parentName}</td>
                    <td>${category.category_slug}</td>
                    <td>${category.category_desc.length > 150 ? category.category_desc.substr(0, 150) : category.category_desc}</td>
                    <td>
                        <a href="javascript:void(0)" class="toggle-status" data-slug="${category.category_slug}" data-status="${category.category_status}">
                            ${category.category_status == 1 ?
                                '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
                                '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>'}
                        </a>
                    </td>
                    <td>
                        <a href="/admin/edit-category/${category.category_slug}" class="active">
                            <i class="fa fa-pencil-square-o text-success text-active"></i>
                        </a>
                        <a onclick="deleteCategory('${category.category_slug}')" href="javascript:void(0)" class="active">
                            <i class="fa fa-trash text"></i>
                        </a>
                    </td>
                </tr>`;
    }








    document.addEventListener("DOMContentLoaded", function() {
        fetchCategories(); // Gọi hàm để lấy danh mục khi trang được tải
    });



    function updatePagination() {

        const parents = allCategories.filter(c => c.category_parent == 0);
        const totalPages = Math.ceil(parents.length / perPage);
        const paginationDiv = document.getElementById("pagination");

        paginationDiv.innerHTML = "";
        for (let i = 1; i <= totalPages; i++) {
            const pageLink = document.createElement("a");
            pageLink.href = "#"; // Sử dụng # cho liên kết
            pageLink.className = "page-link"; // Thêm lớp cho kiểu dáng
            pageLink.innerText = i; // Đặt số trang làm văn bản liên kết

            // Thêm sự kiện cho các sự kiện click
            pageLink.addEventListener("click", function(e) {
                e.preventDefault(); // Ngăn chặn hành vi mặc định của liên kết
                currentPage = i; // Cập nhật trang hiện tại
                renderCategories(currentPage); // Hiển thị danh mục cho trang mới
                updatePagination(); // Cập nhật điều khiển phân trang
            });

            // Nổi bật trang hiện tại
            if (i === currentPage) {
                pageLink.classList.add("active"); // Thêm lớp active cho kiểu dáng
            }

            paginationDiv.appendChild(pageLink); // Thêm liên kết vào container phân trang
        }
    }
    document.getElementById("showAllBtn").addEventListener("click", () => {
        showAll = !showAll;
        document.getElementById("showAllBtn").innerText = showAll ? "Phân trang lại" : "Hiện tất cả danh mục";
        if (showAll) {
            perPage = 1000;
        } else {
            perPage = 3;
        }
        renderCategories(1);

    });
</script>
<script>
    let draggedChildren = [];

    function enableDragAndDrop() {
        $("#categoryTable tbody").sortable({
            items: "> tr", // Cho phép kéo tất cả các dòng
            placeholder: "ui-state-highlight",

            start: function(event, ui) {
                const currentRow = ui.item;
                const currentId = currentRow.data("id");

                draggedChildren = [];

                // Nếu đang kéo cha
                if (!currentRow.hasClass("group-child")) {
                    currentRow.nextAll().each(function() {
                        const row = $(this);
                        if (row.hasClass("group-child") && row.data("parent-id") == currentId) {
                            draggedChildren.push(row);
                        } else {
                            return false;
                        }
                    });

                    draggedChildren.forEach(row => row.hide());
                }
            },

            stop: function(event, ui) {
                const currentRow = ui.item;

                // Nếu là dòng cha thì insert lại con
                if (!currentRow.hasClass("group-child")) {
                    draggedChildren.reverse().forEach(child => {
                        child.insertAfter(currentRow);
                        child.show();
                    });
                }

                draggedChildren = [];
            },
            update: function(event, ui) {
                const parentOrder = [];
                const childOrderMap = {};

                let currentParentId = null;

                const offset = perPage * (currentPage - 1); // Tính offset theo trang

                let parentIndex = 0; // Đếm thứ tự cha

                $("#categoryTable tbody > tr").each(function() {
                    const row = $(this);
                    const rowId = row.data("id");

                    if (!row.hasClass("group-child")) {
                        // Cha
                        parentOrder.push({
                            category_id: rowId,
                            category_order: offset + parentIndex + 1
                        });
                        currentParentId = rowId;
                        childOrderMap[currentParentId] = [];
                        parentIndex++;
                    } else {
                        // Con
                        if (currentParentId) {
                            childOrderMap[currentParentId].push(rowId);
                        }
                    }
                });

                console.log("parents:", parentOrder);
                console.log("children:", childOrderMap);

                // Gửi dữ liệu lên server
                fetch('/api/categories/update-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            "Authorization": "Bearer " + adminToken,
                        },
                        body: JSON.stringify({
                            parents: parentOrder,
                            children: childOrderMap
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message || "Cập nhật thứ tự thành công!");
                        fetchCategories();
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Đã xảy ra lỗi khi cập nhật thứ tự!");
                    });
            }

            // update: function(event, ui) {
            //     const parentOrder = [];
            //     const childOrderMap = {};

            //     let currentParentId = null;

            //     $("#categoryTable tbody > tr").each(function() {
            //         const row = $(this);
            //         const rowId = row.data("id");

            //         if (!row.hasClass("group-child")) {
            //             // Cha
            //             parentOrder.push(rowId);
            //             currentParentId = rowId;
            //             childOrderMap[currentParentId] = [];
            //         } else {
            //             // Con
            //             if (currentParentId) {
            //                 childOrderMap[currentParentId].push(rowId);
            //             }
            //         }
            //     });

            //     console.log("parents:", parentOrder);
            //     console.log("children:", childOrderMap);

            //     // Gửi dữ liệu lên server
            //     fetch('/api/categories/update-order', {
            //             method: 'POST',
            //             headers: {
            //                 'Content-Type': 'application/json',
            //                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
            //                 "Authorization": "Bearer " + adminToken,
            //             },
            //             body: JSON.stringify({
            //                 parents: parentOrder,
            //                 children: childOrderMap
            //             })
            //         })
            //         .then(res => res.json())
            //         .then(data => {
            //             alert(data.message || "Cập nhật thứ tự thành công!");
            //             fetchCategories();
            //         })
            //         .catch(err => {
            //             console.error(err);
            //             alert("Đã xảy ra lỗi khi cập nhật thứ tự!");
            //         });
            // }
        });
    }
</script>

<script>
    function updateCategoryStatus(categorySlug, newStatus, element) {
        // 🛡️ Lấy token từ localStorage
        if (!adminToken) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            return;
        }
        fetch(`{{ url('/api/categories/') }}/${categorySlug}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken, // 🛡️ Gửi token trong header

                },
                body: JSON.stringify({
                    category_status: newStatus
                })
            })
            .then(response => {
                if (response.status === 401) {
                    // Nếu API trả về 401, chuyển hướng đến trang login
                    alert("Token không hợp lệ. Bạn cần đăng nhập lại.");
                    window.location.href = "/admin-login";
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

    function deleteCategory(categoryId) {
        const adminToken = localStorage.getItem("admin_token"); // 🛡️ Lấy token từ localStorage
        if (!adminToken) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa danh mục này không?")) {
            fetch(`{{ url('/api/categories/') }}/${categoryId}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": "Bearer " + adminToken, // 🛡️ Gửi token trong header
                        // "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        // Nếu API trả về 401, chuyển hướng đến trang login
                        alert("Token không hợp lệ. Bạn cần đăng nhập lại.");
                        window.location.href = "/admin-login";
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Xóa danh mục thành công!");
                        fetchCategories(); // Cập nhật danh sách sau khi xóa
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa danh mục."));
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

    .ui-state-highlight {
        background-color: #e0e0e0;
        height: 40px;
    }
</style>
@endsection