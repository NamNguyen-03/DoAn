@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ URL::previous() }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                Cập nhật danh mục bài viết
            </header>
            <div class="panel-body">

                <div class="position-center">
                    <form role="form" class="form-validate" id="updatePostCateForm" method="post">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tên danh mục bài viết</label>
                            <input type="text" value="" name="cate_post_name" onkeyup="ChangeToSlug();" class="form-control" id="slug">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Slug</label>
                            <input type="text" value="" name="cate_post_slug" class="form-control" id="convert_slug">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Mô tả danh mục bài viết</label>
                            <textarea style="resize:none " rows="5" class="form-control" name="cate_post_desc" id="cate_post_desc"></textarea>
                        </div>

                        <button type="submit" name="update_cate_post" class="btn btn-info">Cập nhật</button>
                    </form>
                </div>

            </div>
        </section>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const postcateSlug = "{{ $cate_post_slug }}"; // Nhận brand_id từ Blade
        if (!postcateSlug) {
            alert("Không tìm thấy ID danh mục bài viết.");
            window.location.href = "/admin/all-post-cate";
            return;
        }

        // Lấy dữ liệu thương hiệu
        fetchPostCateData(postcateSlug);

        // Bắt sự kiện submit form
        document.getElementById("updatePostCateForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Ngăn chặn load lại trang
            updatePostCate(postcateSlug);
        });
    });

    function fetchPostCateData(postcateSlug) {
        fetch(`/api/postcates/${postcateSlug}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populatePostCateData(data.data);
                } else {
                    alert("Không tìm thấy thương hiệu.");
                    window.location.href = "/admin/all-post-cate";
                }
            })
            .catch(error => console.error("Lỗi khi lấy dữ liệu danh mục bài viết:", error));
    }

    function populatePostCateData(postcateData) {
        const postcateName = document.getElementsByName("cate_post_name")[0];
        const postcateslug = document.getElementsByName("cate_post_slug")[0];
        const postcateDesc = document.getElementsByName("cate_post_desc")[0];

        if (postcateName) postcateName.value = postcateData.cate_post_name;
        if (postcateslug) postcateslug.value = postcateData.cate_post_slug;
        if (postcateDesc) postcateDesc.value = postcateData.cate_post_desc || "";
    }

    function updatePostCate(postcateSlug) {
        const postcateName = document.getElementsByName("cate_post_name")[0].value;
        const postcateslug = document.getElementsByName("cate_post_slug")[0].value;
        const postcateDesc = document.getElementsByName("cate_post_desc")[0].value;
        const adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            return;
        }

        fetch(`/api/postcates/${postcateSlug}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${adminToken}`
                },
                body: JSON.stringify({
                    cate_post_name: postcateName,
                    cate_post_slug: postcateslug,
                    cate_post_desc: postcateDesc
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
                    alert("Cập nhật danh mục bài viết thành công!");
                    window.location.href = "/admin/all-post-cate"; // Chuyển hướng sau khi cập nhật thành công
                } else {
                    alert("Lỗi khi cập nhật danh mục bài viết: " + data.message);
                }
            })
            .catch(error => console.error("Lỗi khi cập nhật danh mục bài viết:", error));
    }
</script>
@endsection