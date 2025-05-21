@extends('home.home_layout')
@section('content')

<!-- Breadcrumb -->
<nav class="bg-light p-3 mb-4 rounded shadow-sm">
    <div class="container" style="width:100%;">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tài khoản</li>
        </ol>
    </div>
</nav>

<section class="user-account-section py-3">
    <div class="container" style="width:100%">

        <div class="d-flex flex-wrap gap-2 mb-4 user-nav-tabs">
            <a href="{{ route('home.user.user_info') }}"
                class="nav-tab-item {{ request()->routeIs('home.user.user_info', 'home.user.edit_user_info') ? 'active' : '' }}">
                Thông tin tài khoản
            </a>

            <a href="{{ route('home.user.orders') }}"
                class="nav-tab-item {{ request()->routeIs('home.user.orders') ? 'active' : '' }}">
                Đơn hàng của tôi
            </a>
            <a href="{{ route('home.user.comments') }}"
                class="nav-tab-item {{ request()->routeIs('home.user.comments') ? 'active' : '' }}">
                Bình luận của tôi
            </a>
            <a href="{{ route('home.user.wishlist') }}"
                class="nav-tab-item {{ request()->routeIs('home.user.wishlist') ? 'active' : '' }}">
                Sản phẩm đã thích
            </a>
            <a href="{{ route('home.user.changePassword') }}"
                class="nav-tab-item {{ request()->routeIs('home.user.changePassword') ? 'active' : '' }}">
                Đổi mật khẩu
            </a>
        </div>


        <div class="card shadow-sm p-4" style="margin: bottom 20px;">
            <br>
            @yield('mini_content')

        </div>

    </div>
</section>
<style>
    .user-nav-tabs {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        /* Khoảng cách giữa các ô */
        margin-bottom: 1rem;
    }

    .nav-tab-item {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        border: 1px solid #ced4da;
        border-radius: 7px;
        /* Bo tròn full pill */
        background-color: #fff;
        color: #0d6efd;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .nav-tab-item:hover {
        background-color: #f1f1f1;
        color: #0b5ed7;
        border-color: #b6c1d2;
    }

    .nav-tab-item.active {
        background-color: #e9ecef;
        color: #000;
        border-color: #adb5bd;
        font-weight: 600;
    }
</style>
@endsection