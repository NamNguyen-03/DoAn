@extends('home.home_layout')

@section('content')
<link rel="stylesheet" href="{{ asset('css/policy.css') }}">
<script src="{{ asset('js/policy.js') }}" defer></script>

<div class="policy-container">
    <h1 class="policy-title">Chính sách & Quy định chung</h1>

    <div class="policy-section">
        <h2>1. CHẤP THUẬN CÁC ĐIỀU KIỆN SỬ DỤNG</h2>
        <ul>
            <li>Khi sử dụng Website của Fulitex, Quý khách đã mặc nhiên chấp thuận các điều khoản và điều kiện sử dụng được quy định dưới đây.</li>
            <li>Quý khách nên thường xuyên kiểm tra lại để cập nhật các thay đổi mới nhất.</li>
            <li>Việc tiếp tục sử dụng Website đồng nghĩa với việc Quý khách đã chấp thuận các thay đổi.</li>
        </ul>
    </div>

    <div class="policy-section">
        <h2>2. TÍNH CHẤT CỦA THÔNG TIN HIỂN THỊ</h2>
        <ul>
            <li>Các nội dung hiển thị nhằm cung cấp thông tin về Fulitex và sản phẩm, dịch vụ liên quan.</li>
            <li>Các thông tin khác đều được ghi rõ nguồn cung cấp.</li>
        </ul>
    </div>

    <div class="policy-section">
        <h2>3. LIÊN KẾT ĐẾN WEBSITE KHÁC</h2>
        <ul>
            <li>Fulitex không chịu trách nhiệm với nội dung từ các trang liên kết.</li>
        </ul>
    </div>

    <div class="policy-section">
        <h2>4. LIÊN KẾT TỪ WEBSITE KHÁC</h2>
        <ul>
            <li>Không được nhúng hoặc chỉnh sửa giao diện website nếu không có sự đồng ý từ Fulitex.</li>
        </ul>
    </div>

    <div class="policy-section">
        <h2>5. MIỄN TRỪ TRÁCH NHIỆM</h2>
        <ul>
            <li>Fulitex và các bên liên quan không chịu trách nhiệm về bất kỳ thiệt hại nào do việc sử dụng website.</li>
            <li>Website có thể không vận hành liên tục hoặc không lỗi.</li>
        </ul>
    </div>

    <div class="policy-section">
        <h2>6. QUYỀN SỞ HỮU TRÍ TUỆ</h2>
        <ul>
            <li>Toàn bộ nội dung và thiết kế thuộc quyền sở hữu của Fulitex.</li>
            <li>Không sao chép hoặc sử dụng lại nếu không có sự đồng ý.</li>
        </ul>
    </div>

    <div class="policy-section">
        <h2>7. ĐIỀU CHỈNH VÀ SỬA ĐỔI</h2>
        <p>Fulitex có quyền thay đổi nội dung website mà không cần báo trước.</p>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sections = document.querySelectorAll('.policy-section');
        sections.forEach(section => {
            section.addEventListener('mouseenter', () => {
                section.style.backgroundColor = '#f8f9fa';
            });
            section.addEventListener('mouseleave', () => {
                section.style.backgroundColor = 'transparent';
            });
        });
    });
</script>
<style>
    .policy-container {
        max-width: 960px;
        margin: 60px auto;
        padding: 30px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        font-family: 'Segoe UI', sans-serif;
        line-height: 1.7;
        color: #333;
    }

    .policy-title {
        text-align: center;
        font-size: 32px;
        margin-bottom: 30px;
        color: #0d6efd;
        position: relative;
    }

    .policy-title::after {
        content: '';
        width: 60px;
        height: 4px;
        background: #0d6efd;
        display: block;
        margin: 10px auto 0;
        border-radius: 2px;
    }

    .policy-section {
        margin-bottom: 40px;
        transition: all 0.3s ease;
    }

    .policy-section h2 {
        font-size: 22px;
        color: #0d6efd;
        margin-bottom: 15px;
        border-left: 4px solid #0d6efd;
        padding-left: 12px;
    }

    .policy-section ul {
        padding-left: 20px;
    }

    .policy-section ul li {
        margin-bottom: 10px;
        list-style: disc;
    }
</style>
@endsection