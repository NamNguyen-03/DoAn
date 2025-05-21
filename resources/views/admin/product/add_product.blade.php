@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{url('/admin/dashboard') }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{url('/admin/all-product')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách sản phẩm
                </a>
                Thêm sản phẩm
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="addProductForm" class="form-validate" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tên sản phẩm</label>
                            <input type="text" name="product_name" onkeyup="ChangeToSlug();" class="form-control" id="slug" placeholder="Tên sản phẩm">
                        </div>
                        <div class="form-group">
                            <label for="product_price">Giá sản phẩm</label>
                            <input type="text" name="product_price" class="form-control product_price" placeholder="Giá sản phẩm" required>
                        </div>
                        <div class="form-group">
                            <label for="product_price">Giá Nhập sản phẩm</label>
                            <input type="text" name="product_price_in" class="form-control product_price_in" placeholder="Giá Nhập sản phẩm" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Số lượng sản phẩm</label>
                            <input type="number" name="product_quantity" class="form-control" id="product_quantity" placeholder="Số lượng sản phẩm">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Slug</label>
                            <input type="text" name="product_slug" class="form-control" id="convert_slug" placeholder="Slug">
                        </div>
                        <div class="form-group">
                            <label for="product_image">Hình ảnh</label>
                            <input type="file" name="product_image" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="product_desc">Mô tả</label>
                            <textarea rows="5" class="form-control" name="product_desc" id="product_desc" placeholder="Mô tả sản phẩm" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_content">Nội dung</label>
                            <textarea rows="5" class="form-control" name="product_content" id="product_content" placeholder="Nội dung sản phẩm" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tags</label>
                            <input type="text" data-role=tagsinput name="product_tags" class="form-control" placeholder="Tags">
                        </div>
                        <div class="form-group">
                            <label for="category_id">Danh mục</label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">Chọn</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="brand_id">Thương hiệu</label>
                            <select name="brand_id" id="brand_id" class="form-control" required>
                                <option value="">Chọn</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product_status">Hiển thị</label>
                            <select name="product_status" class="form-control" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Thêm sản phẩm</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ url('/api/categories') }}")
            .then(response => response.json())
            .then(data => {
                console.log("Dữ liệu từ API category:", data); // Log dữ liệu category

                if (data.success && Array.isArray(data.data)) { // Sửa lại kiểm tra đúng cấu trúc
                    let categorySelect = document.querySelector("#category_id");

                    // Lọc các danh mục có category_parent != 0
                    let filteredCategories = data.data.filter(category => category.category_parent != 0);

                    // Duyệt qua các danh mục đã lọc
                    filteredCategories.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category.category_id;
                        option.textContent = category.category_name;
                        categorySelect.appendChild(option);
                    });
                } else {
                    console.error("Dữ liệu không hợp lệ hoặc không có danh mục.");
                }
            })
            .catch(error => console.error("Lỗi khi lấy danh mục:", error));

        fetch("{{ url('/api/brands') }}")
            .then(response => response.json())
            .then(data => {
                console.log("Dữ liệu từ API brand:", data); // Log dữ liệu brand

                if (data.success && Array.isArray(data.data)) { // Sửa lại kiểm tra đúng cấu trúc
                    let brandSelect = document.querySelector("#brand_id");
                    data.data.forEach(brand => {
                        let option = document.createElement("option");
                        option.value = brand.brand_id;
                        option.textContent = brand.brand_name;
                        brandSelect.appendChild(option);
                    });
                } else {
                    console.error("Dữ liệu không hợp lệ hoặc không có thương hiệu.");
                }
            })
            .catch(error => console.error("Lỗi khi lấy thương hiệu:", error));
    });





    document.querySelector("#addProductForm").addEventListener("submit", function(event) {
        event.preventDefault();
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        let formData = new FormData(this);
        let adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }
        let productPrice = formData.get("product_price");
        console.log(productPrice)
        if (productPrice) {
            productPrice = productPrice.replace(/,/g, "");
            formData.set("product_price", productPrice);
        }
        let productPriceIn = formData.get("product_price_in");
        console.log(productPriceIn)
        if (productPriceIn) {
            productPriceIn = productPriceIn.replace(/,/g, "");
            formData.set("product_price_in", productPriceIn);
        }
        // Hiển thị dữ liệu gửi đi
        console.log("Dữ liệu gửi đi:");
        for (let pair of formData.entries()) {
            console.log(`${pair[0]}:`, pair[1]);
        }

        fetch("{{ url('/api/products') }}", {
                method: "POST",
                body: formData,
                headers: {
                    // "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    "Authorization": "Bearer " + adminToken
                }
            })
            .then(response => {
                if (response.status === 401) {
                    alert("Chưa đăng nhập, vui lòng đăng nhập!");
                    window.location.href = "{{ url('admin-login') }}";
                    return; // Dừng lại và không xử lý thêm
                }

                return response.json(); // Nếu không phải lỗi 401, tiếp tục xử lý dữ liệu
            })
            .then(data => {
                if (data.success) {
                    alert("Thêm sản phẩm thành công");
                    window.location.href = "{{ url('/admin/all-product') }}";
                } else {
                    alert("Có lỗi xảy ra: " + JSON.stringify(data.errors));
                }
            })
            .catch(error => console.error("Lỗi khi thêm sản phẩm:", error));
    });
</script>
<script>
    CKEDITOR.replace('product_content', {
        filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        filebrowserUploadMethod: 'form'
    });
    CKEDITOR.replace('product_desc', {
        filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        filebrowserUploadMethod: 'form'
    });
    $(document).ready(function() {
        $('.product_price_in').simpleMoneyFormat();
        $('.product_price').simpleMoneyFormat();
    });
</script>
@endsection