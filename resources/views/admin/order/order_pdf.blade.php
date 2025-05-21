<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Phiếu bán hàng - {{ $order->order_code }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            color: #000;
            margin: 20px;
            position: relative;
            min-height: 100%;
            padding-bottom: 100px;
            /* Add space for the signature section */
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .logo {
            width: 120px;
            margin-right: 20px;
        }

        .company-info {
            font-size: 13px;
            line-height: 1.4;
            text-align: right;
        }

        h2 {
            text-align: center;
            margin: 10px 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .info {
            margin-bottom: 15px;
            font-size: 12px;
        }

        .info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 2px;
            text-align: center;
            font-size: 8px;
        }

        .summary {
            font-size: 12px;
            margin-top: 2px;
        }

        .signatures {
            position: absolute;
            bottom: 20px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 10px;
        }

        .signatures div {
            width: 45%;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="header">
        <table style="width: 100%; border: none; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 35%; vertical-align: top;border: none;">
                    <div class="logo">
                        <img src="{{ public_path('frontend/images/techstormlogo.png') }}" alt="Logo" style="width: 150%;margin-left:130px">
                    </div>
                </td>
                <td style="width: 65%; vertical-align: top;border: none;">
                    <div class="company-info">
                        <strong>
                            <h2 style="text-align:right">TechStorm</h2>
                        </strong>
                        Địa chỉ: Số 2, Ngõ 11 Hà Cầu, Hà Đông, Hà Nội<br>
                        Website: TECHSTORM<br>
                        Email: nambup03@gmail.com<br>
                        Hotline: 0355773833<br>
                        Tài khoản: 19037253717019 - Tech - NGUYỄN CÔNG HẢI NAM
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <h2>PHIẾU BÁN HÀNG KIÊM BẢO HÀNH</h2>

    <div class="info">
        <p><strong>Mã đơn hàng:</strong> {{ $order->order_code ?? '---' }}</p>
        <p><strong>Tên khách:</strong> {{ $order->shipping->customer_name }}</p>
        <p><strong>Địa chỉ:</strong> {{ $order->shipping->shipping_address }}</p>
        <p><strong>Số điện thoại:</strong> {{ $order->shipping->shipping_phone }}</p>
        <p><strong>Ngày xuất:</strong> {{ now()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th width="350px">Sản phẩm</th>
                <th>DVT</th>
                <th>SL</th>
                <th>THÀNH TIỀN</th>
                <th>BH</th>
            </tr>
        </thead>
        <tbody>
            @php
            $subtotal=0;
            foreach($order->order_details as $item){
            $subtotal+=$item->product_price *$item->product_quantity;
            }
            $coupon= explode('-',$order->order_coupon);
            $coupon_code = ($coupon[0] === 'null') ? 'Không có' : (isset($coupon[0]) ? $coupon[0] : 'Không có');
            $discount=isset($coupon[1]) ? (float)$coupon[1]:0;
            $tax=($subtotal-$discount)*0.08;
            $shipping_fee=(float)$order->order_ship;
            $total=$subtotal-$discount+$tax+$shipping_fee;
            @endphp
            @foreach ($order->order_details as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align:left;">{{ $item->product_name }}</td>
                <td>c</td>
                <td>{{ $item->product_quantity }}</td>
                <td>{{ number_format($item->product_price * $item->product_quantity, 0, ',', '.') }} đ</td>
                <td>new</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align:right;"><strong>Tạm tính</strong></td>
                <td colspan="2"><strong>{{ number_format($subtotal, 0, ',', '.') }} đ</strong></td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right;font-size:10px">Mã giảm giá: ({{$coupon_code}})</td>
                <td colspan="2">{{ number_format($discount, 0, ',', '.') }} đ</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right;font-size:10px">Thuế(8%)</td>
                <td colspan="2">{{ number_format($tax, 0, ',', '.') }} đ</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right;font-size:10px">Ship</td>
                <td colspan="2">{{ number_format($shipping_fee, 0, ',', '.') }} đ</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right;"><strong>Thành tiền</strong></td>
                <td colspan="2"><strong>{{ number_format($total, 0, ',', '.') }} đ</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <em>Ghi chú</em>
    </div>

    <div class="signatures">
        <table style="width: 100%; border: none; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 50%; vertical-align: top;border:none;">
                    <div class="seller">
                        <strong>Người bán</strong><br><br><br>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top;border:none;">
                    <div class="buyer">
                        <strong>Người mua</strong><br><br><br>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>