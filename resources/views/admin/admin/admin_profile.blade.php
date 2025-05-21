@extends('admin.admin_layout')
@section('admin_content')
<!-- Đầu trang thông tin Admin -->
<div class="admin-info-container">
    <h1 class="admin-heading">Thông tin Admin</h1>

    <!-- Phần thông tin cá nhân -->
    <div class="admin-details">
        <div class="admin-profile">
            <img src="{{ asset('backend/images/admin_avatar.png') }}" alt="Admin Avatar" class="admin-avatar">
            <h2 class="admin-name" id="admin-name-heading"></h2><br>
            <p class="admin-role" id="admin-role">...</p>
        </div>
        <br>
        <div class="admin-contact">
            <h3>Liên hệ</h3><br>
            <ul>
                <li id="admin-email">Email: ...</li>
                <li id="admin-phone">Phone: ...</li>
            </ul>
        </div>
        <!-- Các nút chức năng -->
        <div class="admin-actions">
            <button class="btn btn-primary" onclick="openChangePassword()">🔒 Đổi mật khẩu</button>
            <button class="btn btn-warning" onclick="openEditAdmin()">✏️ Chỉnh sửa thông tin</button>
            <button class="btn btn-warning" onclick="openAddModal()"><i class="fa fa-plus"></i> Thêm tài khoản admin</button>
            <button class="btn btn-primary" onclick="window.location.href='/admin/all-admin'">Các tài khoản admin</button>
            <button class="btn btn-danger" onclick="logoutAdmin()">🚪 Đăng xuất</button>
        </div>
    </div>
</div>
<!-- Modal Form -->
<div id="adminModal" class="modal">
    <div class="modal-content" id="modal-content">

    </div>
</div>

