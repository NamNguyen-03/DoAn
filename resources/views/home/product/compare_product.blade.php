@extends('home.home_layout')
@section('content')
<div class="container py-4">
    <h2>So sánh sản phẩm</h2>
    <div id="compare-container">Đang tải dữ liệu...
        <!-- <div class="row text-center">
            <div class="col-md-4">
                <div class="product">
                    <h5>Product 1</h5>
                    <img src="/uploads/product/product1.jpg" alt="Product 1" width="100">
                    <p>2,000,000 đ</p>
                    <p>Product 1 description</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product">
                    <h5>Product 2</h5>
                    <img src="/uploads/product/product2.jpg" alt="Product 2" width="100">
                    <p>1,500,000 đ</p>
                    <p>Product 2 description</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product">
                    <h5>Product 3</h5>
                    <img src="/uploads/product/product3.jpg" alt="Product 3" width="100">
                    <p>1,800,000 đ</p>
                    <p>Product 3 description</p>
                </div>
            </div>
        </div> -->

    </div>
</div>
<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        const userId = localStorage.getItem("user_id");
        const container = document.getElementById("compare-container");

        const compareKey = `compare_${userId}`;
        const compareData = sessionStorage.getItem(compareKey);

        if (!compareData) {
            container.innerHTML = "<p>Chưa có sản phẩm nào được chọn để so sánh.</p>";
            return;
        }

        const compareList = JSON.parse(compareData);
        if (compareList.length === 0) {
            container.innerHTML = "<p>Chưa có sản phẩm nào để so sánh.</p>";
            return;
        }

        const ids = compareList.map(item => item.product_id);

        fetch('/api/get-products?' + ids.map(id => 'ids[]=' + id).join('&'))
            .then(res => res.json())
            .then(function(productDetails) {
                if (!Array.isArray(productDetails)) {
                    console.error("API không trả về mảng:", productDetails);
                    container.innerHTML = "<p>Dữ liệu trả về không hợp lệ.</p>";
                    return;
                }

                const row = document.createElement("div");
                row.className = "row text-center"; // Tạo hàng Bootstrap

                // Đảm bảo chia đều cột theo số lượng sản phẩm
                const colSize = 12 / productDetails.length;

                // Tạo cột cho mỗi sản phẩm
                productDetails.forEach(product => {
                    const productCol = document.createElement("div");
                    productCol.className = `col-md-${colSize}`; // Bootstrap chia cột đều theo số lượng sản phẩm

                    const productContent = `
                        <div class="product">
                            <h5>${product.product_name}</h5>
                            <img src="/uploads/product/${product.product_image}" alt="${product.product_name}" width="100">
                            <p>${new Intl.NumberFormat('vi-VN').format(product.product_price)} đ</p>
                            <p>${product.product_desc || "Không có mô tả"}</p>
                        </div>
                    `;
                    productCol.innerHTML = productContent;
                    row.appendChild(productCol);
                });

                container.innerHTML = ""; // Xóa dữ liệu cũ
                container.appendChild(row); // Thêm hàng mới vào container
            })
            .catch(function(error) {
                container.innerHTML = "<p>Không thể tải dữ liệu sản phẩm.</p>";
                console.error(error);
            });
    });
</script> -->
<script>
    const userId = localStorage.getItem("user_id") || sessionStorage.getItem('user_id');

    document.addEventListener("DOMContentLoaded", function() {
        fetchCompare();
    });

    function fetchCompare() {
        const container = document.getElementById("compare-container");
        const compareKey = `compare_${userId}`;
        const compareData = sessionStorage.getItem(compareKey);

        if (!compareData) {
            container.innerHTML = "<p>Chưa có sản phẩm nào được chọn để so sánh.</p>";
            return;
        }

        const compareList = JSON.parse(compareData);
        if (compareList.length === 0) {
            container.innerHTML = "<p>Chưa có sản phẩm nào để so sánh.</p>";
            return;
        }

        const ids = compareList.map(item => item.product_id);

        fetch('/api/get-products?' + ids.map(id => 'ids[]=' + id).join('&'))
            .then(res => res.json())
            .then(function(productDetails) {
                if (!Array.isArray(productDetails)) {
                    container.innerHTML = "<p>Dữ liệu trả về không hợp lệ.</p>";
                    return;
                }

                const colCount = productDetails.length;

                const table = document.createElement("table");
                table.className = "table table-bordered text-center";
                table.style.tableLayout = "fixed";
                table.style.width = "100%";

                const headers = ["Tên sản phẩm", "Hình ảnh", "Giá", "Mô tả", "Nội dung"];

                headers.forEach(function(header) {
                    const row = document.createElement("tr");

                    const th = document.createElement("th");
                    th.textContent = header;
                    th.style.background = "#f8f9fa";
                    th.style.verticalAlign = "middle";
                    th.style.width = "15%";
                    th.style.textAlign = "center";
                    row.appendChild(th);

                    productDetails.forEach(function(product) {
                        const td = document.createElement("td");
                        td.style.verticalAlign = "middle";
                        td.style.border = "1px solid #dee2e6";
                        td.style.whiteSpace = "normal"; // Cho xuống dòng nếu dài
                        td.style.wordWrap = "break-word"; // Tự ngắt từ khi dài
                        td.style.padding = "10px"; // Thêm khoảng cách nếu cần
                        td.style.width = `${(85 / colCount).toFixed(2)}%`;

                        if (header === "Tên sản phẩm") {
                            td.textContent = product.product_name;
                        } else if (header === "Hình ảnh") {
                            const img = document.createElement("img");
                            img.src = "/uploads/product/" + product.product_image;
                            img.alt = product.product_name;
                            img.width = 100;
                            td.appendChild(img);
                        } else if (header === "Giá") {
                            td.textContent = new Intl.NumberFormat('vi-VN').format(product.product_price) + " đ";
                        } else if (header === "Mô tả") {
                            td.innerHTML = product.product_desc || "Không có mô tả";
                        } else if (header === "Nội dung") {
                            td.innerHTML = product.product_content || "Không có nội dung";
                        }

                        row.appendChild(td);
                    });

                    table.appendChild(row);
                });

                container.innerHTML = "";
                container.appendChild(table);
            })


            .catch(function(error) {
                container.innerHTML = "<p>Không thể tải dữ liệu sản phẩm.</p>";
                console.error(error);
            });
    }
</script>

@endsection