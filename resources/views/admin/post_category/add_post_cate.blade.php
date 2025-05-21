@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{url('/admin/dashboard') }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{url('/all-post-cate')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách danh mục bài viết
                </a>
                Thêm danh mục bài viết
            </header>
            <div class="panel-body">


                <div class="position-center">
                    <form class="form-validate" id="addPostCateForm" role="form" method="post">

                        <div class="form-group">
                            <label for="exampleInputEmail1">Tên danh mục bài viết</label>
                            <input type="text" name="cate_post_name" onkeyup="ChangeToSlug();" class="form-control" id="slug" placeholder="Tên danh mục">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Slug</label>
                            <input type="text" name="cate_post_slug" id="convert_slug" class="form-control" placeholder="Slug">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Mô tả danh mục bài viết</label>
                            <textarea style="resize:none " rows="5" class="form-control" name="cate_post_desc" id="cate_post_desc" placeholder="Mô tả danh mục"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputPassword1">Hiển thị</label>
                            <select name="cate_post_status" class="form-control input-sm m-bot15">
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>


                        <button type="submit" name="add_cate_post" class="btn btn-info">Thêm danh mục bài viết</button>
                    </form>
                </div>

            </div>
        </section>

    </div>
</div>
<script>
    document.querySelector("#addPostCateForm").addEventListener("submit", function(event) {
        event.preventDefault();
        let formData = new FormData(this);


        let adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch("{{ url('/api/postcates') }}", {
                method: "POST",
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    "Content-Type": "application/json",
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
                    alert("Thêm danh mục bài viết thành công");
                    window.location.href = "{{ url('/admin/all-post-cate') }}";
                } else {
                    alert("Có lỗi xảy ra");
                }
            })
            .catch(error => console.error("Lỗi khi thêm coupon:", error));
    });
</script>
@endsection