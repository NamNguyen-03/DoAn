@extends('home.home_layout')
@section('content')

<style>
    .about-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        line-height: 1.6;
    }

    .about-container h1 {
        text-align: center;
        font-size: 32px;
        margin-bottom: 20px;
        color: #333;
    }

    .about-container h2 {
        font-size: 24px;
        margin-top: 30px;
        color: #444;
    }

    .about-container p {
        font-size: 16px;
        color: #555;
        text-align: justify;
    }

    .about-image {
        width: 100%;
        height: auto;
        margin: 20px 0;
        border-radius: 8px;
    }
</style>

<div class="about-container">
    <h1>Giới thiệu về Fulitex</h1>

    <p>Công ty Fulitex là đơn vị chuyên cung cấp các sản phẩm <strong>máy ảnh</strong>, <strong>thiết bị điện tử</strong> và các phụ kiện công nghệ hàng đầu tại Việt Nam. Với nhiều năm kinh nghiệm trong ngành, chúng tôi cam kết mang đến cho khách hàng những sản phẩm chất lượng cao và dịch vụ chuyên nghiệp nhất.</p>

    <img src="{{ asset('frontend/images/about.png') }}" alt="Về chúng tôi" class="about-image">

    <h2>Sứ mệnh của chúng tôi</h2>
    <p>Sứ mệnh của Fulitex là giúp khách hàng tiếp cận dễ dàng với các công nghệ hiện đại, phục vụ cho công việc và cuộc sống hàng ngày. Chúng tôi luôn đổi mới, cập nhật sản phẩm, và mở rộng dịch vụ nhằm nâng cao trải nghiệm mua sắm của khách hàng.</p>

    <h2>Giá trị cốt lõi</h2>
    <p>
        - Chất lượng sản phẩm là ưu tiên hàng đầu.<br>
        - Uy tín và sự hài lòng của khách hàng là kim chỉ nam cho mọi hoạt động.<br>
        - Hỗ trợ khách hàng nhanh chóng, tận tâm.<br>
        - Không ngừng đổi mới để phát triển bền vững.
    </p>

    <h2>Liên hệ</h2>
    <p>
        Địa chỉ: ABC Đường Công Nghệ, Hà Nội<br>
        Hotline: 0909 999 999<br>
        Email: contact@fulitex.vn
    </p>
</div>

@endsection