<?php
require_once 'db_connect.php';
require_once 'includes/functions_filter.php';
include 'includes/header.php';

// Get parameters
$records_per_page = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : 2; // Default to category 2 (Balo)
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
                <a href="index.php" class="link text-dark text-decoration-none">Trang Chủ</a> / Balo
            </p>
        </div>

        <!-- Custom Nav chứa banner -->
        <div class="custom-nav bg-secondary bg-light h-auto border-1 border-secondary-subtle rounded">
            <a href="">
                <img src="image/index/banner3.jpg" alt="" class="img-fluid w-100">
            </a>
            <?php include 'includes/filter.php' ?>
            <p class="fw-bold fs-4 mt-3 mb-3">Balo nam nữ chính hãng</p>
        </div>
    </div>
    <div class="w-75 mx-auto">
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

            <!-- Pagination -->
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
                <p class="mb-3">Balo với những ưu điểm nổi bật đã trở thành vật dụng được mọi người ưa chuộng trong những năm qua. Tuy xuất hiện từ lâu, tuy nhiên tính thẩm mỹ của balo không được đề cao. Theo dòng thời gian phát triển, những chiếc balo thô sơ ban đầu đã có nhiều thay đổi đáng kể, trở thành phụ kiện chiếm trọn niềm tin của người dùng.</p>
                <p> Thị trường balo hiện nay khá sôi động với sự góp mặt của các thương hiệu lớn nhỏ trong và ngoài nước. Các dòng balo nam, balo nữ hiện nay có đa dạng mẫu mã, kiểu dáng, màu sắc khác nhau, phù hợp để sử dụng trong nhiều trường hợp như du lịch, đi học hoặc làm việc.</p>
                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">1</span>
                    Những công dụng lý tưởng của balo
                </h1>
                <p class="font-extrabold text-[14px] mb-2">1.1 Balo là món phụ kiện thích hợp cho chuyến du lịch</p>
                <p class="text-[14px] mb-2">
                    Balo là món phụ kiện lý tưởng đồng hành cùng mọi người trong nhiều chuyến đi, chẳng hạn như dã ngoại, du lịch, đi phượt hoặc leo núi.
                </p>
                <p class="text-[14px] mb-2">
                    Với balo, mọi người có thể tinh gọn hành lý, đồng thời có thể linh hoạt di chuyển trên nhiều địa hình khác nhau. Sở hữu tính năng chống thấm nước hoàn hảo, balo sẽ dễ dàng cùng bạn di chuyển ngay cả trong điều kiện thời tiết không thuận lợi.
                </p>

                <p class="font-extrabold text-[14px] mb-2">1.2 Sử dụng balo đi học, làm việc</p>
                <p class="text-[14px] mb-4">
                    Từ học sinh, sinh viên đến dân văn phòng, hầu như ai cũng ưa chuộng balo vì tính tiện lợi của mình. Với chiếc balo, học sinh, sinh viên có thể dùng để mang theo sách vở, laptop, giáo trình, thay vì sử dụng túi xách.
                </p>

                <p class="text-[14px] mb-4">
                    Đối với học sinh, chiếc balo có thiết kế cân bằng, ổn định sẽ không ảnh hưởng đến xương và cơ. Lúc này, chiếc balo có phần quai đeo mềm, chắc chắn là lựa chọn phù hợp.
                </p>

                <p class="text-[14px] mb-4">
                    Trong khi đó, laptop là món đồ không thể thiếu đối với sinh viên và dân văn phòng. Với chiếc balo, mọi người có thể dễ dàng mang theo laptop, hạn chế tình trạng lỉnh kỉnh khi di chuyển.
                </p>
                <div class="justify-items-center">
                    <img src="image/index/model_balo.jpg" alt="Model_Balo" class="rounded-3">
                    <p class="fst-italic m-3">Balo là người bạn đồng hành lý tưởng mỗi lần đến trường, đi làm</p>
                </div>

                <p class="font-extrabold text-[14px] mb-2">1.3 Món đồ lý tưởng cho những lần mua sắm</p>
                <p class="text-[14px] mb-2">
                    Thay vì phải xách những chiếc túi lớn nhỏ khác nhau trong mỗi lần mua sắm, một chiếc balo có không gian rộng sẽ giúp bạn đựng đồ hoàn hảo.
                </p>

                <p class="text-[14px] mb-2">
                    Với thiết kế hai quai đeo sau lưng, mọi người sẽ không còn cảm giác nặng tay khi xách quá nhiều túi cùng lúc. Điều này cũng góp phần giảm thiểu số lượng rác thải nhựa ra môi trường, quả là một công đôi việc phải không?
                </p>

                <p class="font-extrabold text-[14px] mb-2">1.4 Khẳng định gu thời trang riêng</p>

                <p class="text-[14px] mb-2">
                    Không chỉ là vật dụng giúp đựng đồ đạc gọn gàng, balo còn là phụ kiện giúp mọi người khẳng định gu thẩm mỹ cá nhân.
                </p>

                <p class="text-[14px] mb-2">
                    Hiện nay, hầu hết các hãng sản xuất balo không chỉ tập trung vào chất lượng mà còn chú trọng về thiết kế. Điều đó giúp mọi người có thêm nhiều lựa chọn hơn với các kiểu balo đa dạng từ thiết kế, màu sắc
                </p>

                <p class="font-extrabold text-[14px] mb-2">1.5 Bảo vệ thiết bị bên trong hoàn hảo</p>
                <p class="text-[14px] mb-2">
                    Balo mang đến cho người dùng sự an tâm khi sở hữu khả năng chống thấm nước, gió bụi lý tưởng. Nhiều hãng còn tích hợp thêm một ngăn chống sốc bên trong, giúp mọi người an tâm khi đựng laptop, máy ảnh hoặc các món đồ có giá trị.
                </p>


                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">2</span>
                    Các loại balo thông dụng nhất hiện nay
                </h1>

                <p class="font-extrabold text-[14px] mb-2">2.1 Balo truyền thống</p>
                <p class="text-[14px] mb-2">
                    Đây là mẫu balo phổ biến, có tính ứng dụng cao và phù hợp sử dụng trong nhiều mục đích khác nhau.</p>
                <p class="text-[14px] mb-2">
                    Các mẫu balo truyền thống thường được làm từ polyester hoặc canvas cứng cáp, dày dặn, có khả năng giữ phom hoàn hảo. Tổng thể balo gồm hai dây đeo, một ngăn chính đựng đồ có khóa kéo. Đây là mẫu balo thích hợp cho những ai mang ít đồ.</p>

                <p class="font-extrabold text-[14px] mb-2">2.2 Balo du lịch</p>
                <p class="text-[14px] mb-4">
                    Balo du lịch thường được làm từ các chất liệu ít thấm nước, có thể tích lớn, dây đeo, khóa bảo vệ hoặc dây rút cùng phần nắp đậy. Trọng lượng balo thường nhẹ để người đeo có thể thoải mái ngay khi di chuyển nhiều.</p>

                <p class="font-extrabold text-[14px] mb-2">2.3 Balo laptop</p>
                <p class="text-[14px] mb-4">
                    Đối với các dòng balo laptop, khả năng chống sốc là điều được mọi người quan tâm hơn cả. Một chiếc balo laptop tốt thường có thiết kế nhỏ gọn, làm từ các chất liệu chống nước tốt như polyester, ethylene.
                </p>

                <p class="text-[14px] mb-4">
                    Điểm khác biệt của dòng balo này chính là ngăn chống sốc có nhiều lớp mút dày, hạn chế tối đa sự va đập có thể khiến laptop bị hỏng. Ngoài ra, bên trong balo còn có nhiều ngăn để mọi người đựng những thiết bị khác như tai nghe, loa, cục sạc, v.v.

                </p>

                <div class="justify-items-center">
                    <img src="image/index/model_balo2.jpg" alt="Model_Balo" class="rounded-3">
                    <p class="fst-italic m-3">Balo laptop</p>
                </div>

                <p class="font-extrabold text-[14px] mb-2">2.4 Balo tập luyện</p>
                <p class="text-[14px] mb-2">
                    Đối với những ai thường tham gia các hoạt động thể thao như chạy bộ, gym hoặc yoga, balo tập luyện là phụ kiện khá phổ biến. Thông thường, dòng sản phẩm này được chia thành hai loại balo nam đẹp và balo nữ để mọi người dễ dàng lựa chọn.</p>
                <p class="text-[14px] mb-2">
                    Dòng balo tập luyện thường có kích thước lớn, được làm từ chất liệu vải polyester có độ bền cao, chống thấm nước và dễ dàng vệ sinh. Mẫu balo này có thiết kế 1 đến 2 ngăn lớn cùng một ngăn khóa kéo phía trước để tối ưu hóa không gian sử dụng.</p>

                <p class="text-[14px] mb-2">
                    Các tiện ích nổi bật khác của dòng balo này phải kể đến phần quai đeo lót mút đệm dễ dàng tùy chỉnh. Hai bên hông balo được thiết kế một túi lưới để người dùng đựng chai nước, kem chống nắng, v.v.</p>

                </p>



                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">3</span>
                    Những thương hiệu balo phổ biến nhất hiện nay
                </h1>
                <p class="font-extrabold text-[14px] mb-2">3.1 Balo Herschel</p>
                <p class="text-[14px] mb-2">
                    Balo Herschel là thương hiệu nổi tiếng từ Vancouver, Canada. Được thành lập vào năm 2009, các thiết kế của hãng in đậm phong cách retro hipster. Tuy đến từ Bắc Mỹ, thế nhưng các sản phẩm của Herschel lại được lòng các tín đồ thời trang châu Á.</p>
                <p class="text-[14px] mb-2">
                    Các sản phẩm do Herschel thiết kế là sự kết hợp hài hòa giữa đường nét cổ điển và cách phối màu trẻ trung, phù hợp với nhiều đối tượng khác nhau. Dù là balo nam hay balo nữ, Herschel vẫn được đánh giá cao với đường may tỉ mỉ, vẻ ngoài cứng cáp.

                </p>
                <p class="text-[14px] mb-2">
                    Herschel sở hữu hàng loạt những thiết kế nổi bật, từ các mẫu balo thường đến balo đi học. Giá cho mỗi chiếc balo Herschel không quá đắt, dao động từ 700.000 VND - 2.000.000 VND.
                </p>

                <div class="justify-items-center">
                    <img src="image/index/model_balo4.jpg" alt="Model_Balo" class="rounded-3">
                    <p class="fst-italic m-3">Balo Herschel</p>
                </div>



                <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
                    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">4</span>
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
</div>

<?php
mysqli_close($conn);
include 'includes/footer.php';
?>