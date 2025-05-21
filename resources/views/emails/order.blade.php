<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đơn hàng #{{ $order->order_code }}</title>
</head>

<body>
    <h2>Đơn hàng #{{ $order->order_code }}</h2>
    <p>Xin chào {{ $order->shipping->customer_name }},</p>
    <p>Cảm ơn bạn đã đặt hàng của cửa hàng.</p>
    <h3>Thông tin giao hàng:</h3>
    <ul>
        <li>Họ tên: {{ $order->shipping->customer_name }}</li>
        <li>Địa chỉ: {{ $order->shipping->shipping_address }}</li>
        <li>SĐT: {{ $order->shipping->shipping_phone }}</li>
        <li>Email: {{ $order->shipping->shipping_email }}</li>
        <li>Ghi chú: {{ $order->shipping->shipping_note }}</li>
    </ul>
    <p> Dưới đây là chi tiết đơn hàng:</p>

    <h3>Sản phẩm:</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Tên</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @php
            $subtotal = 0;
            @endphp
            @foreach ($order->order_details as $item)
            @php
            $lineTotal = $item->product_price * $item->product_quantity;
            $subtotal += $lineTotal;
            @endphp
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->product_quantity }}</td>
                <td>{{ number_format($item->product_price, 0, ',', '.') }}₫</td>
                <td>{{ number_format($lineTotal, 0, ',', '.') }}₫</td>
            </tr>
            @endforeach
        </tbody>

    </table>
    <p><strong>Tạm tính:</strong> {{ number_format($subtotal, 0, ',', '.') }}₫</p>

    @php
    $discount_code=null;
    $discount=0;
    if($order->order_coupon!="null-0"){
    [$discount_code,$discountAmountStr]=explode('-',$order->order_coupon);
    $discount=(int)$discountAmountStr;
    }
    $totalAfterDis=$subtotal-$discount;
    $tax=$totalAfterDis*0.08;
    $total=$totalAfterDis+$tax-$order->order_ship;
    @endphp
    <p>
        <strong>Mã giảm giá:</strong>
        {{ $order->order_coupon != 'null-0' ? $discount_code . ' - Giảm: ' . number_format($discount, 0, ',', '.') . '₫' : 'Không có' }}
    </p>

    <p><strong>Phí vận chuyển:</strong> {{ number_format($order->order_ship, 0, ',', '.') }}₫</p>

    <p><strong>Tổng thanh toán(Đã bao gồm 8% thuế):</strong> {{ number_format($total, 0, ',', '.') }}₫</p>



    <p>Trân trọng,<br>TechStorm chúc bạn một ngày tốt lành</p>
</body>

</html>