<script>
    let verifyPass = false;
    let oldPassword = "";
    let newPassword = "";
    let confirmPassword = "";
    let confirm = "";
    document.addEventListener('DOMContentLoaded', function() {
        fetchAdmin();
        verifyPass = false;

    });

    function fetchAdmin() {
        const adminId = localStorage.getItem('admin_id');
        if (!adminId) {
            console.warn("Không tìm thấy admin_id trong localStorage");
            return;
        }

        fetch(`/api/admins/${adminId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const admin = data.data;
                    document.getElementById('admin-name-heading').innerText = admin.admin_name;
                    document.getElementById('admin-email').innerText = "Email: " + admin.admin_email;
                    document.getElementById('admin-phone').innerText = "Phone: " + admin.admin_phone;
                    document.getElementById('admin-role').innerText = data.roles;
                    const adminNameInput = document.getElementById('admin_name_edit')
                    const adminEmailInput = document.getElementById('admin_email_edit')
                    const adminPhoneInput = document.getElementById('admin_phone_edit')
                    if (adminNameInput && adminEmailInput && adminPhoneInput) {
                        adminNameInput.value = admin.admin_name;
                        adminEmailInput.value = admin.admin_email;
                        adminPhoneInput.value = admin.admin_phone;
                    }

                } else {
                    console.error("Không lấy được dữ liệu admin:", data);
                }
            })
            .catch(err => {
                console.error("Lỗi fetch admin:", err);
            });
    }

    // Hàm mở modal
    function openAddModal() {
        let adminModel = document.getElementById("adminModal")
        adminModel.style.display = "block";
        document.getElementById('modal-content').innerHTML = `
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Đăng ký Admin</h2>
        <form id="adminAddForm" onsubmit="submitAdminRegisterForm(event)">
            <div class="form-group">
                <label for="name">Tên</label>
                <input type="text" name="admin_name" class="form-control" id="admin_name" placeholder="Tên" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="admin_email" class="form-control" id="admin_email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" name="admin_phone" class="form-control" id="admin_phone" placeholder="Số điện thoại" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="admin_password" class="form-control" id="admin_password" placeholder="Mật khẩu" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary">Đăng ký</button>
        </form>
    `;
    }

    function openEditAdmin() {
        let adminModel = document.getElementById("adminModal");
        adminModel.style.display = "block";
        document.getElementById('modal-content').innerHTML = `
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Sửa thông tin</h2>
        <form id="adminEditForm" onsubmit="submitAdminEditForm(event)" >
            <div class="form-group">
                <label for="name">Tên</label>
                <input type="text" name="admin_name" class="form-control" id="admin_name_edit" placeholder="Tên" required autocomplete="admin_name">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="admin_email" class="form-control" id="admin_email_edit" placeholder="Email" required autocomplete="admin_email" autofill="false">
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" name="admin_phone" class="form-control" id="admin_phone_edit" placeholder="Số điện thoại" required autocomplete="admin_phone">
            </div>
           <div class="form-group" style="position: relative;display: flex; align-items: center; gap: 10px;">
            <div style="position: relative; flex-grow: 1;">
                <label for="password">Mật khẩu</label>
               <input type="password" name="admin_password_edit" class="form-control" id="admin_password_edit"
                    placeholder="Mật khẩu" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" required>

                <span id="password-check-icon" style="position: absolute; right: 35px; top: 35px; display: none; color: green;">
                    <i class="fa-solid fa-check"></i>
                </span>
                <span id="password-error-icon" style="position: absolute; right: 35px; top: 35px; display: none; color: red;">
                    <i class="fa-solid fa-xmark"></i>
                </span>

                <span class="toggle-password" data-target="admin_password_edit" 
                    style="position: absolute; top: 45px; right: 10px; cursor: pointer;">
                    <i class="fa-solid fa-eye"></i>
                </span>
                </div>
                <button type="button" id="confirm-password-btn" style="margin-top: 40px;margin-bottom:12px;width:25%;height:35px;display: flex;align-items: center;justify-content: center;">Xác nhận</button>
            </div>

            <button type="submit" class="btn btn-primary">Đăng ký</button>
        </form>
        `;
        fetchAdmin();
        document.querySelectorAll(".toggle-password").forEach(icon => {
            icon.addEventListener("click", function() {
                const targetId = this.getAttribute("data-target");
                const input = document.getElementById(targetId);
                const iconElem = this.querySelector("i");

                if (input.type === "password") {
                    input.type = "text";
                    iconElem.classList.remove("fa-eye");
                    iconElem.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    iconElem.classList.remove("fa-eye-slash");
                    iconElem.classList.add("fa-eye");
                }
            });
        });
        document.getElementById('confirm-password-btn').addEventListener('click', function() {
            const confirmPASS = document.getElementById('admin_password_edit').value;
            const passwordInput = document.getElementById('admin_password_edit');
            const successIcon = document.getElementById('password-check-icon');
            const errorIcon = document.getElementById('password-error-icon');
            alert(confirmPASS)
            fetch(`/api/admin/verify-password`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": "Bearer " + localStorage.getItem("admin_token"),
                    },
                    body: JSON.stringify({
                        password: confirmPASS
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Success - Set input color to green and show check icon
                        passwordInput.style.borderColor = "green";
                        successIcon.style.display = "inline-block"; // Show the check mark icon
                        errorIcon.style.display = "none"; // Hide the error icon
                        confirm = true;
                    } else {
                        // Error - Set input color to red and show error icon
                        passwordInput.style.borderColor = "red";
                        successIcon.style.display = "none"; // Hide the check mark icon
                        errorIcon.style.display = "inline-block"; // Show the error icon
                        confirm = false;

                    }
                })
                .catch(error => {
                    console.error("Lỗi API " + error);
                    // In case of an error, show the red error icon
                    passwordInput.style.borderColor = "red";
                    successIcon.style.display = "none";
                    errorIcon.style.display = "inline-block";
                    confirm = false;

                });
        });

    }

    function submitAdminEditForm(event) {
        event.preventDefault();
        const adminId = localStorage.getItem('admin_id');
        const token = localStorage.getItem('admin_token');
        if (!confirm) {
            alert("Vui lòng xác nhận mật khẩu trước");
            return;
        }

        const adminName = document.getElementById('admin_name_edit').value
        const adminEmail = document.getElementById('admin_email_edit').value
        const adminPhone = document.getElementById('admin_phone_edit').value
        alert(adminName + " " + adminEmail + " " + adminPhone);
        fetch(`/api/admins/${adminId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + token
                },
                body: JSON.stringify({
                    admin_name: document.getElementById('admin_name_edit').value,
                    admin_email: document.getElementById('admin_email_edit').value,
                    admin_phone: document.getElementById('admin_phone_edit').value,
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Cập nhật thông tin thành công")
                    window.location.reload();
                } else {
                    alert("Cập nhật thất bại")
                }
            })
            .catch(error => {
                console.error("Lỗi API " + error)
            })

    }

    function openChangePassword() {
        console.log(verifyPass)
        let adminModel = document.getElementById("adminModal");
        adminModel.style.display = "block";
        document.getElementById('modal-content').innerHTML = `
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Đổi mật khẩu</h2>
        <form id="adminChangePassForm" onsubmit="submitChangePasswordForm(event)">
            <div class="form-group" style="position: relative;">
                <label for="password">Mật khẩu cũ</label>
                <input type="password" name="old_password" class="form-control" id="old_password" placeholder="Mật khẩu cũ" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" required>
                <span class="toggle-password" data-target="old_password"><i class="fa-solid fa-eye"></i></span>
            </div>
            <div class="form-group" style="position: relative;">
                <label for="password">Mật khẩu mới</label>
                <input type="password" name="new_password" class="form-control" id="new_password" placeholder="Mật khẩu mới" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" required>
                <span id="new-password-check-icon" style="position: absolute; right: 40px; top: 38px; display: none; align-items: center; gap: 5px;">
                    <i class="fa-solid"></i>
                    <span id="new-password-check-msg" style="font-size: 13px;"></span>
                </span>
                <span class="toggle-password" data-target="new_password"><i class="fa-solid fa-eye"></i></span>
            </div>
            <div class="form-group" style="position: relative;">
                <label for="password">Nhập lại mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Nhập lại mật khẩu" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" required>
                <span id="confirm-password-check-icon" style="position: absolute; right: 40px; top: 38px; display: none; align-items: center; gap: 5px;">
                    <i class="fa-solid"></i>
                    <span id="confirm-password-check-msg" style="font-size: 13px;"></span>
                </span>
                <span class="toggle-password" data-target="confirm_password"><i class="fa-solid fa-eye"></i></span>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    `;

        // Toggle show/hide password
        document.querySelectorAll(".toggle-password").forEach(icon => {
            icon.addEventListener("click", function() {
                const targetId = this.getAttribute("data-target");
                const input = document.getElementById(targetId);
                const iconElem = this.querySelector("i");

                if (input.type === "password") {
                    input.type = "text";
                    iconElem.classList.remove("fa-eye");
                    iconElem.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    iconElem.classList.remove("fa-eye-slash");
                    iconElem.classList.add("fa-eye");
                }
            });
        });

        // Update global oldPassword when user types
        document.getElementById('old_password').addEventListener('input', function() {
            oldPassword = this.value.trim();
            this.style.border = "";
        });

        // Validate new password
        document.getElementById('new_password').addEventListener('input', function() {
            newPassword = this.value.trim();
            const msgIcon = document.getElementById('new-password-check-icon');
            const msgText = document.getElementById('new-password-check-msg');
            const input = this;

            if (!newPassword) {
                msgIcon.style.display = 'none';
                input.style.border = '';
                return;
            }

            msgIcon.style.display = 'flex';

            if (!oldPassword) {
                input.style.border = "2px solid #f88";
                msgText.innerText = "Vui lòng nhập mật khẩu cũ";
                msgIcon.querySelector("i").className = "fa-solid fa-triangle-exclamation text-danger";
            } else if (newPassword.length < 6) {
                input.style.border = "2px solid #f88";
                msgText.innerText = "Mật khẩu phải có ít nhất 6 ký tự";
                msgIcon.querySelector("i").className = "fa-solid fa-triangle-exclamation text-danger";
            } else if (oldPassword && newPassword === oldPassword) {
                input.style.border = "2px solid #f88";
                msgText.innerText = "Mật khẩu mới phải khác mật khẩu cũ";
                msgIcon.querySelector("i").className = "fa-solid fa-triangle-exclamation text-danger";
            } else {
                input.style.border = "2px solid #4caf50";
                msgText.innerText = "Mật khẩu hợp lệ";
                msgIcon.querySelector("i").className = "fa-solid fa-check text-success";
            }
        });

        // Validate confirm password
        document.getElementById('confirm_password').addEventListener('input', function() {
            confirmPassword = this.value.trim();
            const msgIcon = document.getElementById('confirm-password-check-icon');
            const msgText = document.getElementById('confirm-password-check-msg');
            const input = this;

            if (!confirmPassword) {
                msgIcon.style.display = 'none';
                input.style.border = '';
                return;
            }

            msgIcon.style.display = 'flex';

            if (!newPassword || newPassword.length < 6 || newPassword === oldPassword) {
                input.style.border = "2px solid #f88";
                msgText.innerText = "Vui lòng nhập mật khẩu mới hợp lệ";
                msgIcon.querySelector("i").className = "fa-solid fa-triangle-exclamation text-danger";
            } else if (newPassword === confirmPassword) {
                input.style.border = "2px solid #4caf50";
                msgText.innerText = "";
                msgIcon.querySelector("i").className = "fa-solid fa-check text-success";
            } else {
                input.style.border = "2px solid #f88";
                msgText.innerText = "Mật khẩu không trùng";
                msgIcon.querySelector("i").className = "fa-solid fa-triangle-exclamation text-danger";
            }
        });
    }

    // Hàm đóng modal
    function closeModal() {
        document.getElementById("adminModal").style.display = "none";
    }

    function submitChangePasswordForm(event) {
        event.preventDefault();
        let verifyPass = true;
        let errorMsg = '';
        const adminToken = localStorage.getItem("admin_token");
        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }
        // Kiểm tra các điều kiện
        if (!oldPassword || !newPassword || !confirmPassword) {
            errorMsg = 'Vui lòng nhập đầy đủ các trường.';
            verifyPass = false;
        } else if (newPassword.length < 6) {
            errorMsg = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            verifyPass = false;
        } else if (newPassword === oldPassword) {
            errorMsg = 'Mật khẩu mới không được trùng với mật khẩu cũ.';
            verifyPass = false;
        } else if (newPassword !== confirmPassword) {
            errorMsg = 'Mật khẩu xác nhận không khớp.';
            verifyPass = false;
        }

        // Xử lý kết quả
        if (verifyPass) {
            alert(oldPassword + " " + newPassword + " " + confirmPassword);
            fetch(`/api/admin/change-password`, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + adminToken,
                    },
                    body: JSON.stringify({
                        current_password: oldPassword,
                        new_password: newPassword,
                        new_password_confirmation: confirmPassword
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        oldPassword = "";
                        newPassword = "";
                        confirmPassword = "";
                        window.location.reload();
                    } else {
                        alert("Lỗi " + data.message)
                        document.getElementById('old_password').style.border = "2px solid #f88";
                    }
                })
                .catch(error => {
                    console.error("Lỗi API: " + error);
                })
        } else {
            alert(errorMsg);
        }
    }


    function submitAdminRegisterForm(event) {
        event.preventDefault();

        // Lấy dữ liệu từ form
        const adminName = document.getElementById("admin_name").value;
        const adminEmail = document.getElementById("admin_email").value;
        const adminPhone = document.getElementById("admin_phone").value;
        const adminPassword = document.getElementById("admin_password").value;

        // Kiểm tra token từ localStorage
        const adminToken = localStorage.getItem("admin_token");

        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        // Tạo đối tượng FormData
        const formData = new FormData();
        formData.append('admin_name', adminName);
        formData.append('admin_email', adminEmail);
        formData.append('admin_phone', adminPhone);
        formData.append('admin_password', adminPassword);

        // Gửi yêu cầu POST đến API create-admin
        fetch("{{ url('/api/create-admin') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "Authorization": "Bearer " + adminToken,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal();
                } else {
                    alert("Có lỗi xảy ra khi tạo admin mới:" + data.message);
                }
            })
            .catch(error => {
                console.error("Lỗi khi gửi yêu cầu tạo admin:", error);
                alert("Có lỗi xảy ra khi tạo admin mới.");
            });
    }
