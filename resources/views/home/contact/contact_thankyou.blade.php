@extends('home.home_layout')

@section('content')
<div class="container py-5 text-center">
    <div class="bg-white rounded shadow p-5">
        <h1 class="text-success mb-4"><i class="fas fa-check-circle"></i> Cảm ơn bạn!</h1>
        <p class="lead">Chúng tôi đã nhận được yêu cầu liên hệ của bạn.</p>
        <p class="mb-4">Đội ngũ Fulitex sẽ phản hồi bạn trong thời gian sớm nhất.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">
            Quay lại trang chủ
        </a>
    </div>
</div>
<br>
@endsection