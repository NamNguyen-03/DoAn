@extends('home.home_layout')
@section('content')
<div class="product-details">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background: none;">

        </ol>
    </nav>

    <div class="col-sm-6">
        <ul id="imageGallery" class="cS-hidden" style="padding-top: 5px;">
            <!-- Ảnh sẽ được load ở đây -->
        </ul>
    </div>

    <div class="col-sm-6">
        <div class="product-information">
            <img src="" class="newarrival" alt="" />
            <h2 id="product_name"></h2>
            <div id="rating_star"></div>
            <form method="post">
                <span>
                    <input type="hidden" value="" id="cart_product_id" class="cart_product_id_">
                    <input type="hidden" value="" id="cart_product_name" class="cart_product_name_">
                    <input type="hidden" value="" id="cart_product_image" class="cart_product_image_">
                    <input type="hidden" value="" id="cart_product_quantity" class="cart_product_quantity_">
                    <input type="hidden" value="" id="cart_product_price" class="cart_product_price_">
                    <span id="product_price" width="100%"></span>
                    <br>
                    <br>
                    <br>

                    <label>Số lượng:</label>
                    <input name="qty" type="number" min="1" value="1" class="cart_product_qty_" />
                    <button type="button" data-id_product="" style="margin-top: 18px;" name="add-to-cart" class="btn btn-fefault add-to-cart">
                        <i class="fa fa-shopping-cart"></i>
                        Thêm vào giỏ
                    </button>
                </span>
            </form>
            <p><b>Trạng thái:</b> <span class="product-status">Còn hàng</span></p>
            <p><b>Tình trạng:</b> <span class="product-condition">New</span></p>
            <p><b>Thương hiệu:</b> <span class="product-brand"></span></p>
            <p><b>Danh mục:</b> <span class="product-category"></span></p>
            <p><b>Số lượng trong kho:</b> <span class="product-quantity"></span></p>
            <fieldset>
                <legend>Tags</legend>
                <div class="tag-wrapper">
                    <i class="fa fa-tag"></i>
                    <div class="tag-list"></div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="col-sm-7">
        <div id="product-info-container"></div>
    </div>

</div>

<div class="category-tab shop-details-tab">
    <div class="col-sm-12">
        <ul class="nav nav-tabs">
            <li><a href="#desc" data-toggle="tab">Mô tả sản phẩm</a></li>
            <li><a href="#details" data-toggle="tab">Chi tiết sản phẩm</a></li>
            <li class="active"><a href="#reviews" data-toggle="tab">Đánh giá</a></li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade" id="desc">
            <p id="product_desc"></p>
        </div>
        <div class="tab-pane fade" id="details">
            <p id="product_content"></p>
        </div>
        <div class="tab-pane fade active in" id="reviews">
            <div class="col-sm-18">
                <p><b>Đánh giá</b></p>
                <div class="rating-wrapper" style="display: flex; align-items: center; gap: 8px;">
                    <ul style="width:23%" class="list-inline rating-stars" title="Average Rating" data-product-id=""></ul>
                    <span id="average-rating" class="font-weight-bold" style="padding-bottom:13px;font-size:20px;color:gold">4.6</span>
                </div>

                <div class="comment_section"></div>
                <form action="">
                    <input type="hidden" name="comment_product_id" class="comment_product_id" value="">
                    <div id="comment_show"></div>
                </form>
                <br>
                <p><b>Viết đánh giá của bạn</b></p>
                <ul class="list-inline user-rating" style="padding: 0; margin: 0;">
                    <li class="rating-star" data-value="1">&#9733;</li>
                    <li class="rating-star" data-value="2">&#9733;</li>
                    <li class="rating-star" data-value="3">&#9733;</li>
                    <li class="rating-star" data-value="4">&#9733;</li>
                    <li class="rating-star" data-value="5">&#9733;</li>
                </ul>
                <input type="hidden" name="user_rating" id="user_rating" value="">
                <style>
                    .rating-star {
                        font-size: 30px;
                        color: #ccc;
                        cursor: pointer;
                        display: inline-block;
                    }

                    .rating-star.selected,
                    .rating-star.hovered {
                        color: gold;
                    }
                </style>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {

                    });
                </script>

                <div id="notify_comment"></div>
                <form action="#">
                    <textarea style="width:100%" name="comment" class="comment_content" placeholder="Nội dung bình luận"></textarea>
                    <button type="button" class="btn btn-default pull-right send_comment">Gửi bình luận</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="recommended_items">
    <h2 class="title text-center">Sản phẩm liên quan</h2>
    <div id="recommended-item-carousel" class="carousel slide" data-ride="carousel">

        <div class="carousel-inner" id="related-products-container">
        </div>
        <a class="left recommended-item-control" href="#recommended-item-carousel" data-slide="prev">
            <i class="fa fa-angle-left"></i>
        </a>
        <a class="right recommended-item-control" href="#recommended-item-carousel" data-slide="next">
            <i class="fa fa-angle-right"></i>
        </a>
    </div>
