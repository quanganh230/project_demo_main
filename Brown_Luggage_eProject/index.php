<?php
require_once 'db_connect.php';
require_once 'includes/functions_filter.php';
include 'includes/header.php';

// Get parameters
$records_per_page = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : '';
$color = isset($_GET['color']) && is_numeric($_GET['color']) ? (int)$_GET['color'] : '';
$size = isset($_GET['size']) && is_numeric($_GET['size']) ? (int)$_GET['size'] : '';

// Fetch data
$data = getProducts($conn, $records_per_page, $page, $search, '', $min_price, $max_price, $color, $size);
$products = $data['products'];

// Filter products with 5-star rating
$filtered_products = [];
while ($product = $products->fetch_assoc()) {
    if (isset($product['average_rating']) && $product['average_rating'] == 5) {
        $filtered_products[] = $product;
    }
}

// Recalculate total pages based on filtered products
$total_filtered_records = count($filtered_products);
$total_pages = ceil($total_filtered_records / $records_per_page);

// Apply pagination to filtered products
$start = ($page - 1) * $records_per_page;
$paginated_products = array_slice($filtered_products, $start, $records_per_page);
?>

<div class="display_products">
    <div class="custom-nav bg-secondary bg-light h-auto w-75 mx-auto justify-items-center border-1 border-secondary-subtle rounded">
        <a href=""><img src="image/index/banner.jpg" alt="" class="img-fluid w-100"></a>

        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row g-0">
                        <!-- Hình ảnh 1: Larita Soly -->
                        <div class="col-6">
                            <img src="image/index/home-box-1.jpg" class="d-block w-100" alt="Vali Larita Soly">
                        </div>
                        <!-- Hình ảnh 2: Larita Manzo -->
                        <div class="col-6">
                            <img src="image/index/home-box-2.jpg" class="d-block w-100" alt="Vali Larita Manzo">
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row g-0">
                        <div class="col-6">
                            <img src="image/index/home-box-3.jpg" class="d-block w-100" alt="Slide 3">
                        </div>
                        <div class="col-6">
                            <img src="image/index/home-box-4.jpg" class="d-block w-100" alt="Slide 4">
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <ul class="nav justify-content-center d-flex justify-content-evenly align-items-center mb-5">
        </ul>
    </div>

    <!-- Row for products -->
    <div class="w-75 mx-auto">
        <div class="row">
            <?php if (empty($paginated_products)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Không có sản phẩm nào đạt 5 sao.</p>
                </div>
            <?php else: ?>
                <?php foreach ($paginated_products as $product): ?>
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
                                <div class="card-body">
                                    <div class="color-options d-flex justify-content-center align-items-center">
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
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Row for Strength 1, 2, 3 (fixed position) -->
        <div class="row mt-4">
            <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 border-0">
                    <img src="image/index/box-01.png" class="card-img-top img-fluid" alt="Strength 1">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 border-0">
                    <img src="image/index/box-02.png" class="card-img-top img-fluid" alt="Strength 2">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 border-0">
                    <img src="image/index/box-03.png" class="card-img-top img-fluid" alt="Strength 3">
                </div>
            </div>
        </div>

        <!-- Row for Model 1 and Model 2 -->
        <div class="row mt-4">
            <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
                <div class="card h-100 border-0">
                    <img src="image/index/model1.jpg" class="card-img-top img-fluid" alt="Model 1">
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
                <div class="card h-100 border-0">
                    <img src="image/index/model2.jpg" class="card-img-top img-fluid" alt="Model 2">
                </div>
            </div>
        </div>

        <!-- Row for Brands -->
        <div class="row mt-4">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                    <img src="image/index/brand1.jpg" class="card-img-top img-fluid h-auto w-20" alt="Brand 1">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                    <img src="image/index/brand2.png" class="card-img-top img-fluid" alt="Brand 2">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                    <img src="image/index/brand3.jpg" class="card-img-top img-fluid" alt="Brand 3">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                    <img src="image/index/brand4.png" class="card-img-top img-fluid" alt="Brand 4">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                    <img src="image/index/brand5.jpg" class="card-img-top img-fluid h-auto w-15" alt="Brand 5">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                    <img src="image/index/brand6.jpg" class="card-img-top img-fluid" alt="Brand 6">
                </div>
            </div>
        </div>

        <!-- Row for Miss Universe image -->
        <div class="row mt-4">
            <div class="col-12">
                <img src="image/index/miss.jpg" alt="miss-univeser" class="mb-3 rounded-3 w-100">
            </div>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
include 'includes/footer.php';
?>