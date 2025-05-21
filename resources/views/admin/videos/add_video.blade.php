@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{url('/admin') }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{url('/admin/all-videos')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách videos
                </a>
                Thêm Video
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="addVideoForm" class="form-validate" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tiêu đề videos</label>
                            <input type="text" name="video_title" onkeyup="ChangeToSlug();" class="form-control" id="slug" placeholder="Tiêu đề video">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Slug</label>
                            <input type="text" name="video_slug" class="form-control" id="convert_slug" placeholder="Slug">
                        </div>
                        <div class="form-group">
                            <label for="product_desc">Mô tả video</label>
                            <textarea rows="5" class="form-control" name="video_desc" id="video_desc" placeholder="Mô tả " required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_desc">Link</label>
                            <textarea rows="5" class="form-control" name="video_link" id="video_link" placeholder="Link Video" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_image">Hình ảnh</label>
                            <input type="file" name="video_thumb" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="product_status">Hiển thị</label>
                            <select name="video_status" class="form-control" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Thêm video</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector("#addVideoForm").addEventListener("submit", function(event) {
            event.preventDefault();
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            let formData = new FormData(this);
            const adminToken = localStorage.getItem('admin_token');
            if (!adminToken) {
                alert("Chưa đăng nhập, vui lòng đăng nhập!");
                window.location.href = "{{ url('admin-login') }}";
                return;
            }
            for (let [key, value] of formData.entries()) {
                console.log(key + ": " + value); // In ra mỗi key và value trong FormData
            }
            fetch("{{url('/api/videos')}}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "Authorization": "Bearer " + adminToken
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        alert("Chưa đăng nhập, vui lòng đăng nhập!");
                        window.location.href = "{{ url('admin-login') }}";
                        return; // Dừng lại và không xử lý thêm
                    }

                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Thêm video thành công!");
                        window.location.href = "{{ url('/admin/all-videos') }}";
                    } else {
                        alert("Có lỗi xảy ra: " + JSON.stringify(data.errors));
                    }
                })
                .catch(error => console.error("Lỗi khi thêm sản phẩm:", error));
        });
    });
</script>
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
    CKEDITOR.replace('video_desc', {
        filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        filebrowserUploadMethod: 'form'
    });
</script>
@endsection