</div>

<script>
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');
    const userName = localStorage.getItem('user_name') || sessionStorage.getItem('user_name');
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    const productSlug = `{{$product_slug}}`
    document.addEventListener('DOMContentLoaded', function() {
        let productId = null;

        if (!productSlug) {
            return showAlert('Lỗi', 'Không tìm thấy Slug sản phẩm!', 'error', 'red');
        }
        const tagsContainer = document.querySelector('.tag-list');
        const breadcrumbContainer = document.querySelector('.breadcrumb');
        const relatedContainer = document.getElementById('related-products-container');
        const commentSection = document.querySelector('.comment_section');
        const galleryList = $('#imageGallery');
        // Hàm để fetch thông tin chi tiết sản phẩm
        function fetchProductDetails(productSlug) {
            fetch(`/api/products/${productSlug}`)
                .then(res => res.json())
                .then(({
                    success,
                    data: product
                }) => {
                    if (!success || !product) {
                        return showAlert('Lỗi', 'Không tìm thấy sản phẩm!', 'error', 'red');
                    }
                    productId = product.product_id;
                    // ===== Gán dữ liệu sản phẩm vào giao diện =====
                    document.getElementById('product_name').textContent = product.product_name;
                    document.getElementById('product_price').textContent = Number(product.product_price).toLocaleString('vi-VN') + 'đ';
                    document.getElementById('product_desc').innerHTML = product.product_desc || 'Không có mô tả.';
                    document.getElementById('product_content').innerHTML = product.product_content || 'Không có chi tiết sản phẩm.';
                    document.getElementById('cart_product_id').value = product.product_id;
                    document.getElementById('cart_product_name').value = product.product_name;
                    document.getElementById('cart_product_price').value = product.product_price;
                    document.getElementById('cart_product_quantity').value = product.product_quantity;
                    document.querySelector('.cart_product_image_').value = '';
                    document.querySelector('.product-quantity').textContent = product.product_quantity;
                    document.querySelector('.product-brand').textContent = product.brand?.brand_name || 'Không xác định';
                    document.querySelector('.product-category').textContent = product.category?.category_name || 'Không xác định';
                    document.querySelector('.product-status').textContent = product.product_quantity > 0 ? 'Còn hàng' : 'Hết hàng';

                    // ===== Cập nhật breadcrumb =====
                    breadcrumbContainer.innerHTML = `
                    <li><a href="/">Trang chủ</a></li>
                    <li><a href="/category/${product.category?.category_slug || '#'}">${product.category?.category_name || 'Danh mục'}</a></li>
                    <li class="active">${product.product_name.length > 60 ? product.product_name.substring(0, 60) + "..." : product.product_name}</li>
                `;

                    // ===== Tags sản phẩm =====
                    tagsContainer.innerHTML = product.product_tags ? product.product_tags.split(',').map(tag => `<a href="/tag/${tag.trim()}" class="tag_style">${tag.trim()}</a>`).join('') : '';

                    // ===== Lấy gallery ảnh sản phẩm =====
                    loadGallery(product.product_id);

                    // ===== Sản phẩm liên quan =====
                    loadRelatedProducts(product.category_id, product.product_id);

                    // ===== Load bình luận ===== 
                    loadComments(product.product_id);

                    // ===== Cập nhật rating sao =====
                    updateRatingStars(product.average_rating);
                })
                .catch(err => showAlert('Lỗi', 'Lỗi khi load sản phẩm chi tiết: ' + err.message, 'error', 'red'));
        }


        fetchProductDetails(productSlug);


        //   Load gallery ảnh sản phẩm 
        function loadGallery(productId) {
            fetch(`/api/galleries/${productId}`)
                .then(res => res.json())
                .then(galleryData => {
                    if (galleryData.success && Array.isArray(galleryData.data)) {
                        const images = galleryData.data;
                        galleryList.empty();

                        images.forEach((img, index) => {
                            const imgTag = `
                        <li data-thumb="/uploads/gallery/${img.gallery_image}" data-src="/uploads/gallery/${img.gallery_image}">
                            <img width="100%" alt="${img.gallery_name || ''}" title="${img.gallery_name || ''}" src="/uploads/gallery/${img.gallery_image}" />
                        </li>
                    `;
                            galleryList.append(imgTag);
                            if (index === 0) document.querySelector('.cart_product_image_').value = img.gallery_image;
                        });

                        galleryList.lightSlider({
                            gallery: true,
                            item: 1,
                            loop: true,
                            thumbItem: 4,
                            slideMargin: 0,
                            enableDrag: true,
                            currentPagerPosition: 'left',
                            onSliderLoad: function(el) {
                                $('#imageGallery').removeClass('cS-hidden');
                                el.lightGallery({
                                    selector: '#imageGallery .lslide'
                                });
                            }
                        });
                    }
                })
                .catch(err => showAlert('Lỗi', 'Lỗi khi load ảnh sản phẩm: ' + err.message, 'error', 'red'));
        }

        // Load sản phẩm liên quan 
        function loadRelatedProducts(categoryId, currentProductId) {
            fetch(`/api/products/related/${categoryId}`)
                .then(res => res.json())
                .then(relatedData => {
                    if (relatedData.success && Array.isArray(relatedData.data)) {
                        const relatedProducts = relatedData.data.filter(p => p.product_id !== currentProductId);
                        relatedContainer.innerHTML = '';

                        // Chia sản phẩm thành từng chunk 3 sản phẩm
                        const chunkSize = 3;
                        for (let i = 0; i < relatedProducts.length; i += chunkSize) {
                            const chunk = relatedProducts.slice(i, i + chunkSize);
                            const itemDiv = document.createElement('div');
                            itemDiv.className = 'item' + (i === 0 ? ' active' : '');

                            chunk.forEach(prod => {
                                const relatedHTML = `
                                                <div class="col-sm-4">
                                                    <div class="product-image-wrapper">
                                                        <div class="single-products">
                                                            <div class="productinfo text-center">
                                                                <a href="/product-details/${prod.product_slug}"><img src="/uploads/product/${prod.product_image}" alt="${prod.product_name}" style="height:200px; object-fit:cover;"></a>
                                                                <h2>${Number(prod.product_price).toLocaleString('vi-VN')}đ</h2>
                                                                <p><a href="/product-details/${prod.product_slug}">${prod.product_name.length > 30 ? prod.product_name.slice(0, 30) + '...' : prod.product_name}</a></p>
                                                                <a href="#" class="btn btn-default add-to-cart-related" data-id="${prod.product_id}" data-name="${prod.product_name}" data-image="${prod.product_image}" data-price="${prod.product_price}" data-product_quantity="${prod.product_quantity}">
                                                                    <i class="fa fa-shopping-cart"></i> Thêm giỏ hàng
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                itemDiv.innerHTML += relatedHTML;
                            });

                            relatedContainer.appendChild(itemDiv);
                        }
                    }
                })
                .catch(err => showAlert('Lỗi', 'Lỗi khi load sản phẩm liên quan: ' + err.message, 'error', 'red'));
        }

        //6. Load bình luận
        function loadComments(productId) {
            fetch(`/api/comments/product/${productId}`)
                .then(res => res.json())
                .then(data => {
                    commentSection.innerHTML = data.success && data.data.length ?
                        data.data.map(renderCommentItem).join('') :
                        '<p style="color: #888;">Chưa có bình luận nào.</p>';
                })
                .catch(error => {
                    console.error("Lỗi khi load bình luận:", error);
                    commentSection.innerHTML = '<p style="color:red;">Không thể tải bình luận.</p>';
                });
        }

        function renderCommentStars(ratingValue) {
            if (ratingValue === undefined || ratingValue === null || ratingValue < 0) return '';
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

        function renderCommentItem(comment) {

            // console.log(comment);
            let starsHtml = '';
            // Kiểm tra xem có rating không và nó có giá trị hợp lệ
            if (comment.rating && comment.rating.rating !== undefined && comment.rating.rating >= 0) {
                starsHtml = renderCommentStars(comment.rating.rating); // Gọi hàm renderStars để hiển thị sao
            }

            let html = `
                <div class="comment_item" data-comment-id="${comment.comment_id}" style="background: #f2f2eb; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center;">
                        <img src="{{ asset('frontend/images/avatar1.png') }}" width="50" height="50" style="border-radius: 5px; margin-right: 10px;">
                        <div>
                            <strong style="color: green; font-size: 16px;">${comment.comment_name}</strong>

                            <!-- Đưa phần đánh giá sao vào đây -->
                            <span style="margin-left: 10px;">${starsHtml}</span>

                            <button data-comment-id="${comment.comment_id}" class="reply_button" style="margin-left: 10px; background-color: #4CAF50; color: white; border: none; padding: 5px 10px; border-radius: 5px;">Trả lời</button><br>
                            <small style="color: orange;">${comment.comment_date}</small><br>
                            <span style="font-size:16px;color: #555;">${comment.comment}</span>
                        </div>
                    </div>
                    <!-- Form trả lời sẽ ẩn đi ban đầu -->
                    <div class="reply_form" style="display: none; margin-top: 10px;">
                        <textarea class="reply_content" placeholder="Nhập bình luận của bạn..." style="width: 100%; height: 50px;"></textarea><br>
                        <button class="send_reply_button" style="margin-top: 5px;">Gửi</button>
                    </div>
            `;

            // Nếu có bình luận trả lời thì hiển thị chúng
            if (comment.replies && comment.replies.length > 0) {
                html += `<div class="replies" style="margin-left: 60px; margin-top: 15px; padding-left: 10px; border-left: 2px solid #ccc;">`;

                comment.replies.forEach(reply => {
                    html += `
            <div class="reply_item" style="margin-bottom: 10px;">
                <strong style="color: ${reply.comment_name === 'admin' ? 'red' : 'green'};">${reply.comment_name}</strong>
                <small style="color: orange;">${reply.comment_date}</small><br>
                <span>${reply.comment}</span>
            </div>
            `;
                });

                html += `</div>`;
            }

            html += `</div>`;
            return html;
        }




        // Hàm hiển thị thông báo cảnh báo hoặc thành công
        function showAlert(title, text, type, color) {
            swal({
                title: title,
                text: `<span style='color:${color};'>${text}</span>`,
                type: type,
                html: true
            });
        }

        // Hàm kiểm tra xem người dùng đã đăng nhập hay chưa
        function checkLogin(userId) {
            if (!userId) {
                swal({
                    title: "Cảnh báo",
                    text: "Vui lòng đăng nhập trước!",
                    type: "warning",
                    confirmButtonColor: "red",
                    confirmButtonText: "OK"
                }, function(isConfirm) {
                    if (isConfirm) {
                        window.location.href = '/login';
                    }
                });
                return false;
            }
            return true;
        }


        // Hàm thêm sản phẩm vào giỏ hàng
        function addToCart(product) {
            if (!checkLogin(userId)) return;

            // Kiểm tra số lượng hợp lệ
            if (product.quantity < 1 || product.quantity > product.product_quantity) {
                showAlert("Cảnh báo", "Số lượng không hợp lệ!", "warning", 'red');
                return;
            }

            const cartKey = `cart_${userId}`;
            const currentCart = JSON.parse(localStorage.getItem(cartKey)) || [];
            const existingIndex = currentCart.findIndex(item => item.product_id === product.product_id);

            if (existingIndex !== -1) {
                currentCart[existingIndex].quantity += product.quantity;
                if (currentCart[existingIndex].quantity > product.product_quantity) {
                    currentCart[existingIndex].quantity = product.product_quantity;
                    showAlert("Cảnh báo", "Không đủ sản phẩm trong kho!", "warning", 'red');
                    return;
                }
            } else {
                currentCart.push(product);
            }

            localStorage.setItem(cartKey, JSON.stringify(currentCart));
            swal("Success", "Đã thêm sản phẩm vào giỏ hàng!", "success", 'green');
            document.getElementById('cart-count').textContent = currentCart.length;

        }

        // Hàm gửi bình luận
        function sendComment(commentContent, userName, rating, productId, token, userId) {
            if (!commentContent) {
                showAlert("Cảnh báo", "Vui lòng điền nội dung trước khi bình luận!", "warning", 'red');
                return;
            }
            if (!userId) {
                showAlert("Cảnh báo", "Vui lòng đăng nhập trước khi bình luận!", "warning", 'red');
                return;
            }
            // fetch('/api/comments', {
            //         method: 'POST',
            //         headers: {
            //             "Content-Type": "application/json",
            //             "Authorization": "Bearer " + token,
            //         },
            //         body: JSON.stringify({
            //             rating: rating,
            //             user_name: userName,
            //             comment: commentContent,
            //             product_id: productId,
            //             customer_id: userId,
            //         })
            //     })
            //     .then(res => res.json())
            //     .then(data => {
            //         if (data.success) {
            //             showAlert("Cảm ơn", "Cảm ơn bạn đã bình luận và đánh giá!", "success", "green");
            //             loadComments(productId);
            //             document.querySelector('.comment_content').value = '';
            //         } else {
            //             showAlert("Lỗi", data.message || 'Gửi bình luận thất bại!', "error", "red");
            //         }
            //     })
            //     .catch(error => {
            //         console.error(error);
            //         showAlert("Lỗi", "Có lỗi xảy ra khi gửi bình luận.", "error", "red");
            //     });
        }


        const stars = document.querySelectorAll('.user-rating .rating-star');
        const ratingInput = document.getElementById('user_rating');
        let selectedRating = 0;

        stars.forEach((star, index) => {
            const value = index + 1;

            star.addEventListener('mouseover', () => highlightStars(value));
            star.addEventListener('mouseout', () => highlightStars(selectedRating));
            star.addEventListener('click', () => {
                selectedRating = value;
                ratingInput.value = value;
                highlightStars(value);
            });
        });

        function highlightStars(value) {
            stars.forEach((star, i) => {
                star.classList.toggle('hovered', i < value);
                star.classList.toggle('selected', i < value);
            });
        }

        // Hàm reset rating về 0
        function resetRating() {
            selectedRating = 0;
            ratingInput.value = 0;
            highlightStars(0);
        }


        // Hàm gửi trả lời bình luận
        function sendReply(replyContent, parentCommentId, userName, productId, token, userId) {
            if (!replyContent) {
                showAlert("Cảnh báo", "Vui lòng điền nội dung trước khi trả lời!", "warning", "red");
                return;
            }

            fetch('/api/comments', {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + token,
                    },
                    body: JSON.stringify({
                        user_name: userName,
                        comment: replyContent,
                        product_id: productId,
                        parent_comment_id: parentCommentId,
                        customer_id: userId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    loadComments(productId);
                    if (data.success) {
                        showAlert("Thành công", "Trả lời bình luận thành công!", "success", "green");
                    } else {
                        alert(data.message || 'Gửi trả lời thất bại!');
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Có lỗi xảy ra khi gửi trả lời bình luận.');
                });
        }

        // Sự kiện thêm vào giỏ hàng
        document.querySelector('.add-to-cart').addEventListener('click', function() {
            if (!checkLogin(userId)) return;

            const product = {
                product_id: document.getElementById('cart_product_id').value,
                product_name: document.getElementById('cart_product_name').value,
                product_image: document.getElementById('cart_product_image').value,
                product_price: document.getElementById('cart_product_price').value,
                product_quantity: document.getElementById('cart_product_quantity').value,
                quantity: parseInt(document.querySelector('.cart_product_qty_').value),
            };

            addToCart(product);
        });

        // Sự kiện cho các nút "Thêm vào giỏ hàng" liên quan
        document.body.addEventListener('click', function(e) {
            if (e.target.closest('.add-to-cart-related')) {
                e.preventDefault();
                if (!checkLogin(userId)) return;

                const btn = e.target.closest('.add-to-cart-related');
                const product = {
                    product_id: btn.dataset.id,
                    product_name: btn.dataset.name,
                    product_image: btn.dataset.image,
                    product_quantity: btn.dataset.product_quantity,
                    product_price: btn.dataset.price,
                    quantity: 1,
                };

                addToCart(product);
            }
        });

        // Sự kiện gửi bình luận
        document.querySelector('.send_comment').addEventListener('click', function() {
            const commentContent = document.querySelector('.comment_content').value.trim();
            const rating = document.getElementById('user_rating').value;

            if (checkLogin(userId)) {
                sendComment(commentContent, userName, rating, productId, token, userId);
            }
        });

        // Sự kiện trả lời bình luận
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('reply_button')) {
                const commentItem = e.target.closest('.comment_item');
                const replyForm = commentItem.querySelector('.reply_form');
                replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
            }

            if (e.target && e.target.classList.contains('send_reply_button')) {
                const commentItem = e.target.closest('.comment_item');
                const replyContent = commentItem.querySelector('.reply_content').value.trim();
                const parentCommentId = commentItem.getAttribute('data-comment-id');


                if (checkLogin(userId)) {
                    sendReply(replyContent, parentCommentId, userName, productId, token, userId);
                }
            }
        });

        //hàm hiển thị trung bình sao đánh giá
        function updateRatingStars(avg) {
            console.log(avg);
            const ratingElements = document.querySelectorAll('.rating-stars');
            const ratingText = document.getElementById('average-rating');

            ratingElements.forEach(function(el) {
                // Làm tròn sao
                const decimal = avg % 1;
                let fullStars = Math.floor(avg);
                let hasHalfStar = false;

                if (decimal >= 0.75) {
                    fullStars += 1;
                } else if (decimal >= 0.25) {
                    hasHalfStar = true;
                }

                const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

                let starsHtml = '';

                // Tạo sao đầy
                for (let i = 0; i < fullStars; i++) {
                    starsHtml += `<li style="font-size:30px; color: gold;">&#9733;</li>`;
                }

                // Sao nửa
                if (hasHalfStar) {
                    starsHtml += `<li style="font-size:30px; color: gold; position: relative;">
                    <span style="position: absolute; width: 35%; overflow: hidden; color: gold;">&#9733;</span>
                    <span style="color: #ccc;">&#9733;</span>
                  </li>`;
                }

                // Sao rỗng
                for (let i = 0; i < emptyStars; i++) {
                    starsHtml += `<li style="font-size:30px; color: #ccc;">&#9733;</li>`;
                }

                el.innerHTML = starsHtml;
            });

            // ====== Cập nhật màu của số đánh giá ======
            let color = 'gold';
            if (avg < 1) {
                color = 'red';
            } else if (avg < 3) {
                color = 'darkorange';
            }

            ratingText.textContent = avg.toFixed(1); // giữ định dạng 1 số thập phân
            ratingText.style.color = color;
        }





    });
</script>


<style>
    .tag-wrapper {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .tag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .tag_style {
        background-color: #d9edf7;
        color: #31708f;
        border: 1px solid #bce8f1;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 13px;
        display: inline-block;
        position: relative;
    }



    /* Ẩn dấu phẩy cuối cùng */


    .product-information p {
        margin-bottom: 10px;
        line-height: 0.5;
    }

    .product-information label {
        display: inline-block;
        margin-bottom: 5px;
        font-weight: bold;
    }



    .product-information .btn.add-to-cart {
        margin-top: 10px;
    }



    .product-information #product_price {
        font-size: 24px;
        font-weight: bold;
        color: #e74c3c;
        display: inline-block;
        margin-top: 10px;
    }


    .product-status {
        color: green;
        font-weight: bold;
    }

    .product-information form {
        margin-top: 15px;
    }
</style>





@endsection