@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ URL::previous() }}">
                    <img src="{{ asset('backend/images/back.png') }}" alt="Back" style="float: left; margin-right: 10px; margin-top: 11px; width: 40px; height: 40px;">
                </a>
                Sửa bài viết
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="updatePostForm" class="form-validate" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="post_title">Tên bài viết</label>
                            <input type="text" name="post_title" onkeyup="ChangeToSlug();" class="form-control" id="slug" placeholder="Tên bài viết">

                        </div>
                        <div class="form-group">
                            <label for="cate_post_id">Danh mục bài viết</label>
                            <select name="cate_post_id" id="cate_post_id" class="form-control" required>
                                <option value="">Chọn danh mục</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="post_slug">Slug</label>
                            <input type="text" name="post_slug" id="convert_slug" class="form-control" placeholder="Slug">
                        </div>
                        <div class="form-group">
                            <label for="post_desc">Tóm tắt bài viết</label>
                            <textarea rows="3" name="post_desc" id="post_desc" class="form-control" placeholder="Mô tả bài viết" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_content">Nội dung bài viết</label>
                            <textarea rows="5" name="post_content" id="post_content" class="form-control" placeholder="Nội dung bài viết" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_image">Hình ảnh bài viết</label>
                            <input type="file" name="post_image" id="post_image" class="form-control">
                            <div id="current_image">
                                <label for="current_image">Ảnh hiện tại:</label>
                                <img id="post_image_preview" src="" alt="Post Image Preview" style="max-width: 100px; max-height: 100px;" />
                            </div>
                        </div>
                        <div class="form-group">
                            <select name="post_status" id="post_status" class="form-control" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Cập nhật bài viết</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let postSlug = "{{ $post_slug }}"; // Nhận post_id từ Blade

        if (!postSlug) {
            alert("Không tìm thấy ID bài viết.");
            window.location.href = "/admin/all-post"; // Redirect đến danh sách bài viết nếu không tìm thấy post_id
            return;
        }

        // Lấy dữ liệu bài viết
        fetchPostData(postSlug);

        // Tải danh mục bài viết
        loadCategories();
    });

    function fetchPostData(postSlug) {
        fetch(`/api/posts/${postSlug}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const postData = data.data;

                    document.getElementById('slug').value = postData.post_title;
                    document.getElementById('convert_slug').value = postData.post_slug;
                    // document.getElementById('post_desc').value = postData.post_desc || "";
                    if (CKEDITOR.instances['post_desc']) {
                        CKEDITOR.instances['post_desc'].setData(postData.post_desc || "");
                    }
                    // document.getElementById('post_content').value = postData.post_content || "";
                    if (CKEDITOR.instances['post_content']) {
                        CKEDITOR.instances['post_content'].setData(postData.post_content || "");
                    }
                    document.getElementById('post_status').value = postData.post_status == 1 ? "1" : "0";

                    const selectedCategoryId = postData.cate_post_id;
                    loadCategories(selectedCategoryId);

                    const postImagePreview = document.getElementById('post_image_preview');
                    if (postData.post_image) {
                        postImagePreview.src = `/uploads/post/${postData.post_image}`;
                    }
                } else {
                    alert("Không tìm thấy bài viết.");
                    window.location.href = "/admin/all-post";
                }
            })
            .catch(error => {
                console.error("Lỗi khi lấy dữ liệu bài viết:", error);
            });
    }


    function loadCategories(selectedCategoryId = null) {
        // Tải danh mục bài viết
        fetch("{{ url('/api/postcates') }}")
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    let cateSelect = document.getElementById("cate_post_id");

                    // Xóa tất cả các option cũ trong select trước khi thêm mới
                    cateSelect.innerHTML = '';

                    // Thêm option mặc định
                    let defaultOption = document.createElement("option");
                    defaultOption.value = "";
                    defaultOption.textContent = "Chọn danh mục";
                    cateSelect.appendChild(defaultOption);

                    // Thêm các danh mục từ API vào select
                    data.data.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category.cate_post_id;
                        option.textContent = category.cate_post_name;

                        // Nếu cate_post_id của bài viết trùng với category này, chọn nó
                        if (category.cate_post_id === selectedCategoryId) {
                            option.selected = true;
                        }

                        cateSelect.appendChild(option);
                    });
                } else {
                    console.error("Dữ liệu không hợp lệ hoặc không có danh mục.");
                }
            })
            .catch(error => console.error("Lỗi khi lấy danh mục bài viết:", error));
    }


    document.getElementById("updatePostForm").addEventListener("submit", function(event) {
        event.preventDefault();
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        let postSlug = "{{ $post_slug }}";
        let formData = new FormData(this); // Use FormData to include the file input
        formData.append("_method", "PATCH"); // Laravel needs this to identify the method

        // Lấy admin_token từ localStorage
        let adminToken = localStorage.getItem("admin_token");

        // Nếu không có token, chuyển hướng về trang đăng nhập
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng đến trang đăng nhập
            return;
        }

        // Cập nhật bài viết
        updatePost(postSlug, formData, adminToken);
    });

    function updatePost(postSlug, formData, adminToken) {
        fetch(`/api/posts/${postSlug}`, {
                method: "POST", // Dùng PATCH để cập nhật dữ liệu
                body: formData, // Send the FormData directly
                headers: {
                    'Authorization': `Bearer ${adminToken}` // Thêm token vào header
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = "/admin/all-post"; // Redirect to the post list
                } else {
                    alert("Cập nhật thất bại: " + data.message);
                }
            })
            .catch(error => {
                console.error("Lỗi cập nhật bài viết:", error);
                alert("Đã có lỗi xảy ra, vui lòng thử lại!");
            });
    }
</script>
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
    CKEDITOR.replace('post_content', {
        filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        filebrowserUploadMethod: 'form'
    });
    CKEDITOR.replace('post_desc', {
        filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        filebrowserUploadMethod: 'form'
    });
</script>
@endsection