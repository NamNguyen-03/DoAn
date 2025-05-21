@extends('home.home_layout')
@section('content')

<div class="features_items">
    <!-- Hiển thị tên danh mục -->
    <h2 class="title text-center" style="padding:1px 0;" id="category-name"></h2>
    <div id="post-list" class="product-image-wrapper" style="border:none"></div>

    <!-- Phân trang -->
    <ul id="pagination" class="pagination pagination-sm m-t-none m-b-none justify-content-center mt-3"></ul>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const postList = document.getElementById('post-list');
        const pagination = document.getElementById('pagination');
        const categoryNameElement = document.getElementById('category-name');



        const cateSlug = `{{$cate_post_slug}}`;

        const postsPerPage = 6;
        let currentPage = 1;
        let allPosts = [];
        let categoryName = "";

        // Hàm cắt bớt văn bản khi vượt quá giới hạn
        function truncateText(text, limit = 100) {
            return text.length > limit ? text.substring(0, limit) + '...' : text;
        }

        // Hàm hiển thị bài viết lên giao diện
        function renderPosts(page) {
            const start = (page - 1) * postsPerPage;
            const end = start + postsPerPage;
            const paginatedPosts = allPosts.slice(start, end);

            postList.innerHTML = '';
            paginatedPosts.forEach(post => {
                postList.innerHTML += `
                <div class="single-products" style="margin:10px 0;padding:2px 0;">
                    <a href="/post/${post.post_slug}">
                        <div class="text-center">
                            <img style="float: left;width:30%;padding:5px;" src="/uploads/post/${post.post_image}" alt="${post.post_title}" />
                            <h4 style="color:black;padding:3px;">${post.post_title}</h4>
                            <p style="font-style: normal; font-weight: normal; text-align: left; padding: 5px;margin: left 5px;">${truncateText(post.post_desc)}</p>
                        </div>
                    </a>
                    <div class="text-right">
                        <a href="/post/${post.post_slug}" class="btn btn-default btn-sm">Xem bài viết</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            `;
            });
        }

        // Hàm hiển thị phân trang
        function renderPagination() {
            const totalPages = Math.ceil(allPosts.length / postsPerPage);
            pagination.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.classList.add('page-item');
                if (i === currentPage) li.classList.add('active');
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentPage = i;
                    renderPosts(currentPage);
                    renderPagination();
                });
                pagination.appendChild(li);
            }
        }

        // Hàm gọi API lấy danh mục và bài viết của danh mục đó
        function fetchCategoryData(slug) {
            fetch(`/api/postcates/${slug}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        handleCategoryData(data.data);
                    } else {
                        categoryNameElement.textContent = "Danh mục không tồn tại";
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi gọi API lấy danh mục:', error);
                    postList.innerHTML = '<p class="text-danger">Không thể tải bài viết.</p>';
                });
        }

        // Hàm xử lý dữ liệu danh mục lấy về
        function handleCategoryData(postcate) {
            categoryName = postcate.cate_post_name;
            categoryNameElement.textContent = categoryName;
            allPosts = postcate.posts;
            renderPosts(currentPage);
            renderPagination();
        }

        // Gọi hàm fetchCategoryData với slug lấy từ URL
        fetchCategoryData(cateSlug);
    });
</script>

@endsection