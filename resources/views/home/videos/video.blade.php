@extends('home.home_layout')
@section('content')

<section id="video" class="container py-4" style="width:100%">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" id="breadcrumbContainer" style="background: none;">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/videos">Videos</a></li>
            <li class="breadcrumb-item active" aria-current="page" id="currentVideoTitle"></li>
        </ol>
    </nav>

    <div class="video-detail">
        <div class="video-player" id="videoPlayer">
            <iframe src="https://www.youtube.com/embed/" frameborder="0" allowfullscreen></iframe>
        </div>
        <h1 id="videoTitle" class="video-title-detail"></h1>
        <div id="videoDesc" class="video-description"></div>
    </div>
    <br>
</section>
<script>
    const videoSlug = `{{$video_slug}}`;
    const videoTitleSection = document.getElementById("videoTitle");
    const videoDescSection = document.getElementById("videoDesc");
    const videoPlayerSection = document.getElementById("videoPlayer");

    function extractYoutubeId(link) {
        try {
            const url = new URL(link);
            return url.searchParams.get("v") || url.pathname.split("/").pop();
        } catch (error) {
            return "";
        }
    }

    function fetchVideo() {
        fetch(`/api/videos/${videoSlug}`)
            .then(res => res.json())
            .then(data => {
                const videoLink = extractYoutubeId(data.data.video_link);
                videoTitleSection.innerHTML = data.data.video_title;
                videoDescSection.innerHTML = data.data.video_desc;
                videoPlayerSection.innerHTML = `<iframe width="100%" height="230" src="https://www.youtube.com/embed/${videoLink}" frameborder="0" allowfullscreen></iframe>`;
                document.getElementById("currentVideoTitle").textContent = data.data.video_title
            })
    }
    document.addEventListener("DOMContentLoaded", function() {
        fetchVideo();
    });
</script>
<style>
    .video-detail {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .video-title-detail {
        font-size: 28px;
        color: #333;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .video-description {
        font-size: 16px;
        color: #555;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: "/";
    }

    .video-player {
        aspect-ratio: 16 / 9;
        width: 100%;
    }

    .video-player iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
</style>

@endsection