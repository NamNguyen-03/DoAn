@extends('home.home_layout')

@section('content')
<section id="post" class="container py-4" style="width:100%">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" id="breadcrumbContainer" style="background: none;"></ol>
    </nav>

    <div id="post_detail" style="text-align:center">
        <h1 id="post_title" class="text-2xl font-bold mb-3"></h1>
        <strong>
            <p id="post_desc" class="mb-4 text-gray-700"></p>
        </strong>
        <div style="text-align:center" class="flex justify-center mb-4">
            <img width="600px" id="post_image" src="" alt="Post Image" class="w-full max-w-md mx-auto rounded shadow">
        </div><br>
        <div id="post_content" class="prose prose-lg"></div>
    </div>

    <hr class="my-6">

    <div id="related_posts">
        <h2 class="text-xl font-semibold mb-3">Bài viết liên quan</h2>
        <div class="row g-3" id="related-posts">
            <!-- Bài viết liên quan sẽ được render ở đây -->
        </div>

    </div>
</section>

<script>
    const postSlug = `{{$post_slug}}`;
    const breadcrumbContainer = document.getElementById('breadcrumbContainer');

    document.addEventListener('DOMContentLoaded', function() {
        fetch(`/api/posts/${postSlug}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const post = data.data;
                    // console.log(post)
                    document.getElementById('post_title').textContent = post.post_title;
                    document.getElementById('post_desc').innerHTML = post.post_desc;
                    document.getElementById('post_image').src = `/uploads/post/${post.post_image}`;
                    document.getElementById('post_image').alt = post.post_title;
                    document.getElementById('post_content').innerHTML = post.post_content;
                    breadcrumbContainer.innerHTML = `
                        <li><a href="/">Trang chủ</a></li>
                        <li><a href="/post-cate/${post.cate_post?.cate_post_slug || '#'}">${post.cate_post?.cate_post_name || 'Danh mục'}</a></li>
                        <li class="active">${post.post_title.length > 60 ? post.post_title.substring(0, 60) + "..." : post.post_title}</li>
                    `;
                    loadRelatedPosts(post.cate_post.cate_post_slug, post.post_slug);
                } else {
                    document.getElementById('post_content').innerHTML = '<p>Không tìm thấy bài viết.</p>';
                }
            })
            .catch(error => {
                console.error("Lỗi khi lấy bài viết:", error);
            });
    });

    function loadRelatedPosts(categorySlug, currentPostSlug) {
        fetch(`/api/postcates/${categorySlug}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const allPosts = data.data.posts || [];
                    const activePosts = allPosts.filter(post => post.post_status == 1 && post.post_slug != currentPostSlug);
                    const randomPosts = activePosts.sort(() => 0.5 - Math.random()).slice(0, 3);
                    const relatedContainer = document.getElementById('related-posts');
                    relatedContainer.innerHTML = '';
                    randomPosts.forEach(post => {
                        relatedContainer.innerHTML += `
                        <div class="col-md-4 mb-3 text-center">
                            <a href="/post/${post.post_slug}" class="d-block mb-2">
                                <img src="/uploads/post/${post.post_image}" 
                                    alt="${post.post_title}"
                                    class="img-fluid rounded related-thumbnail" />
                            
                            <br>
                            <strong><p style="margin-top:5px; " class="fw-semibold mb-0 small">${post.post_title.length > 60 ? post.post_title.substring(0, 60) + "..." : post.post_title}</p></strong>
                            </a>
                        </div>
                    `;
                    });
                }
            })
            .catch(error => {
                console.error("Lỗi khi lấy bài viết liên quan:", error);
            });
    }
</script>
<style>
    .related-thumbnail {
        width: 250px;
        height: 170px;
        object-fit: cover;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .related-thumbnail:hover {
        transform: scale(1.03);
    }

    #related_posts h2 {
        text-align: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    #related-posts .col-md-4 {
        padding: 0 10px;
    }
</style>
@endsection