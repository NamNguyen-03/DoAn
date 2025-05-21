<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Đăng nhập ADMIN</title>
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
    <!-- Tải jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Tải jQuery Validation Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
</head>

<body>
    <div class="log-w3">
        <div class="w3layouts-main">
            <h2>Đăng nhập ADMIN</h2>
            <form id="loginForm">
                @csrf
                <input type="text" class="ggg" name="admin_email" id="admin_email" placeholder="Email">
                <input type="password" class="ggg" name="admin_password" id="admin_password" placeholder="Password"><br>
                <span><input type="checkbox" /> Lưu tài khoản</span>
                <h6><a href="#">Quên mật khẩu?</a></h6>
                <div class="clearfix"></div>
                <input type="submit" id="loginButton" value="Đăng nhập" name="login">
            </form>
            <p id="error-message" style="color: red; display: none;"></p>
            <a href="{{url('/admin-register')}}">Đăng kí Staff</a>
        </div>
    </div>

    <script src="{{asset('backend/js/bootstrap.js')}}"></script>
    <script src="{{asset('backend/js/jquery.dcjqaccordion.2.7.js')}}"></script>
    <script src="{{asset('backend/js/scripts.js')}}"></script>
    <script src="{{asset('backend/js/jquery.slimscroll.js')}}"></script>
    <script src="{{asset('backend/js/jquery.nicescroll.js')}}"></script>
    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script><![endif]-->
    <script src="{{asset('backend/js/jquery.scrollTo.js')}}"></script>

    <script>
        document.getElementById("loginForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Ngăn reload trang

            const email = document.getElementById("admin_email").value;
            const password = document.getElementById("admin_password").value;

            // Hiển thị lỗi nếu thiếu thông tin
            if (!email || !password) {
                showError("Vui lòng điền đầy đủ thông tin!");
                return;
            }
            loginButton.disabled = true;
            loginButton.innerText = "Đang đăng nhập...";
            fetch("{{ url('/api/admin-login') }}", {
                    method: "POST",
                    credentials: "include",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        email,
                        password
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ✅ Lưu thông tin admin vào localStorage
                        localStorage.setItem("admin_id", data.admin.admin_id);
                        localStorage.setItem("admin_token", data.token);

                        alert("Đăng nhập thành công!");
                        window.location.href = "{{ url('/admin/dashboard') }}";
                    } else {
                        showError(data.message || "Đăng nhập thất bại!");
                    }
                })
                .catch(error => {
                    console.error("Lỗi đăng nhập:", error);
                    showError("Lỗi hệ thống, vui lòng thử lại sau!");
                });

            function showError(message) {
                const errorBox = document.getElementById("error-message");
                errorBox.innerText = message;
                errorBox.style.display = "block";
            }
        });
    </script>



</body>

</html>