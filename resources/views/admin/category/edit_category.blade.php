@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ URL::previous() }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px; width: 40px; height: 40px;">
                </a>
                Cập nhật danh mục sản phẩm
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="updateCategoryForm" role="form" class="form-validate" action="" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="category_product_name">Tên danh mục</label>
                            <input type="text" name="category_name" onkeyup="ChangeToSlug();" class="form-control" id="slug" placeholder="Tên danh mục" required>
                        </div>
                        <div class="form-group">
                            <label for="category_parent">Thuộc danh mục</label>
                            <select name="category_parent" class="form-control input-sm m-bot15" id="category_parent" required>
                                <option value="0">Danh mục cha</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category_slug">Slug</label>
                            <input type="text" name="category_slug" id="convert_slug" class="form-control" placeholder="Slug" required>
                        </div>
                        <div class="form-group">
                            <label for="category_desc">Mô tả</label>
                            <textarea rows="5" class="form-control" name="category_desc" id="category_desc" style="resize:none"></textarea>
                        </div>

                        <button type="submit" class="btn btn-info">Cập nhật</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const categorySlug = "{{ $category_slug }}"; // Nhận category_id từ Blade

        if (!categorySlug) {
            alert("Không tìm thấy danh mục.");
            window.location.href = "/admin/all-category";
            return;
        }

        // Lấy dữ liệu danh mục
        fetchCategoryData(categorySlug);

        // Bắt sự kiện submit form
        document.getElementById("updateCategoryForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Ngăn chặn load lại trang
            updateCategory(categorySlug);
        });
    });

    function fetchCategoryData(categorySlug) {
        fetch(`/api/categories/${categorySlug}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateCategoryData(data.data);
                    loadCategoryParents(data.data.category_parent);
                } else {
                    alert("Không tìm thấy danh mục.");
                    window.location.href = "/admin/all-category";
                }
            })
            .catch(error => console.error("Lỗi khi lấy dữ liệu danh mục:", error));
    }

    function populateCategoryData(categoryData) {
        const categoryName = document.getElementsByName("category_name")[0];
        const categoryslug = document.getElementsByName("category_slug")[0];
        const categoryDesc = document.getElementsByName("category_desc")[0];

        if (categoryName) categoryName.value = categoryData.category_name;
        if (categoryslug) categoryslug.value = categoryData.category_slug;
        if (categoryDesc) categoryDesc.value = categoryData.category_desc || "";
    }

    function loadCategoryParents(selectedId) {
        fetch("/api/categories")
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    const categorySelect = document.getElementsByName("category_parent")[0];
                    categorySelect.innerHTML = '<option value="0">Danh mục cha</option>';

                    // Lọc các danh mục không có parent (parent_id = 0)
                    const parentCategories = data.data.filter(category => category.category_parent === 0);

                    parentCategories.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category.category_id;
                        option.textContent = category.category_name;

                        if (category.category_id == selectedId) {
                            option.selected = true;
                        }

                        categorySelect.appendChild(option);
                    });
                } else {
                    console.error("Dữ liệu không hợp lệ hoặc không có danh mục cha.");
                }
            })
            .catch(error => console.error("Lỗi khi lấy danh mục cha:", error));
    }


    function updateCategory(categorySlug) {
        const categoryName = document.getElementsByName("category_name")[0].value;
        const categoryslug = document.getElementsByName("category_slug")[0].value;
        const categoryDesc = document.getElementsByName("category_desc")[0].value;
        const categoryParent = document.getElementsByName("category_parent")[0].value;
        const adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "/admin-login"; // Chuyển hướng nếu không có token
            return;
        }

        fetch(`/api/categories/${categorySlug}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${adminToken}`
                },
                body: JSON.stringify({
                    category_name: categoryName,
                    category_slug: categoryslug,
                    category_desc: categoryDesc,
                    category_parent: categoryParent
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
                    alert("Cập nhật danh mục thành công!");
                    window.location.href = "/admin/all-category"; // Chuyển hướng sau khi cập nhật thành công
                } else {
                    alert("Lỗi khi cập nhật danh mục: " + data.message);
                }
            })
            .catch(error => console.error("Lỗi khi cập nhật danh mục:", error));
    }
</script>



@endsection