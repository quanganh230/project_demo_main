<?php
require_once 'db_connect.php';
require_once 'includes/functions_filter.php';
include 'includes/header.php';

// Get parameters
$records_per_page = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : 3; // Default to category 2 (Balo)
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
                <a href="index.php" class="link text-dark text-decoration-none">Trang Chủ</a> / Túi Xách
            </p>
        </div>

        <!-- Custom Nav chứa banner -->
        <div class="custom-nav bg-secondary bg-light h-auto border-1 border-secondary-subtle rounded">
            <a href="">
                <img src="image/index/banner_bag.jpg" alt="" class="img-fluid w-100">
            </a>

            <?php include 'includes/filter.php' ?>

            <p class="fw-bold fs-4 mt-3 mb-3">Túi xách cao cấp chính hãng</p>
        </div>

        <!-- Lọc sản phẩm  -->



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
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>&color=<?php echo urlencode($color); ?>&size=<?php echo urlencode($size); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>

            <div class="description w-75 mx-auto mb-4">
                <p class="mb-3">Túi xách là item thông dụng trong những năm gần đây. Một chiếc túi có thiết kế độc đáo cùng phối màu thời thượng sẽ là điểm nhấn nổi bật cho tổng thể trang phục của bạn.Thời kỳ đầu, túi xách đơn thuần là vật dụng đựng những món đồ lặt vặt. Khi các quan niệm cũ về thời trang sụp đổ, đó là lúc chứng kiến túi xách có sự thay đổi ngoạn mục: tươi trẻ và phóng khoáng hơn.</p>
                <p>Túi xách có sự thay đổi đáng kể về hình dáng, thiết kế, màu sắc và chất liệu. Điều này giúp mọi người dễ dàng tìm ra lựa chọn phù hợp với sở thích và nhu cầu cá nhân. Với giá thành đa dạng từ rẻ đến cao cấp, việc sở hữu túi xách giờ đây đã trở nên dễ dàng hơn rất nhiều.
                </p>
                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">1</span>
                    Các công dụng của túi xách
                </h1>
                <p class="font-extrabold text-[14px] mb-2">Túi xách sở hữu những công dụng lý tưởng để khẳng định tầm quan trọng của mình. Các công dụng có thể kể đến như:</p>

                <p class="font-extrabold text-[14px] mb-2">1.1 Túi xách đem lại sự tiện lợi</p>

                <p class="text-[14px] mb-2">
                    Chỉ với chiếc túi xách, mọi người có thể mang theo các vật dụng và tư trang cá nhân bên mình. Ngoài ra, chiếc túi xách nam hoặc túi xách nữ đẹp có thể đồng hành cùng mọi người trong các hành trình, từ đi học, đi làm đến dạo phố, du lịch hoặc mua sắm.
                </p>

                <div class="justify-items-center">
                    <img src="image/index/model_bag1.jpg" alt="Model_Bag" class="rounded-3">
                    <p class="fst-italic m-3">Chỉ với chiếc túi xách, mọi người có thể mang theo các vật dụng và tư trang cá nhân
                    </p>
                </div>

                <p class="font-extrabold text-[14px] mb-2">1.2 Linh hoạt khi sử dụng</p>
                <p class="text-[14px] mb-2">
                    Đối với các mẫu túi xách nữ hoặc túi xách nam hiện nay, hầu hết các hãng đều thiết kế một phần dây đeo vai có thể tháo rời. Điều này giúp mọi người có thêm một cách đeo khác thay vì phải cầm trong thời gian dài.

                </p>

                <div class="justify-items-center">
                    <img src="image/index/model_bag2.jpg" alt="Model_Bag" class="rounded-3">
                    <p class="fst-italic m-3">Túi xách có đa dạng cách đeo, từ đeo vai đến xách tay cũng đều thích hợp


                    </p>
                </div>

                <p class="font-extrabold text-[14px] mb-2">1.3 Khẳng định gu thẩm mỹ</p>
                <p class="text-[14px] mb-2">
                    Không chỉ là vật dụng đựng đồ, túi xách còn là phụ kiện giúp khẳng định gu thẩm mỹ riêng tại những nơi đông người.

                </p>
                <p class="text-[14px] mb-2">
                    Với sự đa dạng cả về kiểu dáng lẫn màu sắc, mọi người chỉ cần chọn một chiếc túi phù hợp với set đồ, vẻ ngoài của bạn sẽ trở nên sang trọng và cuốn hút hơn. Ngoài ra, các hãng còn liên tục ra mắt những mẫu túi xách hợp thời. Lúc này, việc sở hữu chiếc túi thời thượng sẽ giúp bạn tái khẳng định gu thời trang và cái tôi cá nhân.
                </p>

                <div class="justify-items-center">
                    <img src="image/index/model_bag3.jpg" alt="Model_Bag" class="rounded-3">
                    <p class="fst-italic m-3">Túi xách là phụ kiện giúp khẳng định gu thẩm mỹ riêng tại những nơi đông người
                    </p>
                </div>

                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">3</span>
                    Chính sách bảo hành, đổi trả
                </h1>
                <p class="text-[14px] mb-2">
                    Thị trường túi xách nam, túi xách nữ hiện nay khá sôi động với sự góp mặt của hàng loạt thương hiệu lớn nhỏ cùng đa dạng các địa điểm mua hàng với nhiều mức giá khác nhau. Trong số đó, BROWN LUGGAGE là cái tên được khách hàng tin tưởng mỗi khi có ý định sở hữu một mẫu túi xách chính hãng chất lượng cao với giá thành hợp lý cùng nhiều chính sách hậu mãi hấp dẫn.
                </p>
                <p class="text-[14px] mb-2">
                    MIA.vn là hệ thống siêu thị vali, balo, túi xách, phụ kiện du lịch chính hãng đầu tiên tại TP.HCM và hàng đầu tại Việt Nam, với hơn 18 siêu thị được phân phối rộng rãi khắp các thành phố lớn của cả nước..
                </p>
                <p class="text-[14px] mb-2">
                    Khi mua sản phẩm túi xách tại MIA.vn, khách hàng được hưởng chính sách bảo hành trọn đời, miễn phí đổi trả và giao hàng toàn quốc. Đồng thời, hệ thống cam kết bán hàng chính hãng. Nếu phát hiện hàng giả, khách hàng được hoàn tiền 200%. Khách hàng cũng có thể tìm hiểu thêm về các chính sách của BROWN LUGGAGE tại đây.

                </p>
                <p class="text-[14px] mb-2">
                    Balo với những ưu điểm cùng khả năng đựng đồ lý tưởng đã trở thành phụ kiện được nhiều người ưa chuộng trong thời gian qua. Với bài viết này, hy vọng bạn sẽ có được cái nhìn tổng quát về các thương hiệu chuyên sản xuất balo, đồng thời tìm ra một sản phẩm phù hợp khi có nhu cầu sở hữu chiếc balo đi học hoặc đi làm, du lịch.


                </p>

            </div>
        </div>

    </div>


    <?php

    mysqli_close($conn);
    include 'includes/footer.php';
    ?>