@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{url('/admin/all-product') }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                Thêm thư viện ảnh
            </header>
            <form action="" method="POST" enctype="multipart/form-data" style="margin-top:10px;">
                @csrf
                <div class="row">
                    <div class="col-md-3" align="right"></div>
                    <div class="col-md-6">
                        <input type="file" class="form-control" id="file" name="file[]" accept="image/*" multiple>
                        <span id="error_gallery"></span>
                    </div>
                    <div class="col-md-3">
                        <input type="submit" name="upload" name="taianh" value="Tải ảnh" class="btn btn-success">
                    </div>

                </div>

            </form>
            <div class="panel-body">


                <input type="hidden" value="{{ $product_id }}" name="pro_id" class="pro_id">
                <form action="">

                    <div id="gallery_load">
                    </div>
                </form>
            </div>
        </section>

    </div>
</div>
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();

        const files = document.getElementById('file').files;
        const formData = new FormData();
        const productId = document.querySelector('.pro_id').value;
        let adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            ập
            return;
        }
        for (let i = 0; i < files.length; i++) {
            formData.append('gallery_images[]', files[i]);
        }

        fetch(`/api/gallery/${productId}/upload-multiple`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${adminToken}`,
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                alert(data.message || 'Tải ảnh thành công!');
                galleryLoad()
            })
            .catch(err => {
                console.error('Lỗi:', err);
                alert('Tải ảnh thất bại!');
            });
    });
</script>

<script>
    const galleryLoad = () => {
        const productId = document.querySelector('.pro_id').value;

        fetch(`/api/galleries/${productId}`)
            .then(res => res.json())
            .then(data => {
                const galleryContainer = document.querySelector('#gallery_load');
                galleryContainer.innerHTML = ''; // clear cũ

                if (data.data.length === 0) {
                    galleryContainer.innerHTML = `<p>Chưa có ảnh nào.</p>`;
                    return;
                }

                let table = `<table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên ảnh</th>
                            <th>Ảnh</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>`;

                data.data.forEach((item, index) => {
                    table += `<tr>
                        <td>${index + 1}</td>
                        <td>${item.gallery_name}</td>
                        <td><img src="/uploads/gallery/${item.gallery_image}" width="100" /></td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deleteGallery(${item.gallery_id})">Xóa</button>
                        </td>
                    </tr>`;
                });

                table += `</tbody></table>`;
                galleryContainer.innerHTML = table;
            })
            .catch(err => {
                console.error(err);
                document.querySelector('#gallery_load').innerHTML = '<p>Lỗi tải thư viện ảnh.</p>';
            });
    }

    const deleteGallery = (id) => {
        if (!confirm("Bạn có chắc muốn xóa ảnh này?")) return;

        let adminToken = localStorage.getItem("admin_token");
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            ập
            return;
        }
        fetch(`/api/gallery/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${adminToken}`
                }
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Đã xóa');
                galleryLoad(); // reload lại bảng
            })
            .catch(err => {
                alert("Xóa thất bại");
                console.error(err);
            });
    }

    // Gọi khi trang load
    window.addEventListener('DOMContentLoaded', galleryLoad);
</script>

@endsection