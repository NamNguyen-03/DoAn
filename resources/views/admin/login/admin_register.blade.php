<!DOCTYPE html>

<head>
    <title>Đăng kí Staff</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="application/x-javascript">
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <!-- bootstrap-css -->
    <link rel="stylesheet" href="{{asset('backend/css/bootstrap.min.css')}}">
    <!-- //bootstrap-css -->
    <!-- Custom CSS -->
    <link href="{{asset('backend/css/style.css')}}" rel='stylesheet' type='text/css' />
    <link href="{{asset('backend/css/style-responsive.css')}}" rel="stylesheet" />
    <!-- font CSS -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <!-- font-awesome icons -->
    <link rel="stylesheet" href="{{asset('backend/css/font.css')}}" type="text/css" />
    <link href="{{asset('backend/css/font-awesome.css')}}" rel="stylesheet">
    <!-- //font-awesome icons -->
    <script src="{{asset('backend/js/jquery2.0.3.min.js')}}"></script>

</head>

<body>
    <div class="log-w3">
        <div class="w3layouts-main">
            <h2>Đăng Kí Staff</h2>
            <?php
            // $message = Session::get('message');
            // if ($message) {
            //     echo '<span class=text-alert-green>' . $message . '</span>';
            //     Session::put('message', null);
            // }
            // $message = Session::get('error');
            // if ($error) {
            //     echo '<span class=text-alert>' . $error . '</span>';
            //     Session::put('error', null);
            // }
            // 
            ?>
            <form action="" id="admin-register-form">
                @csrf
                <input type="text" class="ggg" name="admin_name" placeholder="Tên" required="">
                <input type="text" class="ggg" name="admin_email" placeholder="Email" required="">
                <input type="text" class="ggg" name="admin_phone" placeholder="SĐT" required="">
                <input type="password" class="ggg" name="admin_password" placeholder="Mật khẩu" required="">
                <span><input type="checkbox" /> Lưu tài khoản</span>
                <h6><a href="#">Quên mật khẩu?</a></h6>
                <div class="clearfix"></div>
                <input type="submit" value="Đăng kí" name="login">
            </form>
            <!-- <p>Bạn không có tài khoản ư ?<a href="registration.html">Tạo tài khoản mới</a></p> -->
            <a href="{{url('/admin-login')}}">Đăng nhập Admin</a>
        </div>
    </div>
    <script src="{{asset('backend/js/bootstrap.js')}}"></script>
    <script src="{{asset('backend/js/jquery.dcjqaccordion.2.7.js')}}"></script>
    <script src="{{asset('backend/js/scripts.js')}}"></script>
    <script src="{{asset('backend/js/jquery.slimscroll.js')}}"></script>
    <script src="{{asset('backend/js/jquery.nicescroll.js')}}"></script>
    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script><![endif]-->
    <script src="{{asset('backend/js/jquery.scrollTo.js')}}"></script>
</body>
<script>
    document.getElementById("admin-register-form").addEventListener("submit", function(e) {
        e.preventDefault(); // Ngừng việc submit form mặc định

        // Lấy các giá trị từ form
        const admin_name = document.querySelector("input[name='admin_name']").value;
        const admin_email = document.querySelector("input[name='admin_email']").value;
        const admin_phone = document.querySelector("input[name='admin_phone']").value;
        const admin_password = document.querySelector("input[name='admin_password']").value;

        // Cấu trúc dữ liệu để gửi
        const data = {
            admin_name: admin_name,
            admin_email: admin_email,
            admin_phone: admin_phone,
            admin_password: admin_password
        };

        // Gọi Fetch API để gửi yêu cầu POST
        fetch("{{ url('/api/admins') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content") // CSRF Token
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message); // Hiển thị thông báo thành công
                    window.location.href = "{{ url('/admin-login') }}"; // Chuyển hướng đến trang đăng nhập sau khi đăng ký thành công
                } else {
                    alert("Đăng kí không thành công, vui lòng thử lại.");
                }
            })
            .catch(error => {
                console.error("Có lỗi xảy ra:", error);
                alert("Đã xảy ra lỗi khi gửi yêu cầu.");
            });
    });
</script>


</html>