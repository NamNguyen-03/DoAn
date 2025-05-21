@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <!-- <a href="{{url('/add-post')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm bài viết
            </a> -->
            Liệt các comment
        </div>
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">

            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" id="searchInput" class="input-sm form-control" placeholder="Search">
                    <span class="input-group-btn">
                        <button id="search-btn" class="btn btn-sm btn-default" onclick="searchComments()" type="button">Go!</button>
                    </span>

                </div>
            </div>
        </div>
        <div id="notify_comment"></div>
        <div class="table-responsive">


            <table class="table table-striped b-t b-light" id="myTable">
                <thead>
                    <tr>

                        <th>Tên sản phẩm</th>
                        <th>Hình ảnh sản phẩm</th>
                        <th>Comment</th>
                        <th>Người viết</th>
                        <th>Ngày giờ</th>
                        <th>Trạng thái</th>
                        <th>Quản lí</th>

                    </tr>
                </thead>
                <tbody id="comment_table_body">
                    <!-- Comment rows will be inserted here -->
                </tbody>
            </table>
        </div>
        <div id="pagination" class="text-center" style="margin-top: 20px;"></div>

    </div>
</div>



<script>
    let allComments = [];
    let currentPage = 1;
    const commentsPerPage = 10;
    let searchQuery = "";

    document.addEventListener("DOMContentLoaded", function() {
        fetchComments();

    });

    function searchComments() {
        searchQuery = document.getElementById('searchInput').value; // Cập nhật từ khóa tìm kiếm
        currentPage = 1; // Đặt lại về trang đầu tiên
        fetchComments(); // Gọi lại API với từ khóa tìm kiếm
    }

    function truncateText(text, maxLength = 60) {
        return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
    }

    function fetchComments() {

        const url = `{{ url('/api/comments') }}?search=${encodeURIComponent(searchQuery)}`;
        console.log(url)
        fetch(url, {
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    allComments = res.data;
                    renderComments();
                    renderPagination();
                }
            })
            .catch(err => console.error("Lỗi khi gọi API:", err));
    }

    function renderComments() {
        const tbody = document.getElementById("comment_table_body");
        tbody.innerHTML = "";

        const start = (currentPage - 1) * commentsPerPage;
        const end = start + commentsPerPage;
        const pageComments = allComments.slice(start, end);

        pageComments.forEach(comment => {
            const productName = comment.product?.product_name ? truncateText(comment.product.product_name) : 'Tên sản phẩm?';
            const productImage = comment.product ? '/uploads/product/' + comment.product.product_image : '/uploads/default.png';
            const ratingStars = renderStars(comment.rating?.rating);

            // Hiển thị comment chính
            tbody.innerHTML += `
            <tr>
                <td style="width: 280px;">
                    <a href="/home-product_details/${comment.product_id}" target="_blank" title="${comment.product?.product_name || ''}">
                        ${productName}
                    </a>
                </td>
                <td style="width: 180px;">
                    <img src="${productImage}" width="120">
                </td>
                <td style="width:450px;">
                    <span style="color: green;">
                        ${comment.comment}
                    </span>
                    <br>
                    <textarea class="form-control reply_comment_${comment.comment_id}" rows="2"></textarea>
                    <button style="margin-top:2px" class="btn btn-default btn-xs btn-reply-comment"
                            data-product_id="${comment.product_id}"
                            data-comment_id="${comment.comment_id}">
                        Trả lời
                    </button>
                </td>
                <td>
                    ${comment.comment_name}
                    ${ratingStars}
                </td>
                <td>${comment.comment_date}</td>
                <td>
                    <input type="button"
                        data-comment_id="${comment.comment_id}"
                        data-comment_status="${comment.comment_status}"
                        class="btn btn-${comment.comment_status == 0 ? 'primary' : 'danger'} btn-xs comment_status_btn"
                        value="${comment.comment_status == 0 ? 'Duyệt' : 'Bỏ duyệt'}">
                </td>
                <td>
                    <a href="javascript:void(0);" onclick="deleteComment(${comment.comment_id})" class="active">
                        <i class="fa fa-trash text"></i>
                    </a>
                </td>
            </tr>`;

            // Hiển thị replies (nếu có)
            if (comment.replies && comment.replies.length > 0) {
                comment.replies.forEach(reply => {
                    const replyRatingStars = renderStars(reply.rating?.rating); // Nếu có rating của reply

                    tbody.innerHTML += `
                    <tr style="background-color: #f5f5f5;">
                        <td colspan="2" style="text-align: right; font-style: italic; color: #888;">
                            Trả lời cho: ${comment.comment_name}
                        </td>
                        <td>
                            <strong>${reply.comment_name}</strong>
                            ${replyRatingStars}<br>
                            <span style="color: blue;">${reply.comment}</span>
                        </td>
                        <td>${reply.comment_name}</td>
                        <td>${reply.comment_date}</td>
                        <td>
                            <input type="button"
                                data-comment_id="${reply.comment_id}"
                                data-comment_status="${reply.comment_status}"
                                class="btn btn-${reply.comment_status == 0 ? 'primary' : 'danger'} btn-xs comment_status_btn"
                                value="${reply.comment_status == 0 ? 'Duyệt' : 'Bỏ duyệt'}">
                        </td>
                        <td>
                            <a href="javascript:void(0);" onclick="deleteComment(${reply.comment_id})" class="active">
                                <i class="fa fa-trash text"></i>
                            </a>
                        </td>
                    </tr>`;
                });
            }
        });
    }





    function renderStars(ratingValue) {
        if (!ratingValue) return '';
        const fullStars = Math.floor(ratingValue);
        const hasHalfStar = ratingValue % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

        let html = '<span style="margin-left: 5px;">';
        for (let i = 0; i < fullStars; i++) html += '<span style="color: gold;">&#9733;</span>';
        if (hasHalfStar) html += '<span style="color: gold;">&#189;</span>'; // half-star (can be replaced with custom icon)
        for (let i = 0; i < emptyStars; i++) html += '<span style="color: #ccc;">&#9733;</span>';
        html += '</span>';
        return html;
    }

    // Sự kiện nút "Trả lời"
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains('btn-reply-comment')) {
            const button = e.target;
            const commentId = button.getAttribute('data-comment_id');
            const replyContent = document.querySelector(`.reply_comment_${commentId}`).value.trim();

            if (!replyContent) {
                alert("Vui lòng nhập nội dung trả lời!");
                return;
            }

            const token = localStorage.getItem('admin_token');
            if (!token) {
                alert("Vui lòng đăng nhập để trả lời bình luận.");
                return;
            }

            // Gửi yêu cầu trả lời bình luận
            fetch('/api/comments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        user_name: 'admin',
                        comment_status: 1,
                        comment: replyContent,
                        parent_comment_id: commentId,
                        product_id: button.getAttribute('data-product_id')
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Trả lời bình luận thành công.");
                        fetchComments(); // Tải lại danh sách bình luận
                    } else {
                        alert("Lỗi khi trả lời bình luận.");
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi gửi trả lời:", error);
                    alert("Đã xảy ra lỗi khi gửi trả lời.");
                });
        }
    });


    function renderPagination() {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        const totalPages = Math.ceil(allComments.length / commentsPerPage);

        for (let i = 1; i <= totalPages; i++) {
            const link = document.createElement("a");
            link.href = "javascript:void(0)";
            link.classList.add("page-link");
            if (i === currentPage) link.classList.add("active");
            link.textContent = i;

            link.addEventListener("click", function() {
                currentPage = i;
                renderComments();
                renderPagination();
            });

            pagination.appendChild(link);
        }
    }

    document.addEventListener("click", function(e) {
        if (e.target.classList.contains('comment_status_btn')) {
            const button = e.target;
            const commentId = button.dataset.comment_id;

            fetch(`/api/comments/status/${commentId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token')
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        button.value = res.status == 1 ? 'Bỏ duyệt' : 'Duyệt';
                        button.classList.remove('btn-primary', 'btn-danger');
                        button.classList.add(res.status == 1 ? 'btn-danger' : 'btn-primary');
                        showNotification("Cập nhật thành công", "green");
                    } else {
                        alert("Không thể cập nhật trạng thái!");
                    }
                })
                .catch(error => {
                    console.error('Lỗi cập nhật trạng thái:', error);
                });
        }
    });

    async function deleteComment(commentId) {
        const token = localStorage.getItem('admin_token');
        if (!token) {
            showNotification("Không đủ thẩm quyền, vui lòng đăng nhập", "red");
            window.location.href = '/admin-login';
            return;
        }

        const confirmDelete = confirm("Bạn chắc chắn muốn xóa bình luận này?");
        if (!confirmDelete) return;

        try {
            const res = await fetch(`/api/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (res.ok) {
                showNotification("Xóa comment thành công", "green");
                fetchComments();
            } else {
                alert(data.message || "Xóa thất bại!");
            }
        } catch (error) {
            console.error("Lỗi khi xóa bình luận:", error);
            alert("Đã xảy ra lỗi khi xóa bình luận.");
        }
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
</script>


<style>
    #pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .page-link {
        margin: 5px;
        padding: 10px 15px;
        text-decoration: none;
        color: #007bff;
        border: 1px solid #007bff;
        border-radius: 5px;
    }

    .page-link:hover {
        background-color: #007bff;
        color: white;
    }

    .page-link.active {
        background-color: #007bff;
        color: white;
        border: 1px solid #0056b3;
    }
</style>

@endsection