<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- SEO -->
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="INDEX,FOLLOW" />
    <link rel="canonical" href="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{asset('frontend/images/techstormlogo.png')}}" />
    <!-- SEO -->
    <title>Tech Storm</title>
    <link href="{{asset('frontend/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/prettyPhoto.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/price-range.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/main.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/responsive.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/sweetalert.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/lightgallery.min.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/lightslider.css')}}" rel="stylesheet">
    <link href="{{asset('frontend/css/prettify.css')}}" rel="stylesheet">
    <!-- <link href="{{asset('frontend/css/vlite.css')}}" rel="stylesheet"> -->
    <link href="{{asset('frontend/css/flickity.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> -->


    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <link rel="shortcut icon" href="{{('frontend/images/favicon.ico')}}">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="images/ico/apple-touch-icon-57-precomposed.png">
</head><!--/head-->

<body>

    <header id="header"><!--header-->
        <!--header_top-->
        <div class="header_top_custom">
            <div class="container d-flex justify-content-between align-items-center py-2">
                <div class="brand-info d-flex align-items-center gap-2">
                    <span class="slogan text-white">
                        Chuỗi sửa chữa chuyên sâu
                        <i class="fa fa-mobile-screen-button mx-1"></i> Điện thoại
                        <i class="fa fa-laptop mx-1"></i> Laptop
                        <i class="fa fa-desktop mx-1"></i> PC -
                        Thành viên của <img src="{{asset('frontend/images/techstormlogo.png')}}" alt="TechStorm" style="height: 18px;">
                    </span>
                </div>
            </div>
        </div>

        <style>
            .header_top_custom {
                background-color: rgb(159, 204, 218);
                /* màu nền đỏ */
            }

            .header_top_custom .slogan {
                font-size: 14px;
                font-weight: 500;
            }

            .header_top_custom img {
                vertical-align: middle;
            }

            .header_top_custom i {
                color: white;
            }
        </style>

        <!-- /header_top -->

        <div class="header-middle"><!--header-middle-->
            <div class="container">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="logo pull-left">
                            <a href="{{URL::to('/')}}"><img src="{{asset('frontend/images/techstormlogo.png')}}" style="width:60%" alt="" /></a>
                        </div>
                        <!-- <div class="btn-group pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle usa" data-toggle="dropdown">
                                    USA
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Canada</a></li>
                                    <li><a href="#">UK</a></li>
                                </ul>
                            </div>

                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle usa" data-toggle="dropdown">
                                    DOLLAR
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Canadian Dollar</a></li>
                                    <li><a href="#">Pound</a></li>
                                </ul>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-sm-8">
                        <div class="shop-menu pull-right">
                            <!-- Menu khi ĐÃ đăng nhập -->
                            <ul id="logged-in-menu" style="display: none;">
                                <li><a href="{{URL::to('/account/info')}}"><i class="fa fa-user"></i><span id="user_name">...</span></a></li>
                                <li><a href="{{URL::to('/check-out')}}"><i class="fa fa-crosshairs"></i> Thanh toán</a></li>
                                <li><a href="{{URL::to('/cart')}}"><i class="fa fa-shopping-cart"></i> Giỏ hàng</a></li>
                                <li><a href="{{URL::to('/wishlist')}}"><i class="fa fa-heart"></i> Yêu thích</a></li>
                                <li><a href="{{URL::to('/orders')}}"><i class="fa fa-receipt"></i> Đơn hàng</a></li>
                                <li><a href="#" id="logout-button"><i class="fa fa-lock"></i> Đăng xuất</a></li>
                            </ul>

                            <!-- Menu khi CHƯA đăng nhập -->
                            <ul id="logged-out-menu" style="display: none;">
                                <li><a href="{{URL::to('/check-out')}}"><i class="fa fa-crosshairs"></i> Thanh toán</a></li>
                                <li><a href="{{URL::to('/cart')}}"><i class="fa fa-shopping-cart"></i> Giỏ hàng</a></li>
                                <li><a href="{{URL::to('/orders')}}"><i class="fa fa-receipt"></i> Đơn hàng</a></li>
                                <li><a href="{{URL::to('/login')}}"><i class="fa fa-lock"></i> Đăng nhập</a></li>
                            </ul>


                        </div>
                    </div>
                </div>
            </div>
        </div><!--/header-middle-->
        <a href="javascript:void(0);" id="cart-bubble">
            <span id="cart-count">0</span>
            <i class="fa-solid fa-cart-shopping"></i>
        </a>

        <!-- Popup modal -->
        <div id="cart-modal" style="display:none;">
            <div id="cart-modal-content">
                <h3>Giỏ hàng</h3>

                <div id="cart-table-wrapper">
                    <table id="cart-table">
                        <thead>
                            <tr>
                                <th>Ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            <!-- Cart items sẽ được thêm vào đây -->
                        </tbody>
                    </table>
                </div>

                <div id="cart-modal-footer">
                    <button id="close-modal">Đóng</button>
                    <a href="/cart" id="go-to-cart">Đi đến giỏ hàng</a>
                </div>
            </div>
        </div>


        <div class="header-bottom" style="height:100px"><!--header-bottom-->
            <div class="container">
                <div class="row">
                    <div class="col-sm-7">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div class="mainmenu pull-left">
                            <ul class="nav navbar-nav collapse navbar-collapse">
                                <li><a href="{{ URL::to('/') }}" class="active">Trang Chủ</a></li>

                                <li class="dropdown">
                                    <a href="#">Sản phẩm<i class="fa fa-angle-down"></i></a>
                                    <ul role="menu" class="sub-menu">
                                        <li><a href="{{ url('/products') }}">Tất cả sản phẩm</a></li>
                                        <li><a href="{{ URL::to('/check-out') }}">Thanh toán</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="#">Tin tức<i class="fa fa-angle-down"></i></a>
                                    <ul role="menu" class="sub-menu" id="post-cate-menu">
                                        <!-- Danh mục bài viết sẽ được load bằng JS -->
                                    </ul>
                                </li>

                                <!-- <li><a href="{{ URL::to('/cart') }}">Giỏ hàng</a></li> -->
                                <li><a href="{{ URL::to('/videos') }}">Videos</a></li>
                                <li><a href="{{ URL::to('/contact-us') }}">Liên hệ</a></li>
                            </ul>
                        </div>

                    </div>
                    <div class="col-sm-5">
                        <div class="search_box pull-right" style="margin-top: -40px;">
                            <input type="text" name="keywords_submit" id="keywords" placeholder="Tìm kiếm sản phẩm" />
                            <button onclick="searchProduct()" style="width:100px; margin-top:0; color:#866;font-size:16px;" name="search_items" class="btn btn-primary btn-sm">Tìm</button>

                            <div id="search-ajax"></div>
                            <style>
                                #search-ajax {
                                    position: absolute;
                                    top: 35px;
                                    left: 60px;
                                    width: 350px;
                                    background: white;
                                    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                                    z-index: 999;
                                    display: none;
                                }
                            </style>
                        </div>

                    </div>
                </div>
            </div>
        </div><!--/header-bottom-->
    </header><!--/header-->


    <!-- slider-productslider -->

    @yield('product_slider')


    <br>

    <div class="modal fade" id="watch_video" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="video_title">Modal title</h4>

                </div>
                <div class="modal-body">
                    <div id="video_link"></div>
                    <div id="video_desc"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng video</button>
                </div>
            </div>
        </div>
    </div>
    <section>
        <div class="container">
            <div class="row">

                @yield('sidebar_content')






                @yield('content')




            </div>
        </div>
        </div>
    </section>

    <section id="post_slider_section" style="background-color: #e0f7fa;">
        <div class="container">
            <div class="post_slider_header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0;">
                <h2 style="margin: 0; font-size: 30px;">Tin tức công nghệ</h2>
                <a href="/tin-tuc" style="color: #1a73e8; text-decoration: none;">Xem tất cả</a>
            </div>

            <div class="swiper post_swiper">
                <div class="swiper-wrapper" id="post_slider_track">
                    <!-- JS sẽ render các bài viết tại đây -->

                </div>

                <!-- Nút chuyển -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <br>
            </div>
        </div>
    </section>
    <div id="compare-bar" style="
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background:rgba(145, 255, 246, 0.53);
    border-top: 1px solid #ccc;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 30px;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
