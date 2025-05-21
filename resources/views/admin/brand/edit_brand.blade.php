@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ URL::previous() }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px; width: 40px; height: 40px;">
                </a>
                Cập nhật thương hiệu
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="updateBrandForm" role="form" class="form-validate" action="" method="post">
                        @csrf
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
                            <textarea rows="5" class="form-control" name="brand_desc" id="brand_desc" style="resize:none"></textarea>
                        </div>

                        <button type="submit" class="btn btn-info">Cập nhật</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const brandSlug = "{{ $brand_slug }}"; // Nhận brand_id từ Blade

        if (!brandSlug) {
            alert("Không tìm thấy ID thương hiệu.");
            window.location.href = "/admin/all-brand";
            return;
        }

        // Lấy dữ liệu thương hiệu
        fetchBrandData(brandSlug);

        // Bắt sự kiện submit form
        document.getElementById("updateBrandForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Ngăn chặn load lại trang
            updateBrand(brandSlug);
        });
    });

    function fetchBrandData(brandSlug) {
        fetch(`/api/brands/${brandSlug}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateBrandData(data.data);
                } else {
                    alert("Không tìm thấy thương hiệu.");
                    window.location.href = "/admin/all-brand";
                }
            })
            .catch(error => console.error("Lỗi khi lấy dữ liệu thương hiệu:", error));
    }

    function populateBrandData(brandData) {
        const brandName = document.getElementsByName("brand_name")[0];
        const brandslug = document.getElementsByName("brand_slug")[0];
        const brandDesc = document.getElementsByName("brand_desc")[0];

        if (brandName) brandName.value = brandData.brand_name;
        if (brandslug) brandslug.value = brandData.brand_slug;
        if (brandDesc) brandDesc.value = brandData.brand_desc || "";
    }

    function updateBrand(brandSlug) {
        const brandName = document.getElementsByName("brand_name")[0].value;
        const brandslug = document.getElementsByName("brand_slug")[0].value;
        const brandDesc = document.getElementsByName("brand_desc")[0].value;
        const adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            return;
        }

        fetch(`/api/brands/${brandSlug}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${adminToken}`
                },
                body: JSON.stringify({
                    brand_name: brandName,
                    brand_slug: brandslug,
                    brand_desc: brandDesc
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
                    alert("Cập nhật thương hiệu thành công!");
                    window.location.href = "/admin/all-brand"; // Chuyển hướng sau khi cập nhật thành công
                } else {
                    alert("Lỗi khi cập nhật thương hiệu: " + data.message);
                }
            })
            .catch(error => console.error("Lỗi khi cập nhật thương hiệu:", error));
    }
</script>

@endsection