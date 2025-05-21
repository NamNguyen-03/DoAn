@extends('home.home_layout')
@section('content')

<style>
    .track-order-container {
        max-width: 600px;
        margin: 60px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        text-align: center;
    }

    .track-order-container h1 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
    }

    .track-form input[type="text"] {
        width: 80%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .track-form button {
        padding: 12px 30px;
        font-size: 16px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .track-form button:hover {
        background-color: #0056b3;
    }

    .order-result {
        margin-top: 30px;
        text-align: left;
        color: #444;
    }
</style>

<div class="track-order-container">
    <h1>Tra cứu đơn hàng</h1>
    <form class="track-form" id="trackOrderForm">
        <input type="text" id="orderCode" placeholder="Nhập mã đơn hàng (VD: ORD-123456...-JSDHN...)" required>
        <br>
        <button type="submit">Tra cứu</button>
    </form>

    <div id="orderResult" class="order-result"></div>
</div>

<script>
    document.getElementById('trackOrderForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const orderCode = document.getElementById('orderCode').value.trim();
        const resultDiv = document.getElementById('orderResult');

        if (!orderCode) {
            resultDiv.innerHTML = '<p>Vui lòng nhập mã đơn hàng.</p>';
            return;
        }
        window.location.href = `/order_details/${orderCode}`

    });
</script>

@endsection