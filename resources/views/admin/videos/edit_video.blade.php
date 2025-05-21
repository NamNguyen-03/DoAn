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
                    <form id="updateVideoForm" class="form-validate" enctype="multipart/form-data">
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
                            <input type="file" name="video_thumb" class="form-control">
                            <div id="current_image">
                                <label for="current_image">Ảnh hiện tại:</label>
                                <img id="video_thumb_preview" src="" alt="Video Image Preview" style="max-width: 100px; max-height: 100px;" />
                            </div>
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
        const videoSlug = `{{$video_slug}}`;
        if (!videoSlug) {
            alert("Không tìm thấy video.");
            window.location.href = "/admin/all-videos"; // Redirect đến danh sách banner nếu không tìm thấy banner_id
            return;
        }
        fetchVideoData(videoSlug);
        document.getElementById("updateVideoForm").addEventListener("submit", function(event) {
            event.preventDefault();
            updateVideo(videoSlug);
        })
    });

    function fetchVideoData(videoSlug) {
        fetch(`/api/videos/${videoSlug}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const videoData = data.data;
                    renderVideoData(videoData);
                } else {
                    alert("Không tìm thấy videos");
                    window.location.href = "/admin/all-videos";
                }
            })
            .catch(error => {
                console.error("Lỗi khi lấy dữ liệu video:", error);
            });
    }

    function renderVideoData(videoData) {
        const form = document.forms['updateVideoForm'];
        form.elements['video_title'].value = videoData.video_title;
        form.elements['video_slug'].value = videoData.video_slug;
        form.elements['video_link'].value = videoData.video_link;
        if (CKEDITOR.instances['video_desc']) {
            CKEDITOR.instances['video_desc'].setData(videoData.video_desc || "");
        }
        form.elements['video_status'].value = videoData.video_status;
        if (videoData.video_thumb) {
            const previewImage = document.getElementById('video_thumb_preview');
            previewImage.src = `/uploads/video_thumbs/${videoData.video_thumb}`;
        }
    }

    function updateVideo(videoSlug) {
        const adminToken = localStorage.getItem("admin_token");
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng đến trang đăng nhập
            return;
        }
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        let formData = new FormData(document.getElementById("updateVideoForm"));
        formData.append("_method", "PATCH");
        // for (let [key, value] of formData.entries()) {
        //     console.log(key + ": " + value); // In ra mỗi key và value trong FormData
        // }
        const headers = new Headers();
        headers.append("Authorization", `Bearer ${adminToken}`);
        fetch(`/api/videos/${videoSlug}`, {
                method: "POST",
                headers: headers,
                body: formData
            })
            .then(response => handleUnauthorizedError(response)) // Kiểm tra lỗi 401 Unauthorized
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = "/admin/all-videos"; // Redirect đến danh sách banner
                } else {
                    alert("Cập nhật thất bại: " + data.message);
                }
            })
            .catch(error => {
                console.error("Lỗi cập nhật videos:", error);
                alert("Đã có lỗi xảy ra, vui lòng thử lại!");
            });
    }

    function handleUnauthorizedError(response) {
        if (response.status === 401) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}"; // Chuyển hướng đến trang đăng nhập
            throw new Error("Unauthorized access"); // Stop further processing
        }
        return response; // Proceed if not 401
    }
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