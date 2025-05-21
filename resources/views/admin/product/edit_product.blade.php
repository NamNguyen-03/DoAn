@extends('admin.admin_layout')
@section('admin_content')

<!-- Include TagsInput CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" />

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ url('/admin/all-product') }}">
                    <img src="{{ asset('backend/images/back.png') }}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{ url('/admin/all-product') }}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách sản phẩm
                </a>
                Sửa sản phẩm
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="updateProductForm" class="form-validate" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="product_name">Tên sản phẩm</label>
                            <input type="text" name="product_name" onkeyup="ChangeToSlug();" class="form-control" id="slug" placeholder="Tên sản phẩm">
                        </div>
                        <div class="form-group">
                            <label for="product_price">Giá sản phẩm</label>
                            <input type="text" name="product_price" id="product_price" class="form-control" placeholder="Giá sản phẩm" required>
                        </div>
                        <div class="form-group">
                            <label for="product_price">Giá Nhập sản phẩm</label>
                            <input type="text" name="product_price_in" id="product_price_in" class="form-control" placeholder="Giá Nhập sản phẩm" required>
                        </div>
                        <div class="form-group">
                            <label for="product_quantity">Số lượng sản phẩm</label>
                            <input type="number" name="product_quantity" id="product_quantity" class="form-control" placeholder="Số lượng sản phẩm" required>
                        </div>
                        <div class="form-group">
                            <label for="product_slug">Slug</label>
                            <input type="text" name="product_slug" class="form-control" id="convert_slug" placeholder="Slug">
                        </div>
                        <div class="form-group">
                            <label for="product_image">Hình ảnh</label>
                            <input type="file" name="product_image" id="product_image" class="form-control">
                            <div id="current_image">
                                <label for="current_image">Ảnh hiện tại:</label>
                                <img id="product_image_preview" src="" alt="Product Image Preview" style="max-width: 100px; max-height: 100px;" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="product_desc">Mô tả</label>
                            <textarea rows="5" name="product_desc" id="product_desc" class="form-control" id="product_desc" placeholder="Mô tả sản phẩm" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_content">Nội dung</label>
                            <textarea rows="5" name="product_content" id="product_content" class="form-control" id="product_content" placeholder="Nội dung sản phẩm" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_tags">Tags</label>
                            <input type="text" name="product_tags" id="product_tags" class="form-control" data-role="tagsinput" placeholder="Tags">
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
                            <select name="product_status" id="product_status" class="form-control" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Cập nhật sản phẩm</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Include jQuery + TagsInput JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Khởi tạo tagsinput 1 lần duy nhất
        $('#product_tags').tagsinput();

        const productSlug = "{{ $product_slug }}";
        if (!productSlug) {
            alert("Không tìm thấy ID sản phẩm.");
            window.location.href = "/admin/all-product";
            return;
        }

        fetchProductData(productSlug);
    });

    function fetchProductData(productSlug) {
        fetch(`/api/products/${productSlug}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const form = document.forms['updateProductForm'];
                    const product = data.data;

                    form.elements['product_name'].value = product.product_name;
                    form.elements['product_price'].value = product.product_price;
                    form.elements['product_price_in'].value = product.product_price_in;
                    form.elements['product_quantity'].value = product.product_quantity;
                    form.elements['product_slug'].value = product.product_slug;
                    // form.elements['product_desc'].value = product.product_desc || "";
                    // form.elements['product_content'].value = product.product_content || "";
                    if (CKEDITOR.instances['product_desc']) {
                        CKEDITOR.instances['product_desc'].setData(product.product_desc || "");
                    }
                    // document.getElementById('post_content').value = postData.post_content || "";
                    if (CKEDITOR.instances['product_content']) {
                        CKEDITOR.instances['product_content'].setData(product.product_content || "");
                    }
                    form.elements['product_status'].value = product.product_status;

                    // Tags (gán giá trị trực tiếp, không gọi lại .tagsinput())
                    const tagsInput = $('#product_tags');
                    tagsInput.tagsinput('removeAll');
                    if (product.product_tags) {
                        product.product_tags.split(',').forEach(tag => {
                            tagsInput.tagsinput('add', tag.trim());
                        });
                    }

                    // Hình ảnh hiện tại
                    if (product.product_image) {
                        const previewImage = document.getElementById('product_image_preview');
                        previewImage.src = `/uploads/product/${product.product_image}`;
                    }

                    // Load danh mục và thương hiệu
                    loadCategoriesAndBrands(product.category_id, product.brand_id);
                } else {
                    alert("Không tìm thấy sản phẩm.");
                    window.location.href = "/admin/all-product";
                }
            })
            .catch(err => {
                console.error("Lỗi khi lấy dữ liệu sản phẩm:", err);
                alert("Lỗi khi lấy dữ liệu sản phẩm.");
            });
    }

    function loadCategoriesAndBrands(selectedCategoryId = null, selectedBrandId = null) {
        fetch("{{ url('/api/categories') }}")
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const select = document.forms['updateProductForm'].elements['category_id'];
                    select.innerHTML = '<option value="">Chọn</option>';

                    // Lọc danh mục có parent_id khác 0 (danh mục con)
                    const childCategories = data.data.filter(cat => cat.category_parent !== 0);

                    childCategories.forEach(cat => {
                        const option = new Option(cat.category_name, cat.category_id);
                        if (selectedCategoryId == cat.category_id) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });
                }
            });

        fetch("{{ url('/api/brands') }}")
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const select = document.forms['updateProductForm'].elements['brand_id'];
                    select.innerHTML = '<option value="">Chọn</option>';
                    data.data.forEach(brand => {
                        const option = new Option(brand.brand_name, brand.brand_id);
                        if (selectedBrandId == brand.brand_id) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });
                }
            });
    }

    document.forms['updateProductForm'].addEventListener('submit', function(e) {
        e.preventDefault();
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        const productSlug = "{{ $product_slug }}";
        const form = this;
        const formData = new FormData(form);

        formData.append("product_tags", $('#product_tags').val());
        formData.append("_method", "PATCH");

        const token = localStorage.getItem("admin_token");
        if (!token) {
            alert("Chưa đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch(`/api/products/${productSlug}`, {
                method: "POST",
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = "/admin/all-product";
                } else {
                    alert("Cập nhật thất bại: " + data.message);
                }
            })
            .catch(err => {
                console.error("Lỗi cập nhật sản phẩm:", err);
                alert("Đã có lỗi xảy ra!");
            });
    });
</script>
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
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
</script>
@endsection