</script>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .admin-info-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 30px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        position: relative;
        /* Quan trọng để modal định vị theo container */
    }

    .admin-heading {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 30px;
        text-align: center;
        color: #333;
    }

    .admin-details {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .admin-profile {
        text-align: center;
    }

    .admin-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 4px solid #007bff;
        object-fit: cover;
    }

    .admin-name {
        font-size: 26px;
        font-weight: 600;
        margin-top: 10px;
        color: #333;
    }

    .admin-role {
        font-size: 16px;
        color: #666;
    }

    .admin-contact {
        text-align: center;
    }

    .admin-contact h3 {
        font-size: 20px;
        color: #007bff;
        margin-bottom: 10px;
    }

    .admin-contact ul {
        list-style: none;
        padding-left: 0;
        font-size: 16px;
        color: #444;
    }

    .admin-contact li {
        margin-bottom: 6px;
    }

    .admin-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin-top: 20px;
    }

    .admin-actions .btn {
        padding: 10px 18px;
        font-size: 14px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-warning {
        background-color: #ffc107;
        color: black;
    }

    .btn-warning:hover {
        background-color: #e0a800;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    /* Modal */
    .modal {
        display: none;
        position: absolute;
        /* Đặt theo admin-info-container */
        z-index: 10;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        border-radius: 10px;
    }

    .modal-content {
        background-color: #ffffff;
        margin: auto;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        position: relative;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        top: 50%;
        left: 19%;
        transform: translate(-50%, -50%);
    }

    .modal-content h2 {
        margin-top: 0;
        color: #333;
        text-align: center;
    }

    .close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 24px;
        font-weight: bold;
        color: #aaa;
        cursor: pointer;
    }

    .close:hover {
        color: #000;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 12px 40px 12px 12px;
        /* tăng padding bên phải */
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .modal-content button {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        background-color: #28a745;
        color: white;
        transition: background-color 0.3s ease;
    }

    .modal-content button:hover {
        background-color: #218838;
    }

    .toggle-password {
        position: absolute;
        top: 75%;
        right: 12px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        z-index: 2;
    }

    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    input[type="password"]::-webkit-credentials-auto-fill-button,
    input[type="password"]::-webkit-clear-button {
        display: none !important;
    }
</style>

@endsection