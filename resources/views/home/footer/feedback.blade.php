@extends('home.home_layout')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Góp ý</h1>
    <p>Fulitex luôn lắng nghe ý kiến từ khách hàng để cải thiện chất lượng sản phẩm và dịch vụ.</p>

    <p>Bạn có thể gửi góp ý qua các kênh sau:</p>
    <ul>
        <li>📧 Email: <a href="mailto:feedback@fulitex.vn">feedback@fulitex.vn</a></li>
        <li>📞 Hotline: 1800 1234</li>
        <li>💬 Hoặc điền biểu mẫu bên dưới:</li>
    </ul>

    <form method="POST" action="#">
        @csrf
        <div class="mb-3">
            <label class="form-label">Họ tên</label>
            <input type="text" class="form-control" placeholder="Nhập họ tên">
        </div>
        <div class="mb-3">
            <label class="form-label">Email liên hệ</label>
            <input type="email" class="form-control" placeholder="example@email.com">
        </div>
        <div class="mb-3">
            <label class="form-label">Nội dung góp ý</label>
            <textarea class="form-control" rows="5" placeholder="Viết góp ý của bạn tại đây..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi góp ý</button>
    </form>
</div>
@endsection