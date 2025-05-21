@extends('admin.admin_layout')
@section('admin_content')
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{url('/admin/dashboard') }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{url('/admin/all-promotion-content')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách email content
                </a>
                Thêm email content
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="addPromotionContentForm" class="form-validate" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="product_desc">Phong bì</label>
                            <textarea rows="5" class="form-control" name="envelope" id="envelope" placeholder="Phong bì mail" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_desc">Tiêu đề</label>
                            <textarea rows="5" class="form-control" name="subject" id="promotion_subject" placeholder="Tiêu đề" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_desc">Nội dung</label>
                            <textarea rows="5" class="form-control" name="content" id="promotion_content" placeholder="Nội dung" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="promotion_content_status">Hiển thị</label>
                            <select name="promotion_content_status" class="form-control" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Thêm nội dung khuyến mãi</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
<script>
    const adminToken = localStorage.getItem('admin_token');

    document.addEventListener("DOMContentLoaded", function() {

    });
    document.getElementById('addPromotionContentForm').addEventListener('submit', function() {
        event.preventDefault();
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        if (!adminToken) {
            alert('Vui lòng đăng nhập trước khi thực hiện');
            window.location.href = '/admin-login';
            return;
        }
        let formData = new FormData(this);
        console.log("Dữ liệu gửi đi:");
        for (let pair of formData.entries()) {
            console.log(`${pair[0]}:`, pair[1]);
        }
        fetch(`/api/promotions-content`, {
                method: "POST",
                headers: {
                    // "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    "Authorization": "Bearer " + adminToken
                },
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Đã thêm content email mới");
                    window.location.href = "{{ url('/admin/all-promotion-content') }}";
                }
            })
            .catch(error => console.error("Lỗi khi thêm content email:", error));
    });
</script>
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
    CKEDITOR.replace('promotion_content', {
        filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        filebrowserUploadMethod: 'form'
    });
    CKEDITOR.replace('promotion_subject', {
        filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        filebrowserUploadMethod: 'form'
    });
</script>
@endsection