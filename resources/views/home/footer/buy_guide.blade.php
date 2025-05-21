@extends('home.home_layout')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Hướng dẫn mua hàng</h1>
    <ol>
        <li>Truy cập website: <a href="{{ url('/') }}">{{ url('/') }}</a></li>
        <li>Chọn sản phẩm bạn muốn mua → Nhấn "Thêm vào giỏ hàng".</li>
        <li>Kiểm tra giỏ hàng và nhấn "Thanh toán".</li>
        <li>Điền thông tin giao hàng, chọn phương thức thanh toán.</li>
        <li>Xác nhận đơn hàng → Fulitex sẽ liên hệ xác nhận và giao hàng.</li>
    </ol>
    <p>Nếu cần hỗ trợ, vui lòng gọi hotline <strong>1800 1234</strong> hoặc gửi email đến <a href="mailto:support@fulitex.vn">support@fulitex.vn</a>.</p>
</div>
@endsection