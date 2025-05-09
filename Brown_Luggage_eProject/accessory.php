<?php
require_once 'db_connect.php';
require_once 'includes/functions_filter.php';
include 'includes/header.php';

// Get parameters
$records_per_page = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : 4; // Default to category 2 (Balo)
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
                <a href="index.php" class="link text-dark text-decoration-none">Trang Chủ</a> / Phụ Kiện
            </p>
        </div>

        <!-- Custom Nav chứa banner -->
        <div class="custom-nav bg-secondary bg-light h-auto border-1 border-secondary-subtle rounded">
            <a href="">
                <img src="image/index/phukien1.jpg" alt="" class="img-fluid w-100">
            </a>
            <?php include 'includes/filter.php' ?>
            <p class="fw-bold fs-4 mt-3 mb-3">Phụ kiện du lịch</p>
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
                                    <?php echo number_format($product['product_price'], 2); ?>₫
                                </p>
                            </div>
                        </div>
                    </a>

                </div>
            <?php endwhile; ?>

            <!-- Phân trang -->
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
                <p class="mb-3">Phụ kiện du lịch, còn gọi là phụ kiện vali, là người bạn đồng hành đáng tin cậy của các tín đồ đam mê xê dịch.</p>
                <p>Các mẫu phụ kiện du lịch phổ biến nhất hiện nay gồm gối cổ, bao trùm vali, thẻ treo hành lý, cân điện tử, v.v. Đây là những mẫu phụ kiện vali có tính ứng dụng cao, thích hợp sử dụng trong nhiều chuyến đi khác nhau như du lịch, công tác, v.v.
                </p>

                <p class="mb-3">Nhằm đáp ứng yêu cầu thẩm mỹ liên tục thay đổi của người dùng, các thương hiệu đã ra mắt hàng loạt những mẫu phụ kiện vali khác nhau. Với sự đa dạng cả về thiết kế, mẫu mã, màu sắc và chất liệu, mọi người sẽ có thể tìm ra cho mình món đồ ưng ý nhất.

                </p>


                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">1</span>
                    Công dụng của các món phụ kiện du lịch

                </h1>
                <p class="font-extrabold text-[14px] mb-2">Gối kê cổ, thẻ treo hành lý, bao trùm vali, đây là những món phụ kiện du lịch thông dụng và được ưa chuộng trong thời gian qua. Những công dụng giúp các món đồ này nhận được sự yêu mến của đông đảo mọi người có thể kể đến như:</p>

                <p class="font-extrabold text-[14px] mb-2">1.1 Đem lại sự thoải mái trong các chuyến đi
                </p>

                <p class="text-[14px] mb-2">
                Đối với những ai có hành trình di chuyển dài, ắt hẳn gối kê cổ là món phụ kiện du lịch không thể thiếu. Có phụ kiện này, phần gáy, cổ của người dùng sẽ cảm thấy thoải mái hơn, đẩy lùi những cơn đau, mỏi khi phải ngồi ngủ hoặc ngủ sai tư thế trong thời gian dài.

</p>

                <div class="justify-items-center">
                    <img src="image/index/model_phukien1.jpg" alt="Model_Bag" class="rounded-3">
                    <p class="fst-italic m-3">Các món phụ kiện du lịch như gối cổ đem lại sự thoải mái cho người dùng trong suốt hành trình


                    </p>
                </div>

               
                <p class="font-extrabold text-[14px] mb-2">1.2 Bảo vệ vali luôn như mới</p>
                <p class="text-[14px] mb-4">
                Nếu bạn là người có yêu cầu cao về tính thẩm mỹ, chắc chắn một chiếc bao trùm vali sẽ là món đồ không thể thiếu.</p>

                <p class="text-[14px] mb-4">
                Với món phụ kiện du lịch này, chiếc vali của bạn sẽ được bảo vệ an toàn, giảm thiểu những vấn đề về trầy xước, bám bụi, thấm nước, ngay cả khi sử dụng trong điều kiện thời tiết xấu. Ngoài ra, có chiếc bao trùm vali đồng hành, mọi người sẽ có thể an tâm về các đồ vật bên trong mỗi khi di chuyển.</p>

                    
                <div class="justify-items-center">
                    <img src="image/index/model_phukien2.jpg" alt="Model_Bag" class="rounded-3">
                    <p class="fst-italic m-3">Bao trùm là món phụ kiện vali giúp giữ cho sản phẩm không bị trầy xước trong quá trình sử dụng
                    </p>
                </div>

                <p class="font-extrabold text-[14px] mb-2">1.3 Tránh bị thất lạc hành lý</p>
                <p class="text-[14px] mb-4">
                Nếu bạn muốn hạn chế tình trạng thất lạc hành lý khi đi sân bay, nhà ga, bến xe vào những ngày cao điểm, thẻ treo vali là món phụ kiện du lịch phù hợp.</p>

                <p class="text-[14px] mb-4">
                Có phụ kiện du lịch này, mọi người sẽ dễ dàng xác định hành lý của mình. Hoặc nếu có xảy ra tình trạng nhầm lẫn thì cũng sẽ dễ dàng tìm thấy bởi trên thẻ có điền các thông tin cá nhân như họ tên, số điện thoại, v.v.</p>

                <div class="justify-items-center">
                    <img src="image/index/model_phukien3.jpg" alt="Model_Bag" class="rounded-3">
                    <p class="fst-italic m-3">Một chiếc thẻ treo hành lý giúp hành khách dễ dàng xác định được vali của mình
                    </p>
                </div>
                

                <p class="font-extrabold text-[14px] mb-2">1.4 Dễ dàng theo dõi số kg hành lý</p>

                <p class="text-[14px] mb-4">
                Để có thể dễ dàng theo dõi số kg hành lý, cân điện tử là món phụ kiện du lịch mọi người nên lựa chọn. </p>

                <p class="text-[14px] mb-4">
                Có món phụ kiện du lịch này cùng đồng hành, mọi người sẽ có thể dễ dàng nắm được liệu hành lý có bị quá kg so với quy định của các hãng hàng không hay không. Cân điện tử sẽ đo, cung cấp chính xác trọng lượng vali, giúp mọi người có thể sắp xếp đồ đạc hợp lý. </p>

                <div class="justify-items-center">
                    <img src="image/index/model_phukien4.jpg" alt="Model_Bag" class="rounded-3">
                    <p class="fst-italic m-3">Cân điện tử giúp người dùng dễ dàng theo dõi trọng lượng hành lý


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