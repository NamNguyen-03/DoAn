@extends('home.home_layout')
@section('content')

<div class="features_items">
    <h2 id="videos" class="title text-center" style="padding:1px 0;">Videos</h2>
    <div id="video-list" class="video-grid"></div>
    <br>
    <ul id="video-pagination" class="pagination pagination-sm m-t-none m-b-none justify-content-center mt-3"></ul>
</div>

<style>
    .video-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .video-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .video-card:hover {
        transform: translateY(-5px);
    }

    .video-thumb {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .video-title {
        padding: 15px;
        font-size: 18px;
        font-weight: bold;
        color: #333;
        text-align: center;
    }

    .watch-button {
        display: block;
        margin: 0 auto 15px auto;
        padding: 10px 20px;
        background-color: #0fa6fe;
        color: white;
        border: none;
        border-radius: 5px;
        text-align: center;
        font-size: 14px;
        text-decoration: none;
        transition: background-color 0.3s ease;
        width: fit-content;
    }

    .watch-button:hover {
        background-color: rgb(32, 0, 211);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const videoList = document.getElementById('video-list');
        const pagination = document.getElementById('video-pagination');
        const itemsPerPage = 6;
        let allVideos = [];
        let currentPage = 1;

        function fetchVideos() {
            fetch('/api/videos')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allVideos = data.data;
                        renderVideos(currentPage);
                        renderPagination();
                    } else {
                        videoList.innerHTML = '<p>Không có video nào.</p>';
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải video:', error);
                    videoList.innerHTML = '<p class="text-danger">Lỗi khi tải video.</p>';
                });
        }

        function renderVideos(page) {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginated = allVideos.slice(start, end);

            videoList.innerHTML = paginated.map(video => `
                <div class="video-card">
                    <img src="/uploads/video_thumbs/${video.video_thumb}" alt="${video.video_title}" class="video-thumb">
                    <div class="video-title">${video.video_title}</div>
                    <a href="/video/${video.video_slug}" class="watch-button" >Xem video</a>
                </div>
            `).join('');
        }

        function renderPagination() {
            const totalPages = Math.ceil(allVideos.length / itemsPerPage);
            pagination.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.classList.add('page-item');
                if (i === currentPage) li.classList.add('active');
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentPage = i;
                    renderVideos(currentPage);
                    renderPagination();
                });
                pagination.appendChild(li);
            }
        }

        fetchVideos();
    });
</script>
@endsection