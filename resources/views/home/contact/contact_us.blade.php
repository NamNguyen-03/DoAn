@extends('home.home_layout')

@section('content')
<div class="container" style="width:115%; ">
    <div class="row contact-section">
        <!-- Form liên hệ -->

        <div class="col-md-9 contact-form">
            <h2>Liên hệ với chúng tôi</h2>
            <form action="" method="POST" id="contactUsForm">
                @csrf

                <label>Họ và tên</label>
                <input type="text" name="customer_name" class="form-control" placeholder="Họ và tên" required>

                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" required>

                <label>Nội dung tin nhắn</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Lời nhắn" required></textarea>

                <button type="submit" class="btn btn-primary">Gửi tin nhắn</button>
            </form>
        </div>

        <!-- Google Map -->
        <div class="col-md-6 contact-map" style="">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3725.715042319643!2d105.77153667486057!3d20.96395568066921!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313452d6c29ed1e1%3A0x2c65752567674202!2zMjg1IMSQLiBUw7QgSGnhu4d1LCBIw6AgQ-G6p3UsIEjDoCDEkMO0bmcsIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1743086406326!5m2!1svi!2s"
                width="600px" height="400px" allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </div>

    <!-- Thông tin liên hệ -->
    <div class="row contact-info">

    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {

    });
    document.getElementById("contactUsForm").addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        console.log("Dữ liệu gửi đi:");
        for (let pair of formData.entries()) {
            console.log(`${pair[0]}:`, pair[1]);
        }
        fetch(`/api/contact-us`, {
                method: "POST",
                headers: {
                    "Accept": "application/json"
                },
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    swal("Thông báo", "Cảm ơn bạn đã liên hệ!", "success");
                    window.location.href = '/thank-you';
                }
            })
    })
</script>
<style>
    .contact-section {
        display: flex;
        align-items: stretch;
        background: #f8f9fa;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .contact-form {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .contact-form h2 {
        margin-bottom: 20px;
    }

    .contact-form label {
        font-weight: bold;
        margin-top: 10px;
    }

    .contact-form input,
    .contact-form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .contact-form button {
        margin-top: 15px;
        width: 100%;
        padding: 10px;
        background: #007bff;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .contact-form button:hover {
        background: #0056b3;
    }

    .contact-map iframe {
        height: 480px;
        border-radius: 10px;
    }

    .contact-info {
        margin-top: 30px;
        padding: 20px;
        background: #f8f9fa;
        text-align: center;
        border-radius: 10px;
        display: flex;
        justify-content: space-around;
    }

    .contact-info i {
        font-size: 24px;
        color: #007bff;
        margin-bottom: 10px;
    }
</style>
@endsection