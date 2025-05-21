@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{url('/admin/dashboard') }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{url('/admin/all-post')}}" class="btn btn-default" style="height: 40px; line-height: 30px; float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách bài viết
                </a>
                Thêm bài viết
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form role="form" class="form-validate" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tên bài viết</label>
                            <input type="text" name="post_title" onkeyup="ChangeToSlug();" class="form-control" id="slug" placeholder="Tên bài viết">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputPassword1">Danh mục bài viết</label>
                            <select name="cate_post_id" id="cate_post_id" class="form-control input-sm m-bot15">
                                <option value="0">Chọn</option>

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Slug</label>
                            <input type="text" name="post_slug" id="convert_slug" class="form-control" placeholder="Slug">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputPassword1">Tóm tắt bài viết (mô tả bài viết)</label>
                            <textarea style="resize:none " rows="3" class="form-control" name="post_desc" id="post_desc" placeholder="Tóm tắt bài viết (mô tả bài viết)"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputPassword1">Nội dung bài viết</label>
                            <textarea style="resize:none " rows="3" class="form-control" name="post_content" id="post_content" placeholder="Nội dung bài viết"></textarea>
                        </div>


                        <div class="form-group">
                            <label for="exampleInputEmail1">Hình ảnh bài viết</label>
                            <input type="file" name="post_image" class="form-control" id="post_image">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputPassword1">Hiển thị</label>
                            <select name="post_status" class="form-control input-sm m-bot15">
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>

                        <button type="submit" name="add_post_cate" class="btn btn-info">Thêm bài viết cho danh mục</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fetch danh mục bài viết
        fetch("{{ url('/api/postcates') }}")
            .then(response => response.json())
            .then(data => {
                console.log(data); // Log the entire response to debug

                if (data.success && Array.isArray(data.data)) {
                    let select = document.querySelector("select[name='cate_post_id']");
                    data.data.forEach(item => {
                        let option = document.createElement("option");
                        option.value = item.cate_post_id; // Ensure this matches your model
                        option.textContent = item.cate_post_name; // This should also match your model
                        select.appendChild(option);
                    });
                } else {
                    console.error("Dữ liệu danh mục bài viết không hợp lệ");
                }
            })
            .catch(error => console.error("Lỗi khi lấy danh mục bài viết:", error));
    });


    document.querySelector("form").addEventListener("submit", function(e) {
        e.preventDefault();

        let form = this;

        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        let formData = new FormData(form);
        let token = localStorage.getItem("admin_token");

        if (!token) {
            alert("Vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch("{{ url('/api/posts') }}", {
                method: "POST",
                headers: {
                    "Authorization": "Bearer " + token
                },
                body: formData
            })
            .then(response => {
                if (response.status === 401) {
                    alert("Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.");
                    window.location.href = "{{ url('admin-login') }}";
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert("Thêm bài viết thành công!");
                    window.location.href = "{{ url('/admin/all-post') }}";
                } else {
                    alert("Lỗi: " + JSON.stringify(data.errors));
                }
            })
            .catch(error => console.error("Lỗi khi thêm bài viết:", error));
    });
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