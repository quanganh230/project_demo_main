<?php
require_once 'db_connect.php';
require_once 'includes/functions_filter.php';
include 'includes/header.php';

// Get parameters
$records_per_page = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : 1; // Default to category 2 (Balo)
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : '';
$color = isset($_GET['color']) && is_numeric($_GET['color']) ? (int)$_GET['color'] : '';
$size = isset($_GET['size']) && is_numeric($_GET['size']) ? (int)$_GET['size'] : '';

// Fetch data
$data = getProducts($conn, $records_per_page, $page, $search, $category, $min_price, $max_price, $color, $size);
$products = $data['products'];
$total_pages = $data['total_pages'];

?>



<div class="display_products">
    <div class="w-75 mx-auto">
        <!-- Phần Trang Chủ / Vali căn trái -->
        <div class="d-flex justify-content-start mb-2">
            <p class="mb-0 text-dark">
                <a href="index.php" class="link text-dark text-decoration-none">Trang Chủ</a> / Vali
            </p>
        </div>

        <!-- Custom Nav chứa banner -->
        <div class="custom-nav bg-secondary bg-light h-auto border-1 border-secondary-subtle rounded">
            <a href="">
                <img src="image/index/banner3.jpg" alt="" class="img-fluid w-100">
            </a>

            <?php include 'includes/filter.php' ?>

            <p class="fw-bold fs-4 mt-3 mb-3">Vali kéo chính hãng</p>
        </div>



    </div>
    <div class="w-75 mx-auto"> <!-- Add this wrapper to match the custom-nav width and centering -->
        <div class="row">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <a href="detail_product.php?id=<?php echo $product['product_id']; ?>">
                        <div class="card product-card w-63 h-auto">
                            <p class="card-text fw-bold small d-flex align-items-center gap-1">
                                <?php echo number_format($product['average_rating'] ?? 0); ?><i class="bi bi-star-fill"></i>
                            </p>
                            <img class="w-auto h-auto"
                                src="<?php echo htmlspecialchars($product['product_image'] ?: 'images/index/box-01.png'); ?>"
                                class="card-img-top"
                                alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <div class="card-body ">
                                <div class="color-options d-flex justify-content-center align-items-center ">
                                    <?php if (!empty($product['colour_hex_code'])): ?>
                                        <?php
                                        $colors = explode(',', $product['colour_hex_code']);
                                        foreach ($colors as $color):
                                        ?>
                                            <div class="color-circle"
                                                style="background-color: <?php echo htmlspecialchars($color); ?>; 
                                                            width: 20px; 
                                                            height: 20px; 
                                                            border-radius: 50%; 
                                                            margin-right: 10px;">
                                            </div>
                                        <?php endforeach; ?>

                                    <?php endif; ?>
                                </div>
                                <h5 class="card-title fw-bold d-flex justify-content-center align-items-center"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                <p class="card-text d-flex justify-content-center align-items-center fw-bold">
                                    <?php echo number_format($product['product_price'], 0, '', '.'); ?>₫
                                </p>
                            </div>
                        </div>
                    </a>

                </div>
            <?php endwhile; ?>

            <nav aria-label="Page navigation" class="mb-3">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                            <a class="page-link bg-danger border-0" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>&color=<?php echo urlencode($color); ?>&size=<?php echo urlencode($size); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>

            <div class="description w-75 mx-auto mb-4">
                <p>Vali kéo là vật dụng không thể thiếu cho những chuyến đi, được làm từ nhiều chất liệu phổ biến như nhựa, vải, nhôm… Vali kéo du lịch có nhiều kích thước khác nhau tuỳ vào mục đích sử dụng cho những chuyến đi ngắn hay dài ngày. Bạn cần quan tâm đến chất liệu, kích thước, màu sắc và giá cả khi lựa chọn một chiếc vali kéo du lịch cho riêng mình.</p>
                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">1</span>
                    Các loại vali kéo thông dụng hiện nay
                </h1>
                <p class="font-extrabold text-[14px] mb-2">1.1 Vali nhựa</p>
                <p class="text-[14px] mb-2">
                    Vali kéo nhựa được số đông lựa chọn vì tính thời trang và bảo mật cao. Tuỳ vào từng chất liệu nhựa mà những chiếc vali có khả năng chống chọi với va đập khác nhau.
                </p>
                <p class="font-extrabold text-[14px] mb-2">1.2 Vali vải</p>
                <p class="text-[14px] mb-2">
                    Với những chiếc vali kéo du lịch bằng vải, bạn có thể tận dụng tối đa khả năng chứa đồ khi cần thiết vì form sản phẩm có thể phồng lên được.
                </p>
                <p class="font-extrabold text-[14px] mb-2">1.3 Vali nhôm</p>
                <p class="text-[14px] mb-4">
                    Vali kéo nhôm bao gồm những loại vali thân nhựa có đường viền được bao phủ bởi khung hợp kim nhôm cứng cáp hoặc vali bằng nhôm nguyên khối.
                </p>



                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">2</span>
                    Kích thước/ size vali kéo phổ biến

                </h1>
                <p class=" text-[14px] mb-2">- Vali size S: vali size 20 - loại vali có thể xách tay lên máy bay được</p>
                <p class=" text-[14px] mb-2">- Vali size M: vali size 24 - loại vali ký gửi</p>
                <p class=" text-[14px] mb-2">- Vali size L: vali size 28 - loại vali ký gửi</p>

                <img src="image/index/size_vali.jpg" alt="">


                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">3</span>
                    Chính sách bảo hành tại BROWN LUGGAGE
                </h1>
                <p class=" text-[14px] mb-2">BROWN LUGGAGE là chuỗi bán lẻ hành lý với hơn 25+ cửa hàng BROWN LUGGAGE còn được biết đến là thương hiệu bán VALI được yêu thích nhất tại thành phố Hà Nội.</p>
                <div class="justify-items-center mb-3">
                    <img src="image/index/reason_choose.jpg" alt="">
                    <p class="fst-italic m-3">Chính sách bảo hành, đổi trả và chính sách mua hàng tại BROWN LUGGAGE</p>
                </div>

                <p>Khi mua sản phẩm vali kéo du lịch tại MIA.vn, khách hàng được hưởng chính sách bảo hành miễn phí trọn đời, giao hàng toàn quốc. Hệ thống cam kết bán hàng chính hãng, nếu phát hiện hàng giả, khách hàng được hoàn tiền 200%.</p>
            </div>
        </div>

    </div>


    <?php

    mysqli_close($conn);
    include 'includes/footer.php';
    ?>