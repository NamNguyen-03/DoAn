@extends('admin.admin_layout')
@section('admin_content')


<div class="email-container">
    <div class="form-group">
        <label for="product_desc">Envelope:</label>
        <textarea rows="5" class="form-control" name="promotion_envelope" id="promotion_envelope" required></textarea>
    </div>
    <div class="email-subject">Thông báo: <div class="form-group">
            <label for="product_desc">Subject:</label>
            <textarea rows="5" class="form-control" name="promotion_subject" id="promotion_subject" required></textarea>
        </div> !</div>
    <div class="email-content">
        <div class="form-group">
            <label for="product_desc">Content:</label>
            <textarea rows="5" class="form-control" name="promotion_content" id="promotion_content" required></textarea>
        </div>

        Trân trọng,
        Fulitex Camera
    </div>
    <button id="confirm_edit_content" class="btn btn-primary btn-md">Cập nhật</button>
</div>



<script>
    const adminToken = localStorage.getItem('admin_token');
    const contentId = `{{$id}}`;
    document.addEventListener("DOMContentLoaded", function() {
        fetchContent();
    });

    function fetchContent() {
        fetch(`/api/promotions-content/${contentId}`)
            .then(res => res.json())
            .then(data => {
                if (CKEDITOR.instances['promotion_subject']) {
                    CKEDITOR.instances['promotion_subject'].setData(data.data.subject || "");
                }
                if (CKEDITOR.instances['promotion_content']) {
                    CKEDITOR.instances['promotion_content'].setData(data.data.content || "");
                }
                document.getElementById('promotion_envelope').value = data.data.envelope
            })
            .catch(error => {
                console.error('Lỗi khi tải nội dung email:', error);
                document.getElementById('email-content').textContent = 'Không thể hiển thị nội dung email.';
            });
    }
    document.getElementById('confirm_edit_content').addEventListener('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        let Envelope = document.getElementById('promotion_envelope').value;
        let Subject = document.getElementById('promotion_subject').value;
        let Content = document.getElementById('promotion_content').value;
        fetch(`/api/promotions-content/${contentId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken
                },
                body: JSON.stringify({
                    envelope: Envelope,
                    subject: Subject,
                    content: Content
                })

            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Cập nhật thành công")
                    window.location.href = "/admin/all-promotion-content";
                } else {
                    alert("Lỗi")
                }
            })
            .catch(error => {
                console.error("Lỗi api: " + error);
            })
    })
</script>
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
    CKEDITOR.replace('promotion_subject', {
        filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        filebrowserUploadMethod: 'form'
    });
    CKEDITOR.replace('promotion_content', {
        filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        filebrowserUploadMethod: 'form'
    });
</script>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f6fa;
        margin: 0;
        padding: 0;
    }

    .email-container {
        max-width: 700px;
        margin: 30px auto;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .email-subject {
        font-size: 22px;
        font-weight: bold;
        color: #2f3640;
        margin-bottom: 20px;
        border-bottom: 1px solid #dcdde1;
        padding-bottom: 10px;
    }

    .email-content {
        font-size: 16px;
        color: #353b48;
        line-height: 1.6;
        white-space: pre-wrap;
        /* giữ định dạng xuống dòng */
    }

    @media (max-width: 768px) {
        .email-container {
            margin: 15px;
            padding: 20px;
        }

        .email-subject {
            font-size: 20px;
        }

        .email-content {
            font-size: 15px;
        }
    }
</style>
@endsection