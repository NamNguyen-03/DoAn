@extends('home.user_layout')
@section('mini_content')


<section class="orders-section py-5">
    <div class="container" style="width:100%">
        <div class="comments-wrapper" id="commentsWrapper">
            <!-- Comment cards will be injected here -->
        </div>
    </div>
</section>
<script>
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');

    document.addEventListener('DOMContentLoaded', function() {
        if (!userId) {
            swal({
                title: "Cảnh báo",
                text: "<span style='color:red;'>Vui lòng đăng nhập trước!</span>",
                type: "warning",
                html: true
            });
            window.location.href = "/login";
            return;
        }

        fetch(`/api/users/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data.comments)) {
                    renderComments(data.data.comments);
                }
            })
            .catch(error => console.error('Lỗi khi fetch comment:', error));
    });

    function renderComments(comments) {
        const wrapper = document.getElementById('commentsWrapper');
        wrapper.innerHTML = '';

        comments.forEach(comment => {
            const product = comment.product;
            const productSlug = product.product_slug;
            const productLink = `/product-details/${productSlug}`;
            const productImage = product.product_image;
            const productNameFull = product.product_name.length > 35 ?
                product.product_name.substring(0, 35) + "..." :
                product.product_name;

            // ⭐ Xử lý rating
            let stars = '';
            const ratingValue = comment.rating && comment.rating.rating ? Math.round(comment.rating.rating * 2) / 2 : 5;
            for (let i = 1; i <= 5; i++) {
                if (ratingValue >= i) {
                    stars += '<i class="fas fa-star" style="color:gold;"></i>';
                } else if (ratingValue + 0.5 === i) {
                    stars += '<i class="fas fa-star-half-alt" style="color:gold;"></i>';
                } else {
                    stars += '<i class="far fa-star" style="color:gold;"></i>';
                }
            }

            const commentHTML = `
            <div class="comment-box" style="margin-bottom: 20px;">
                <div class="comment-content" style="display: flex; align-items: flex-start; justify-content: space-between;">
                    <div class="comment-left" style="margin-right: 10px;">
                        <img src="/frontend/images/avatar1.png" width="50" height="50" style="border-radius: 5px;">
                    </div>
                    <div class="comment-main" style="flex-grow: 1;">
                        <div class="comment-header" style="display: flex; align-items: center; gap: 10px;">
                            <span class="comment-name" style="font-weight: bold;">${comment.comment_name}</span>
                            <span class="comment-rating">${stars}</span>
                            
                        </div>
                        <div class="comment-date" style="font-size: 0.9em; color: gray;">${comment.comment_date}</div>
                        <div class="comment-text" style="margin-top: 5px;">${comment.comment}</div>
                    </div>
                    <div class="comment-right" style="margin-left: -20px; text-align: left;">
                        <a href="${productLink}">
                            <img src="/uploads/product/${productImage}" alt="${productNameFull}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                        </a>
                        <div style="margin-top: 5px;">
                            <a href="${productLink}" style="color: #007bff; font-size: 0.9em; display: inline-block; text-align: center;">${productNameFull}</a>
                        </div>
                    </div>
                </div>
            </div>`;
            wrapper.innerHTML += commentHTML;
        });
    }
</script>

<style>
    .comment-box {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #fafaf5;
        padding: 12px;
        margin-bottom: 20px;
    }

    .comment-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .comment-left {
        margin-right: 12px;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .comment-main {
        flex: 1;
    }

    .comment-header {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: bold;
        color: #2c3e50;
    }

    .comment-rating {
        color: gold;
    }

    .reply-btn {
        background-color: #28a745;
        color: #fff;
        border: none;
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 12px;
        cursor: pointer;
    }

    .comment-date {
        color: #f39c12;
        font-size: 13px;
        margin-top: 5px;
    }

    .comment-text {
        margin-top: 6px;
        font-size: 14px;
        color: #333;
    }

    .comment-product {
        margin-top: 8px;
        font-size: 13px;
        color: #666;
        font-style: italic;
    }

    .comment-right {
        margin-left: 12px;
    }

    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
    }
</style>

@endsection