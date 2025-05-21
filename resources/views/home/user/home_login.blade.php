@extends('home.home_layout')
@section('content')

<section id="form"><!--form-->
    <div class="container">
        <div class="col-sm-5">
            <div class="login-form">
                <h2 class="h2dangnhap">Đăng nhập vào tài khoản của bạn</h2>
                <form id="login-form">
                    {{csrf_field()}}
                    <input type="text" required title="Vui lòng nhập tài khoản" name="email" placeholder="Tài khoản" />
                    <input type="password" required title="Vui lòng nhập mật khẩu" name="password" placeholder="Password" />
                    <span>
                        <input type="checkbox" class="checkbox" id="remember_me">
                        Nhớ đăng nhập
                    </span>
                    <span>
                        <a href="{{url('/forgot-password')}}" style="float:right">Quên mật khẩu</a>
                    </span>
                    <button type="submit" class="btn btn-default">Đăng nhập</button>
                </form><br>

                <label for="">Đăng nhập bằng:</label>
                <br>
                <style>
                    ul.list-login {
                        margin: 10px;
                        padding: 0;
                    }

                    ul.list-login li {
                        display: inline;
                        margin: 5px;
                    }
                </style>
                <ul class="list-login">
                    <li>
                        <div id="g_id_onload"
                            data-client_id="{{ env('GOOGLE_CLIENT_ID') }}"
                            data-callback="handleGoogleCredentialResponse"
                            data-auto_prompt="false">
                        </div>

                        <div class="g_id_signin"
                            data-type="standard"
                            data-theme="outline"
                            data-size="large"
                            data-logo_alignment="center">
                        </div>

                        <!-- Google Login Button -->

                    <li>
                        <!-- <div class="custom-fb-btn" id="facebookLoginBtn">
                                <img src="{{ asset('frontend/images/facebook_icon.png') }}" alt="Facebook" class="fb-icon">
                                <span>Đăng nhập bằng Facebook</span>
                            </div> -->


                    </li>
                </ul>
                <style>
                    .custom-fb-btn {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: 10px;
                        padding: 10px 20px;
                        border: 1px solid #dcdcdc;
                        border-radius: 5px;
                        background-color: white;
                        color: #3b5998;
                        font-weight: 500;
                        font-size: 14px;
                        cursor: pointer;
                        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                        transition: box-shadow 0.2s;
                        width: 100%;
                        margin-left: -10px;
                        margin-top: 5px;
                    }

                    .custom-fb-btn:hover {
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                    }

                    .fb-icon {
                        width: 20px;
                        height: 20px;
                    }
                </style>
            </div><!--/login form-->
        </div>

        <div class="col-sm-1">
            <h2 class="or">Hoặc</h2>
        </div>

        <div class="col-sm-5">
            <div class="signup-form"><!--sign up form-->
                <h2 class="h2dangnhap">Bạn là người mới?</h2>
                <form id="register-form">
                    {{csrf_field()}}
                    <input type="text" id="customer_name" required placeholder="Họ và tên" />
                    <input type="email" id="customer_email" required placeholder="Email" />
                    <input type="text" id="customer_phone" required pattern="^\d{10,11}$" placeholder="Phone" />
                    <input type="password" id="customer_password" minlength="6" required placeholder="Mật khẩu" />

                    <button type="submit" class="btn btn-default">Đăng kí</button>
                </form>
                <p id="register-message" style="color: red;"></p>
            </div><!--/sign up form-->
        </div>

    </div>
</section><!--/form-->

<script>
    document.getElementById("register-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Ngăn chặn reload trang

        let data = {
            name: document.getElementById("customer_name").value,
            email: document.getElementById("customer_email").value,
            password: document.getElementById("customer_password").value,
            phone: document.getElementById("customer_phone").value
        };

        fetch(`{{ url('/api/users') }}`, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("register-message").style.color = "green";
                    document.getElementById("register-message").innerText = "Đăng ký thành công! Vui lòng đăng nhập.";
                } else {
                    document.getElementById("register-message").innerText = data.message || "Đăng ký thất bại!";
                }
            })
            .catch(error => {
                document.getElementById("register-message").innerText = "Lỗi hệ thống, vui lòng thử lại sau!";
            });
    });

    document.getElementById("login-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Ngăn chặn reload trang

        let email = document.querySelector("input[name='email']").value;
        let password = document.querySelector("input[name='password']").value;
        let remember = document.getElementById('remember_me').checked;
        fetch("{{ url('/api/login') }}", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember_me: remember
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const storage = remember ? localStorage : sessionStorage;
                    storage.setItem("auth_token", data.token);
                    storage.setItem("user_id", data.user.id);
                    storage.setItem("user_email", data.user.email);
                    storage.setItem("user_name", data.user.name);

                    alert("Đăng nhập thành công!");
                    window.location.href = "{{ url('/') }}"; // Chuyển hướng về trang chính
                } else {
                    alert(data.message || "Đăng nhập thất bại!");
                }
            })
            .catch(error => {
                alert("Lỗi hệ thống, vui lòng thử lại sau!");
                console.error("Login error:", error);
            });
    });

    function handleGoogleCredentialResponse(response) {
        const googleToken = response.credential;

        fetch("/api/login/google/callback", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_token: googleToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem("auth_token", data.token);
                    localStorage.setItem("user_id", data.user.id);
                    localStorage.setItem("user_email", data.user.email);
                    localStorage.setItem("user_name", data.user.name);

                    alert("Đăng nhập Google thành công!");
                    window.location.href = "/";
                } else {
                    alert(data.message || "Đăng nhập thất bại!");
                }
            })
            .catch(error => {
                alert("Lỗi hệ thống, vui lòng thử lại sau!");
                console.error("Google login error:", error);
            });
    }


    window.fbAsyncInit = function() {
        FB.init({
            appId: '{{ env("FACEBOOK_CLIENT_ID") }}',
            cookie: true,
            xfbml: false,
            version: 'v19.0'
        });
    };

    document.getElementById("facebookLoginBtn").addEventListener("click", function() {
        FB.login(function(response) {
            if (response.authResponse) {
                const accessToken = response.authResponse.accessToken;

                fetch("{{ url('/api/login/facebook') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            access_token: accessToken
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.token) {
                            localStorage.setItem("auth_token", data.token);
                            localStorage.setItem("user_id", data.user.id);
                            localStorage.setItem("user_email", data.user.email);
                            localStorage.setItem("user_name", data.user.name);
                            alert("Đăng nhập Facebook thành công!");
                            window.location.href = "/";
                        } else {
                            alert("Đăng nhập thất bại!");
                        }
                    })
                    .catch(err => {
                        console.error("Facebook login error:", err);
                        alert("Có lỗi xảy ra khi đăng nhập.");
                    });
            } else {
                alert("Bạn chưa cho phép đăng nhập.");
            }
        }, {
            scope: 'email,public_profile'
        });
    });
</script>

<script src="https://accounts.google.com/gsi/client" async defer></script>
<script async defer crossorigin="anonymous"
    src="https://connect.facebook.net/en_US/sdk.js"></script>

@endsection