<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Viewer</title>
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
</head>

<body>

    <div class="email-container">
        <div class="email-subject">Thông báo: {!!$promotion->subject!!}!</div>
        <div class="email-content">
            {!!$promotion->content!!}

            Trân trọng,
            Fulitex Camera
        </div>
    </div>

</body>

</html>