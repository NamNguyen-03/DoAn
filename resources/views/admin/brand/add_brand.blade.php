@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{url('/admin/dashboard') }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{url('/admin/all-brand')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách thương hiệu
                </a>
                Thêm thương hiệu sản phẩm
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="addBrandForm" class="form-validate">
                        <div class="form-group">
                            <label for="brand_name">Tên thương hiệu</label>
                            <input type="text" name="brand_name" onkeyup="ChangeToSlug();" class="form-control" id="slug" placeholder="Tên thương hiệu" required>
                        </div>
                        <div class="form-group">
                            <label for="brand_slug">Slug</label>
                            <input type="text" name="brand_slug" id="convert_slug" class="form-control" placeholder="Slug" required>
                        </div>
                        <div class="form-group">
                            <label for="brand_desc">Mô tả</label>
                            <textarea style="resize: none" rows="5" class="form-control" name="brand_desc" id="brand_desc" placeholder="Mô tả thương hiệu" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="brand_status">Hiển thị</label>
                            <select name="brand_status" class="form-control input-sm m-bot15" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Thêm thương hiệu</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.querySelector("#addBrandForm").addEventListener("submit", function(event) {
        event.preventDefault();
        let formData = new FormData(this);


        let adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch("{{ url('/api/brands') }}", {
                method: "POST",
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    "Content-Type": "application/json",
                    // "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Authorization": "Bearer " + adminToken
                }
            })
            .then(response => {
                if (response.status === 401) {

                    alert("Chưa đăng nhập, vui lòng đăng nhập!");
                    window.location.href = "{{ url('admin-login') }}";
                    return;
                }

                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    alert("Thêm thương hiệu thành công");
                    window.location.href = "{{ url('/admin/all-brand') }}";
                } else {
                    alert("Có lỗi xảy ra");
                }
            })
            .catch(error => console.error("Lỗi khi thêm coupon:", error));
    });
</script>

@endsection