">
        <div id="compare-items" style="
    display: flex;
    gap: 15px;
    overflow-x: auto;
    overflow-y: visible; /* Cho phép phần tử con tràn theo chiều dọc */
    flex: 1;
    padding-right: 15px;
    position: relative; /* Cần thiết để z-index hoạt động đúng */
    z-index: 10; /* Đảm bảo nằm trên những phần khác */
">
        </div>


        <div style="flex-shrink: 0; display: flex; gap: 10px;">
            <button id="compare-now" style="
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        ">So sánh</button>
            <button id="clear-compare" style="
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        ">Xóa tất cả</button>
        </div>
    </div>

    <style>
        #compare-bar::-webkit-scrollbar {
            display: none;
        }

        .compare-item {
            min-width: 60px;
            height: 60px;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            background: #f8f8f8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }

        .compare-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>









    <footer id="footer"><!--Footer-->
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="companyinfo">
                            <h2>TECH STORM</h2>
                            <p><strong>
                                    HÀ ĐÔNG, HÀ NỘI
                                </strong></p>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="row" id="recent-video-thumbs"></div>
                    </div>
                    <div class="col-sm-3">
                        <div class="address">
                            <!-- <img src="{{asset('public/frontend/images/mapHD.jpg')}}" alt="" /> -->
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3725.715042319643!2d105.77153667486057!3d20.96395568066921!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313452d6c29ed1e1%3A0x2c65752567674202!2zMjg1IMSQLiBUw7QgSGnhu4d1LCBIw6AgQ-G6p3UsIEjDoCDEkMO0bmcsIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1743086406326!5m2!1svi!2s" width="270" height="225" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            <!-- <p>Hà Đông, Hà Nội</p> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-widget">
            <div class="container">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="single-widget">
                            <h2>Dịch vụ của chúng tôi</h2>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="{{url('/track-order')}}">Tra cứu đơn hàng</a></li>
                                <li><a href="{{url('/buy-guide')}}">Hướng dẫn mua hàng trưc tuyến</a></li>
                                <li><a href="{{url('/payment-guide')}}">Hướng dẫn thanh toán</a></li>
                                <li><a href="{{url('/contact-us')}}">Góp ý, Khiếu nại</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="single-widget">
                            <h2>Chính sách chung</h2>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="{{url('/terms')}}">Chính sách, quy định chung</a></li>
                                <li><a href="#">Chính sách bảo hành</a></li>
                                <li><a href="#">Chính sách hàng chính hãng</a></li>
                                <li><a href="#">Chính sách giao hàng</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="single-widget">
                            <h2>Giới thiệu về chúng tôi</h2>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="{{url('/introduction')}}">Giới thiệu công ty</a></li>
                                <li><a href="{{url('/contact-us')}}">Liên hệ hợp tác</a></li>
                                <li><a href="#">Copyright</a></li>
                            </ul>

                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="single-widget">
                            <h2>Theo dõi chúng tôi tại</h2>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="#">
                                        <i class="fa-brands fa-facebook" style="font-size: 25px;"></i>
                                    </a>
                                    <a href="#">
                                        <i class="fa-brands fa-instagram" style="font-size: 25px;"></i>
                                    </a>
                                    <a href="#">
                                        <i class="fa-brands fa-youtube" style="font-size: 25px;"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-3 col-sm-offset-1">
                        <div class="single-widget">
                            <h2>Đăng ký email đề nhận tin khuyến mãi</h2>
                            <form action="javascript:void(0);" class="searchform" id="promotionForm">
                                <input type="text" id="promotion_email" placeholder="Your email address" />
                                <button type="submit" class="btn btn-default" id="promotion_email_button"><i class="fa fa-arrow-circle-o-right"></i></button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <p class="pull-left">Copyright © 2013.</p>
                    <p class="pull-right"> <span><a target="_blank" href=""></a></span></p>
                </div>
            </div>
        </div>

    </footer><!--/Footer-->



    <script src="{{asset('frontend/js/jquery.js')}}"></script>
    <script src="{{asset('frontend/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('frontend/js/jquery.scrollUp.min.js')}}"></script>
    <script src="{{asset('frontend/js/price-range.js')}}"></script>
    <script src="{{asset('frontend/js/jquery.prettyPhoto.js')}}"></script>
    <script src="{{asset('frontend/js/main.js')}}"></script>
    <script src="{{asset('frontend/js/sweetalert.min.js')}}"></script>
    <script src="{{asset('frontend/js/lightgallery-all.min.js')}}"></script>
    <script src="{{asset('frontend/js/lightslider.js')}}"></script>
    <script src="{{asset('frontend/js/prettify.js')}}"></script>
    <!-- <script src="{{asset('frontend/js/vlite.js')}}"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset('backend/js/simple.money.format.js')}}"></script>






    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v22.0"></script>




    <script>
        let Email = '';
        document.getElementById('promotionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Email = document.getElementById('promotion_email').value;
            fetchPromotion(Email);
        });

        // document.addEventListener("DOMContentLoaded", function() {
        //     var titleElement = document.getElementById("post-title");
        //     if (titleElement) {
        //         var offsetTop = titleElement.getBoundingClientRect().top + window.scrollY - 10; // Cuộn xuống thêm 10px
        //         window.scrollTo({
        //             top: offsetTop,
        //             behavior: "smooth"
        //         });
        //     }
        // });
        function fetchPromotion(email) {
            fetch(`/api/contact-us`, {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        email: Email,
                        message: "Email đăng kí nhận khuyến mãi",
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        swal("Thông báo", "Bạn đã gửi email thành công!", "success");
                        Email = '';
                    } else {
                        swal("Lỗi", data.message || "Gửi email thất bại", "error");
                    }
                })
                .catch(error => {
                    console.error("Lỗi: " + error);
                })
        }

        function showNotification(message, bgColor) {
            let alertBox = document.createElement("div");
            alertBox.textContent = message;
            alertBox.style.position = "fixed";
            alertBox.style.top = "10px";
            alertBox.style.left = "50%";
            alertBox.style.transform = "translateX(-50%)";
            alertBox.style.background = bgColor;
            alertBox.style.color = "white";
            alertBox.style.padding = "10px 20px";
            alertBox.style.borderRadius = "5px";
            alertBox.style.zIndex = "9999";
            alertBox.style.boxShadow = "0px 4px 6px rgba(0,0,0,0.1)";
            document.body.appendChild(alertBox);

            setTimeout(function() {
                alertBox.style.opacity = "0";
                setTimeout(() => alertBox.remove(), 500);
            }, 3000);
        }
        $(document).ready(function() {
            $('.min-price').simpleMoneyFormat();
            $('.max-price').simpleMoneyFormat();
        });
        const rangeMin = document.getElementById('range-min');
        const rangeMax = document.getElementById('range-max');
        const minPrice = document.getElementById('min-price');
        const maxPrice = document.getElementById('max-price');

        // Hàm cập nhật và format lại giá
        function updateFormattedPrice(inputElement, value) {
            inputElement.value = value;
            $(inputElement).simpleMoneyFormat(); // Áp dụng lại
        }

        // Khi kéo slider
        rangeMin.addEventListener('input', function() {
            updateFormattedPrice(minPrice, this.value);
        });

        rangeMax.addEventListener('input', function() {
            updateFormattedPrice(maxPrice, this.value);
        });

        minPrice.addEventListener('input', function() {
            const raw = this.value.replace(/\./g, '').replace(/[^\d]/g, '');
            rangeMin.value = raw;
        });

        maxPrice.addEventListener('input', function() {
            const raw = this.value.replace(/\./g, '').replace(/[^\d]/g, '');
            rangeMax.value = raw;
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            let user_name = localStorage.getItem("user_name") || sessionStorage.getItem('user_name');
            const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');
            updateCompareSection();
            if (userId) {
                // fetch(`/api/users/${userId}`)
                //     .then(res => res.json())
                //     .then(data => {
                //         document.getElementById("user_name").innerText = " " + `${data.data.name}`;
                //     })
                document.getElementById("user_name").innerText = " " + user_name;
            } else {

            }
            if (token) {
                document.getElementById("logged-in-menu").style.display = "block";
                document.getElementById("logged-out-menu").style.display = "none";
                // document.getElementById("user_name").innerText = " " + user_name;
            } else {
                // Nếu token không hợp lệ thì đăng xuất người dùng
                localStorage.removeItem("auth_token") || sessionStorage.removeItem('auth_token');
                document.getElementById("logged-in-menu").style.display = "none";
                document.getElementById("logged-out-menu").style.display = "block";
            }

            //cart-bubble
            const cartBubble = document.getElementById('cart-bubble');
            const cartModal = document.getElementById('cart-modal');
            const cartItemsList = document.getElementById('cart-items');
            const closeModalBtn = document.getElementById('close-modal');

            if (userId && token) {
                const cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];
                document.getElementById('cart-count').textContent = cart.length;
            } else {
                document.getElementById('cart-count').textContent = 0;
            }

            // Kéo thả
            let isDragging = false;
            let offsetX, offsetY;

            cartBubble.addEventListener('mousedown', function(e) {
                isDragging = true;
                offsetX = e.clientX - cartBubble.getBoundingClientRect().left;
                offsetY = e.clientY - cartBubble.getBoundingClientRect().top;
                e.preventDefault();
            });

            document.addEventListener('mousemove', function(e) {
                if (isDragging) {
                    let newLeft = e.clientX - offsetX;
                    let newTop = e.clientY - offsetY;

                    // Giới hạn không cho ra ngoài màn hình
                    const bubbleWidth = cartBubble.offsetWidth;
                    const bubbleHeight = cartBubble.offsetHeight;
                    const maxLeft = window.innerWidth - bubbleWidth;
                    const maxTop = window.innerHeight - bubbleHeight;

                    // Clamp giá trị
                    newLeft = Math.max(0, Math.min(newLeft, maxLeft));
                    newTop = Math.max(0, Math.min(newTop, maxTop));

                    cartBubble.style.left = newLeft + 'px';
                    cartBubble.style.top = newTop + 'px';
                    cartBubble.style.bottom = 'auto';
                    cartBubble.style.right = 'auto';
                }
            });


            document.addEventListener('mouseup', function(e) {
                if (isDragging) {
                    isDragging = false;
                    const bubbleRect = cartBubble.getBoundingClientRect();
                    const screenWidth = window.innerWidth;

                    if (bubbleRect.left + bubbleRect.width / 2 < screenWidth / 2) {
                        cartBubble.style.left = '10px';
                        cartBubble.style.right = 'auto';
                    } else {
                        cartBubble.style.left = 'auto';
                        cartBubble.style.right = '10px';
                    }
                }
            });
            closeModalBtn.addEventListener('click', function() {
                cartModal.style.display = 'none';
            });
            // Bắt sự kiện double click để mở modal
            cartBubble.addEventListener('dblclick', function(e) {
                e.preventDefault(); // Ngăn redirect

                const cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];

                const cartItems = document.getElementById('cart-items');
                cartItems.innerHTML = ''; // Clear cũ

                if (cart.length === 0) {
                    cartItems.innerHTML = `
            <tr>
                <td colspan="4">Giỏ hàng trống</td>
            </tr>
        `;
                } else {
                    cart.forEach((item, index) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                <td><img src="{{ asset('uploads/product/') }}/${item.product_image}" alt="${item.product_name}" width="80px"/></td>
                <td>${item.product_name}</td>
                <td><input type="number" class="cart-qty-input" value="${item.quantity}" min="1" /></td>
                <td><button class="delete-product">X</button></td>
            `;
                        cartItems.appendChild(tr);

                        // Xử lý sự kiện sửa số lượng
                        const qtyInput = tr.querySelector('.cart-qty-input');
                        qtyInput.addEventListener('change', function() {
                            cart[index].quantity = parseInt(this.value) || 1;
                            localStorage.setItem(`cart_${userId}`, JSON.stringify(cart));
                        });

                        // Xử lý sự kiện xóa sản phẩm
                        const deleteBtn = tr.querySelector('.delete-product');
                        deleteBtn.addEventListener('click', function() {
                            cart.splice(index, 1);
                            localStorage.setItem(`cart_${userId}`, JSON.stringify(cart));
                            cartBubble.dispatchEvent(new Event('dblclick')); // Load lại
                        });
                    });
                }

                cartModal.style.display = 'flex';
            });
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.add-to-compare');
                if (btn) {
                    e.preventDefault();

                    const productId = btn.getAttribute('data-product_id');
                    const productImage = btn.getAttribute('data-product_image');
                    const productName = btn.getAttribute('data-product_name');
                    if (!userId || !token) {
                        alert("Vui lòng đăng nhập để thêm vào so sánh.");
                        return;
                    }

                    const compareKey = `compare_${userId}`;
                    let compareList = JSON.parse(sessionStorage.getItem(compareKey)) || [];

                    // Kiểm tra nếu product_id đã tồn tại
                    const exists = compareList.some(item => item.product_id === productId);
                    if (exists) {
                        swal("Thông báo", "Sản phẩm đã có trong danh sách so sánh!", "info");
                        return;
                    }

                    // Kiểm tra số lượng tối đa
                    if (compareList.length >= 3) {
                        swal("Thông báo", "Bạn chỉ có thể so sánh tối đa 3 sản phẩm!", "warning");
                        return;
                    }

                    // Thêm vào danh sách
                    compareList.push({
                        product_id: productId,
                        product_image: productImage,
                        product_name: productName
                    });

                    sessionStorage.setItem(compareKey, JSON.stringify(compareList));

                    swal("Thành công", "Đã thêm sản phẩm vào danh sách so sánh!", "success");
                    updateCompareSection();
                }
            });

            function updateCompareSection() {
                const compareKey = `compare_${userId}`;
                const compareList = JSON.parse(sessionStorage.getItem(compareKey)) || [];
                const container = document.getElementById('compare-items');
                container.innerHTML = '';

                const compareBar = document.getElementById('compare-bar');
                if (compareList.length === 0) {
                    compareBar.style.display = 'none';
                    return;
                }

                compareBar.style.display = 'flex';

                compareList.forEach(item => {
                    const el = document.createElement('div');
                    el.className = 'compare-item';
                    el.setAttribute('data-id', item.product_id);
                    el.style.cssText = `
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 4px;
            background: #f9f9f9;
            min-width: 160px;
            position: relative;
        `;

                    el.innerHTML = `
            <img 
                src="/uploads/product/${item.product_image}" 
                alt="${item.product_name}" 
                style="width: 50px; height: 50px; object-fit: cover; margin-right: 8px;">
            <span style="
                flex: 1; 
                font-size: 14px; 
                white-space: nowrap; 
                overflow: hidden; 
                text-overflow: ellipsis;"
            >${item.product_name.length > 30 ?
                item.product_name.substring(0, 30) + "..." :
                item.product_name}</span>
            <button class="remove-compare" data-id="${item.product_id}" style="
                background: none;
                border: none;
                color: red;
                font-weight: bold;
                margin-left: 8px;
                cursor: pointer;
            ">&times;</button>
        `;

                    container.appendChild(el);
                });
                container.querySelectorAll('.remove-compare').forEach(button => {
                    button.addEventListener('click', function() {
                        const idToRemove = this.getAttribute('data-id');
                        const updatedList = compareList.filter(p => p.product_id != idToRemove);
                        sessionStorage.setItem(compareKey, JSON.stringify(updatedList));
                        updateCompareSection(); // Cập nhật lại giao diện
                    });
                });
                fetchCompare();

            }

            let isSubmitting = false;
            document.getElementById('clear-compare').addEventListener('click', () => {
                swal({
                    title: "Bạn xác nhận xóa?",
                    text: "",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Tiếp tục",
                    cancelButtonText: "Không",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        if (isSubmitting) return; // kiểm tra lại lần nữa
                        isSubmitting = true;

                        // Disable nút confirm ngay lập tức
                        const confirmBtn = document.querySelector('.sweet-alert .confirm');
                        if (confirmBtn) {
                            confirmBtn.disabled = true;
                            confirmBtn.innerText = "Đang xử lý...";
                        }

                        const compareKey = `compare_${userId}`;
                        sessionStorage.removeItem(compareKey);
                        updateCompareSection();
                        swal("Thành công", "Đã xóa danh sách so sánh!", "success");

                    } else {
                        swal("Đã hủy", " ", "error");
                    }
                });
                fetchCompare();

            });



            document.getElementById('compare-now').addEventListener('click', () => {
                window.location.href = '/compare'; // Thay bằng link so sánh thực tế của bạn
            });


            // Nếu click ra ngoài modal content thì cũng đóng
            cartModal.addEventListener('click', function(e) {
                if (e.target === cartModal) {
                    cartModal.style.display = 'none';
                }
            });

        });




        // Xử lý đăng xuất
        document.getElementById("logout-button").addEventListener("click", function(event) {
            event.preventDefault();
            let token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

            fetch("{{ url('/api/logout') }}", {
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        localStorage.removeItem("auth_token") || sessionStorage.removeItem('auth_token');
                        localStorage.removeItem("user_id");
                        localStorage.removeItem("user_email");
                        localStorage.removeItem("user_name");

                        alert("Bạn đã đăng xuất!");
                        window.location.href = "{{ url('/login') }}";
                    }
                })
                .catch(error => {
                    console.error("Lỗi đăng xuất:", error);
                });
        });

        // cart_bubble
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("keywords");
            const resultBox = document.getElementById("search-ajax");

            let timeout = null;

            searchInput.addEventListener("keyup", function() {
                clearTimeout(timeout);
                const query = this.value.trim();

                if (query.length === 0) {
                    resultBox.style.display = "none";
                    resultBox.innerHTML = "";
                    return;
                }

                timeout = setTimeout(() => {
                    fetch(`{{ url('/api/products') }}?search=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.length > 0) {
                                let html = "";
                                data.data.forEach(product => {
                                    html += `
                                <a href="/product-details/${product.product_slug}">
                                    <div style="display: flex; align-items: center;">
                                        <img src="/uploads/product/${product.product_image}" alt="${product.product_name}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                        <div>
                                            <strong>${product.product_name.length > 70 ? product.product_name.substring(0, 70) + '...' : product.product_name}</strong><br>
                                            <span style="color: red;">${Number(product.product_price).toLocaleString()} đ</span>
                                        </div>
                                    </div>
                                </a>`;
                                });

                                resultBox.innerHTML = html;
                                resultBox.style.display = "block";
                            } else {
                                resultBox.innerHTML = "<div style='padding:10px;'>Không tìm thấy sản phẩm.</div>";
                                resultBox.style.display = "block";
                            }
                        })
                        .catch(err => {
                            resultBox.innerHTML = "<div style='padding:10px;'>Lỗi khi tìm kiếm.</div>";
                            resultBox.style.display = "block";
                            console.error(err);
                        });
                }, 300); // debounce 300ms
            });

            document.addEventListener("click", function(e) {
                if (!document.querySelector(".search_box").contains(e.target)) {
                    resultBox.style.display = "none";
                }
            });
        });

        function searchProduct() {
            const keyword = document.getElementById('keywords').value.trim();

            if (keyword == '') {
                swal("Vui lòng nhập từ khóa!", "", "warning");
                return;
            } else {
                window.location.href = `/search?q=${encodeURIComponent(keyword)}`;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            fetch('/api/postcates')
                .then(response => response.json())
                .then(data => {
                    if (data.success && Array.isArray(data.data)) {
                        const activeCategories = data.data.filter(cate => cate.cate_post_status == 1);
                        const menu = document.getElementById('post-cate-menu');

                        activeCategories.forEach(cate => {
                            const li = document.createElement('li');
                            li.innerHTML = `<a href="/post-cate/${cate.cate_post_slug}">${cate.cate_post_name}</a>`;
                            menu.appendChild(li);
                        });
                    } else {
                        console.error("Không lấy được danh mục bài viết hợp lệ.");
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi fetch danh mục bài viết:", error);
                });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch data from the API
            fetch('/api/posts')
                .then(response => response.json())
                .then(data => {
                    const posts = data.success && Array.isArray(data.data) ? data.data : [];
                    const track = document.getElementById('post_slider_track');

                    if (posts.length > 0) {
                        const html = posts.map(post => {
                            const postURL = `/post/${post.post_slug}`;
                            return `
                                <div class="swiper-slide"  style="display: flex; justify-content: center;">
                                    <div class="post_item" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                        <a href="${postURL}">
                                            <img src="/uploads/post/${post.post_image}" alt="${post.post_title}" style="width: 100%; height: 180px; object-fit: cover;">
                                        </a>
                                        <div class="post_item_content" style="padding: 12px;">
                                            <h3 class="post_item_title" style="font-size: 18px; margin: 0 0 8px 0;">
                                                <a href="${postURL}" style="text-decoration: none; color: #333;">${post.post_title}</a>
                                            </h3>
                                            <p class="post_item_description" style="font-size: 14px; color: #666;">
                                                ${post.post_desc.slice(0, 80)}...
                                            </p>
                                        </div>
                                    </div>
                                </div>

                `;
                        }).join('');

                        track.innerHTML = html;

                        new Swiper('.post_swiper', {
                            slidesPerView: 3,
                            spaceBetween: 20,
                            loop: true,
                            autoplay: {
                                delay: 4000,
                                disableOnInteraction: false,
                            },
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev',
                            },
                            breakpoints: {
                                0: {
                                    slidesPerView: 1
                                },
                                768: {
                                    slidesPerView: 2
                                },
                                992: {
                                    slidesPerView: 3
                                }
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching posts:', error);
                });


        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetchBanners();
            fetchVideos();

            function fetchBanners() {
                fetch("{{ url('/api/banners') }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && Array.isArray(data.data)) {
                            const activeBanners = data.data
                                .filter(banner => banner.banner_status === 1)
                                .sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at)); // Sắp xếp mới nhất

                            displayBanners(activeBanners);
                        } else {
                            console.error("Không thể lấy dữ liệu banner:", data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Lỗi khi gọi API:", error);
                    });
            }

            function displayBanners(banners) {
                const bannerCarousel = document.getElementById("banner-carousel");
                const indicators = document.querySelector(".carousel-indicators");

                if (!bannerCarousel || !indicators) return;

                bannerCarousel.innerHTML = "";
                indicators.innerHTML = "";

                banners.slice(0, 6).forEach((banner, index) => {
                    const itemClass = index === 0 ? "item active" : "item";
                    const bannerItem = `
                    <div class="${itemClass}">
                        <img src="{{ asset('uploads/banner/') }}/${banner.banner_image}" alt="${banner.banner_name}" />
                    </div>
                `;
                    bannerCarousel.innerHTML += bannerItem;

                    const indicator = `<li data-target="#slider-carousel" data-slide-to="${index}" class="${index === 0 ? 'active' : ''}"></li>`;
                    indicators.innerHTML += indicator;
                });
            }


            function fetchVideos() {
                fetch(`/api/videos`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const shuffled = data.data.sort(() => 0.5 - Math.random());
                            const randomThree = shuffled.slice(0, 4);
                            activeVideos = randomThree;
                            displayVideos(activeVideos);
                        } else {
                            console.error("Không thể lấy dữ liệu banner:", data.message);
                        }
                    })
                    .catch(error => {
                        console.log("Đã xảy ra lỗi API " + data.message);
                    })
            }

            function displayVideos(Videos) {
                const container = document.getElementById("recent-video-thumbs");
                if (!container) return;
                container.innerHTML = "";

                Videos.forEach(video => {
                    const video_thumb = document.createElement('div');
                    video_thumb.className = 'col-sm-3';
                    video_thumb.innerHTML = `
                            <div class="video-gallery text-center">
                                <a href="#" class="watch-video" data-title="${video.video_title}" data-link="${video.video_link}">
                                    <div class="iframe-img">
                                        <img src="{{ asset('uploads/video_thumbs/') }}/${video.video_thumb}" alt="${video.video_title}" />
                                    </div>
                                    <div class="overlay-icon">
                                        <i class="fa fa-play-circle-o"></i>
                                    </div>
                                </a>
                                <h2><strong>${video.video_title || ''}</strong></h2>
                            </div>
                        `;
                    container.appendChild(video_thumb);
                });

                // Gán sự kiện click mở modal
                container.querySelectorAll('.watch-video').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const title = this.dataset.title;
                        const link = this.dataset.link;

                        const embedLink = convertToEmbedLink(link);

                        document.getElementById('video_title').textContent = title;
                        document.getElementById('video_link').innerHTML = `
                                <iframe width="100%" height="335" src="${embedLink}" frameborder="0" allowfullscreen></iframe>
                            `;

                        $('#watch_video').modal('show');
                    });
                });

                // Chuyển link YouTube sang dạng embed
                function convertToEmbedLink(url) {
                    let videoId = '';
                    const watchPattern = /(?:youtube\.com\/watch\?v=)([^&]+)/;
                    const shortPattern = /(?:youtu\.be\/)([^?]+)/;

                    if (watchPattern.test(url)) {
                        videoId = url.match(watchPattern)[1];
                    } else if (shortPattern.test(url)) {
                        videoId = url.match(shortPattern)[1];
                    }

                    return `https://www.youtube.com/embed/${videoId}`;
                }
                $('#watch_video').on('hidden.bs.modal', function() {
                    document.getElementById('video_link').innerHTML = '';
                });
            }

        });
    </script>
    <script>
        function toggleItems(hiddenItems, toggleBtn, expandText = 'Xem thêm', collapseText = 'Rút gọn') {
            const isExpanded = toggleBtn.getAttribute('data-expanded') === 'true';
            hiddenItems.forEach(li => li.style.display = isExpanded ? 'none' : '');
            toggleBtn.innerHTML = isExpanded ?
                `<i class="fa fa-chevron-down"></i> ${expandText}` :
                `<i class="fa fa-chevron-up"></i> ${collapseText}`;
            toggleBtn.setAttribute('data-expanded', !isExpanded);
        }

        function fetchBrands() {
            fetch('/api/brands')
                .then(res => res.json())
                .then(data => {
                    const brandList = document.getElementById('brands-list');
                    const toggleBtn = document.getElementById('brand-toggle-btn');
                    if (!brandList || !Array.isArray(data.data)) return;

                    brandList.innerHTML = '';
                    const visibleCount = 6;
                    const hiddenItems = [];
                    brandsDiplay = data.data.sort((a, b) => a.brand_order - b.brand_order);
                    brandsDiplay.forEach((brand, index) => {
                        const li = document.createElement('li');
                        li.innerHTML = `<a href="/brand/${brand.brand_slug}">${brand.brand_name}</a>`;
                        if (index < visibleCount) {
                            brandList.appendChild(li);
                        } else {
                            li.style.display = 'none';
                            hiddenItems.push(li);
                            brandList.appendChild(li);
                        }
                    });

                    if (hiddenItems.length > 0) {
                        toggleBtn.style.display = 'inline-block';
                        toggleBtn.setAttribute('data-expanded', 'false');
                        toggleBtn.onclick = () => toggleItems(hiddenItems, toggleBtn);
                    } else {
                        toggleBtn.style.display = 'none';
                    }
                })
                .catch(err => console.error('Lỗi khi lấy thương hiệu:', err));
        }

        // function fetchCategories() {
        //     fetch('/api/categories')
        //         .then(res => res.json())
        //         .then(data => {
        //             const accordion = document.getElementById('category-accordian');
        //             const toggleBtn = document.getElementById('category-toggle-btn');
        //             if (!accordion || !Array.isArray(data.data)) return;

        //             accordion.innerHTML = '';
        //             const parents = data.data.filter(cat => cat.category_parent === 0);
        //             const allPanels = [];

        //             parents.forEach(parent => {
        //                 const panel = document.createElement('div');
        //                 panel.className = 'panel panel-default';

        //                 panel.innerHTML = `
        //                                             <div class="panel-heading">
        //                                                 <h4 class="panel-title">
        //                                                     <a href="/category-parent/${parent.category_slug}">${parent.category_name}</a>
        //                                                     <a data-toggle="collapse" data-parent="#category-accordian" href="#category-${parent.category_id}">
        //                                                         <span class="badge pull-right"><i class="fa fa-plus"></i></span>
        //                                                     </a>
        //                                                 </h4>
        //                                             </div>
        //                                             <div id="category-${parent.category_id}" class="panel-collapse collapse">
        //                                                 <div class="panel-body"><ul></ul></div>
        //                                             </div>
        //                                         `;

        //                 const ul = panel.querySelector('ul');

        //                 if (Array.isArray(parent.children)) {
        //                     parent.children.forEach(child => {
        //                         const li = document.createElement('li');
        //                         li.innerHTML = `<a href="/category/${child.category_slug}">${child.category_name}</a>`;
        //                         ul.appendChild(li);
        //                     });
        //                 }

        //                 accordion.appendChild(panel);
        //                 allPanels.push(panel);
        //             });

        //             const visibleCount = 6;
        //             const hiddenItems = allPanels.slice(visibleCount);
        //             hiddenItems.forEach(p => p.style.display = 'none');

        //             if (allPanels.length > visibleCount) {
        //                 toggleBtn.style.display = 'inline-block';
        //                 toggleBtn.setAttribute('data-expanded', 'false');
        //                 toggleBtn.onclick = () => toggleItems(hiddenItems, toggleBtn);
        //             } else {
        //                 toggleBtn.style.display = 'none';
        //             }
        //         })
        //         .catch(err => console.error('Lỗi khi lấy danh mục:', err));
        // }
        function fetchCategories() {
            fetch('/api/categories')
                .then(res => res.json())
                .then(data => {
                    const accordion = document.getElementById('category-accordian');
                    const toggleBtn = document.getElementById('category-toggle-btn');
                    if (!accordion || !Array.isArray(data.data)) return;

                    accordion.innerHTML = '';

                    // Sắp xếp danh mục cha theo category_order
                    const parents = data.data
                        .filter(cat => cat.category_parent === 0)
                        .sort((a, b) => a.category_order - b.category_order);

                    const allPanels = [];

                    parents.forEach(parent => {
                        const panel = document.createElement('div');
                        panel.className = 'panel panel-default';

                        panel.innerHTML = `
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="/category-parent/${parent.category_slug}">${parent.category_name}</a>
                            <a data-toggle="collapse" data-parent="#category-accordian" href="#category-${parent.category_id}">
                                <span class="badge pull-right"><i class="fa fa-plus"></i></span>
                            </a>
                        </h4>
                    </div>
                    <div id="category-${parent.category_id}" class="panel-collapse collapse">
                        <div class="panel-body"><ul></ul></div>
                    </div>
                `;

                        const ul = panel.querySelector('ul');

                        if (Array.isArray(parent.children)) {
                            // Sắp xếp danh mục con theo category_order
                            const sortedChildren = parent.children.sort((a, b) => a.category_order - b.category_order);

                            sortedChildren.forEach(child => {
                                const li = document.createElement('li');
                                li.innerHTML = `<a href="/category/${child.category_slug}">${child.category_name}</a>`;
                                ul.appendChild(li);
                            });
                        }

                        accordion.appendChild(panel);
                        allPanels.push(panel);
                    });

                    // Hiển thị mặc định 6 nhóm đầu, các nhóm còn lại ẩn
                    const visibleCount = 6;
                    const hiddenItems = allPanels.slice(visibleCount);
                    hiddenItems.forEach(p => p.style.display = 'none');

                    if (allPanels.length > visibleCount) {
                        toggleBtn.style.display = 'inline-block';
                        toggleBtn.setAttribute('data-expanded', 'false');
                        toggleBtn.onclick = () => toggleItems(hiddenItems, toggleBtn);
                    } else {
                        toggleBtn.style.display = 'none';
                    }
                })
                .catch(err => console.error('Lỗi khi lấy danh mục:', err));
        }

        document.addEventListener("DOMContentLoaded", function() {
            fetchCategories();
            fetchBrands();
        });
    </script>
    <style>
        #compare-bar {
            overflow: visible !important;
            z-index: 1000;
        }

        #compare-items {
            overflow-x: auto;
            overflow-y: visible !important;
            display: flex;
            gap: 15px;
            padding-right: 15px;
        }
    </style>
</body>

</html>