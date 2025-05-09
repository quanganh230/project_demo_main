<?php
require_once 'db_connect.php';
include 'includes/header.php';

// Lấy product_id từ GET và đảm bảo là số nguyên
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    die("ID sản phẩm không hợp lệ");
}

// Xử lý gửi đánh giá
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    if (isset($_SESSION['user_id'])) {
        $rating = (int)$_POST['rating'];
        $message = mysqli_real_escape_string($conn, trim($_POST['message']));
        $user_id = (int)$_SESSION['user_id'];

        if ($rating >= 1 && $rating <= 5) {
            $insert_feedback_query = "INSERT INTO Feedbacks (product_id, user_id, rating, message, status, created_at) VALUES (?, ?, ?, ?, 'approved', NOW())";
            $insert_feedback_stmt = mysqli_prepare($conn, $insert_feedback_query);
            if ($insert_feedback_stmt) {
                mysqli_stmt_bind_param($insert_feedback_stmt, 'iids', $product_id, $user_id, $rating, $message);
                if (mysqli_stmt_execute($insert_feedback_stmt)) {
                    $success = "Đánh giá của bạn đã được gửi thành công!";
                } else {
                    $error = "Lỗi khi gửi đánh giá: " . mysqli_stmt_error($insert_feedback_stmt);
                }
                mysqli_stmt_close($insert_feedback_stmt);
            } else {
                $error = "Lỗi chuẩn bị truy vấn: " . mysqli_error($conn);
            }
        } else {
            $error = "Số sao phải từ 1 đến 5!";
        }
    } else {
        $error = "Vui lòng đăng nhập để gửi đánh giá!";
    }
}

// Truy vấn thông tin sản phẩm
$product_query = "
    SELECT 
        p.id AS product_id,
        p.name AS product_name,
        p.description AS product_description,
        p.category_id,
        cat.name AS category_name,
        p.brand_id,
        brands.name AS brand_name,
        GROUP_CONCAT(DISTINCT sizes.id ORDER BY sizes.name) AS size_ids,
        GROUP_CONCAT(DISTINCT sizes.name ORDER BY sizes.name) AS size_names,
        p.price AS product_price,
        GROUP_CONCAT(DISTINCT c.id ORDER BY c.name) AS colour_ids,
        GROUP_CONCAT(DISTINCT c.hex_code ORDER BY c.name) AS colour_hex_codes,
        GROUP_CONCAT(DISTINCT c.name ORDER BY c.name) AS colour_names,
        pi.image_url AS product_image,
        AVG(f.rating) AS average_rating
    FROM 
        Products p
        LEFT JOIN Categories cat ON p.category_id = cat.id
        LEFT JOIN Brands brands ON p.brand_id = brands.id
        LEFT JOIN Product_Sizes ps ON p.id = ps.product_id
        LEFT JOIN Sizes sizes ON ps.size_id = sizes.id
        LEFT JOIN Product_colors pc ON p.id = pc.product_id
        LEFT JOIN Colors c ON pc.color_id = c.id
        LEFT JOIN Product_images pi ON p.id = pi.product_id AND pi.u_primary = 1
        LEFT JOIN Feedbacks f ON p.id = f.product_id AND f.status = 'approved'
    WHERE 
        p.id = ?
    GROUP BY 
        p.id, p.name, p.price, p.description,
        p.category_id, cat.name, p.brand_id, brands.name, pi.image_url
    ORDER BY 
        p.id
";

$product_stmt = mysqli_prepare($conn, $product_query);
if (!$product_stmt) {
    die("Lỗi chuẩn bị truy vấn sản phẩm: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($product_stmt, 'i', $product_id);
if (!mysqli_stmt_execute($product_stmt)) {
    die("Lỗi thực thi truy vấn sản phẩm: " . mysqli_stmt_error($product_stmt));
}
$product_result = mysqli_stmt_get_result($product_stmt);
$product = mysqli_fetch_assoc($product_result);

if (!$product) {
    die("Không tìm thấy sản phẩm với ID: " . $product_id);
}

// Truy vấn ảnh bổ sung từ detail_product_images
$additional_images_query = "
    SELECT image_url
    FROM detail_product_images
    WHERE product_id = ?
    ORDER BY created_at
";
$additional_images_stmt = mysqli_prepare($conn, $additional_images_query);
if (!$additional_images_stmt) {
    die("Lỗi chuẩn bị truy vấn ảnh bổ sung: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($additional_images_stmt, 'i', $product_id);
if (!mysqli_stmt_execute($additional_images_stmt)) {
    die("Lỗi thực thi truy vấn ảnh bổ sung: " . mysqli_stmt_error($additional_images_stmt));
}
$additional_images_result = mysqli_stmt_get_result($additional_images_stmt);
$additional_images = [];
while ($image = mysqli_fetch_assoc($additional_images_result)) {
    $additional_images[] = $image['image_url'];
}

// Truy vấn phản hồi
$feedback_query = "
    SELECT f.*, u.name 
    FROM Feedbacks f 
    JOIN Users u ON f.user_id = u.id 
    WHERE f.product_id = ? AND f.status = 'approved' 
    ORDER BY f.created_at DESC
";
$feedback_stmt = mysqli_prepare($conn, $feedback_query);
if (!$feedback_stmt) {
    die("Lỗi chuẩn bị truy vấn phản hồi: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($feedback_stmt, 'i', $product_id);
if (!mysqli_stmt_execute($feedback_stmt)) {
    die("Lỗi thực thi truy vấn phản hồi: " . mysqli_stmt_error($feedback_stmt));
}
$feedback_result = mysqli_stmt_get_result($feedback_stmt);
$feedbacks = [];
while ($feedback = mysqli_fetch_assoc($feedback_result)) {
    $feedbacks[] = $feedback;
}
?>

<div class="display_products">
    <div class="w-75 mx-auto">
        <!-- Breadcrumb động -->
        <div class="d-flex justify-content-start mb-2">
            <p class="mb-0 text-dark">
                <a href="index.php" class="link text-dark text-decoration-none">Trang Chủ</a> /
                <a href="index.php?category_id=<?php echo $product['category_id']; ?>" class="link text-dark text-decoration-none">
                    <?php echo htmlspecialchars($product['category_name'] ?? 'Danh mục không xác định'); ?>
                </a>
            </p>
        </div>

        <!-- Chi tiết sản phẩm -->
        <div>
            <?php if ($product): ?>
                <div class="row">
                    <div class="col-md-6">
                        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="<?php echo htmlspecialchars($product['product_image'] ?: 'images/index/box-01.png'); ?>"
                                        class="d-block w-100"
                                        alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                </div>
                                <?php foreach ($additional_images as $index => $image): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo htmlspecialchars($image); ?>"
                                            class="d-block w-100"
                                            alt="<?php echo htmlspecialchars($product['product_name']) . ' - Angle ' . ($index + 1); ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Thương hiệu:</strong> <?php echo htmlspecialchars($product['brand_name']); ?></p>
                        <h1 class="mb-4"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                        <p class="card-text fw-bold small d-flex align-items-center gap-1">
                            <?php
                            $rating = $product['average_rating'] ?? 0;
                            for ($i = 1; $i <= 5; $i++):
                                if ($i <= floor($rating)):
                            ?>
                                    <i class="bi bi-star-fill text-warning"></i>
                                <?php elseif ($i - 0.5 <= $rating): ?>
                                    <i class="bi bi-star-half text-warning"></i>
                                <?php else: ?>
                                    <i class="bi bi-star text-warning"></i>
                            <?php endif;
                            endfor; ?>
                            <span class="ms-1"><?php echo number_format($rating, 1); ?></span>
                        </p>

                        <!-- Chọn màu -->
                        <p><strong>Mã màu:</strong></p>
                        <div class="color-options mb-3">
                            <?php if (!empty($product['colour_ids'])): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <select id="color_id" class="form-select w-50" required>
                                        <option value="">Chọn màu</option>
                                        <?php
                                        $color_ids = array_unique(array_map('trim', explode(',', $product['colour_ids'])));
                                        $color_names = array_unique(array_map('trim', explode(',', $product['colour_names'])));
                                        $color_hexes = array_unique(array_map('trim', explode(',', $product['colour_hex_codes'])));
                                        foreach ($color_ids as $index => $color_id):
                                            $color_name = $color_names[$index];
                                            $color_hex = $color_hexes[$index];
                                        ?>
                                            <option value="<?php echo $color_id; ?>"
                                                data-hex="<?php echo htmlspecialchars($color_hex); ?>">
                                                <?php echo htmlspecialchars($color_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="color-swatch ms-2" style="width: 30px; height: 30px; border-radius: 50%; border: 1px solid #ddd;"></div>
                                </div>
                                <script>
                                    document.getElementById('color_id').addEventListener('change', function() {
                                        const selectedOption = this.options[this.selectedIndex];
                                        const hex = selectedOption.getAttribute('data-hex');
                                        document.querySelector('.color-swatch').style.backgroundColor = hex;
                                    });
                                </script>
                            <?php else: ?>
                                <span>Không có màu</span>
                                <input type="hidden" id="color_id" value="0">
                            <?php endif; ?>
                        </div>

                        <!-- Chọn kích thước -->
                        <p><strong>Kích thước:</strong></p>
                        <div class="size-options mb-3">
                            <?php if (!empty($product['size_ids'])): ?>
                                <select id="size_id" class="form-select w-50" required>
                                    <option value="">Chọn kích thước</option>
                                    <?php
                                    $size_ids = array_unique(array_map('trim', explode(',', $product['size_ids'])));
                                    $size_names = array_unique(array_map('trim', explode(',', $product['size_names'])));
                                    foreach ($size_ids as $index => $size_id):
                                        $size_name = $size_names[$index];
                                    ?>
                                        <option value="<?php echo $size_id; ?>">
                                            <?php echo htmlspecialchars($size_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <span>Không có kích thước</span>
                                <input type="hidden" id="size_id" value="0">
                            <?php endif; ?>
                        </div>

                        <!-- Số lượng -->
                        <p><strong>Số lượng:</strong></p>
                        <input type="number" id="quantity" value="1" min="1" class="form-control w-25 d-inline" required>

                        <!-- Hiển thị giá tiền -->
                        <p><strong>Giá:</strong>
                        <div class="fs-1 fw-bold text-danger">
                            <?php echo number_format($product['product_price'], 0, '', '.'); ?>₫
                        </div>
                        </p>

                        <!-- Nút thêm vào giỏ hàng -->
                        <button class="btn btn-danger add-to-cart"
                            data-id="<?php echo $product['product_id']; ?>">MUA NGAY</button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">Sản phẩm không tồn tại hoặc đã hết hàng.</div>
                <?php endif; ?>
                </div>
        </div>



        <div>
            <?php
        // Hiển thị mô tả
        if (!empty($product['product_description'])) {
            // Hiển thị trực tiếp HTML từ description
            echo $product['product_description'];
        } else {
            echo '<p>Không có mô tả.</p>';
        }
        ?>
        </div>




            <div class="mt-8 w-75 mx-auto">
        <h2 class="text-2xl font-semibold mb-4">Đánh Giá Sản Phẩm</h2>
        <?php if (isset($success)) echo "<p class='text-green-500 mb-4'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="rating" class="form-label">Số Sao</label>
                    <div class="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                            <label for="star<?php echo $i; ?>" class="star"><i class="bi bi-star-fill"></i></label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Bình Luận</label>
                    <textarea name="message" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" name="submit_feedback" class="btn btn-danger">Gửi Đánh Giá</button>
            </form>
        <?php else: ?>
            <p>Vui lòng <a href="login.php" class="text-blue-500">đăng nhập</a> để gửi đánh giá!</p>
        <?php endif; ?>
        <div class="mt-6">
            <?php if (empty($feedbacks)): ?>
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            <?php else: ?>
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="border-b py-4">
                        <p><strong><?php echo htmlspecialchars($feedback['name']); ?></strong> -
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star-fill <?php echo $i <= $feedback['rating'] ? 'text-warning' : 'text-secondary'; ?>"></i>
                            <?php endfor; ?>
                        </p>
                        <p><?php echo htmlspecialchars($feedback['message']); ?></p>
                        <p class="text-gray-500 text-sm"><?php echo $feedback['created_at']; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $('.add-to-cart').click(function() {
            if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
                alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
                window.location.href = 'login.php';
                return;
            }

            var product_id = $(this).data('id');
            var color_id = $('#color_id').val() || 0;
            var size_id = $('#size_id').val() || 0;
            var quantity = parseInt($('#quantity').val());

            if (!color_id && $('#color_id').prop('tagName') === 'SELECT') {
                alert('Vui lòng chọn màu sắc!');
                return;
            }
            if (!size_id && $('#size_id').prop('tagName') === 'SELECT') {
                alert('Vui lòng chọn kích thước!');
                return;
            }
            if (isNaN(quantity) || quantity < 1) {
                alert('Vui lòng nhập số lượng hợp lệ!');
                return;
            }

            console.log('Sending AJAX:', {
                product_id,
                color_id,
                size_id,
                quantity
            });

            $.ajax({
                url: 'add_to_cart.php',
                method: 'POST',
                data: {
                    product_id: product_id,
                    color_id: color_id,
                    size_id: size_id,
                    quantity: quantity
                },
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX success:', response);
                    if (response && typeof response === 'object' && response.message) {
                        alert(response.message);
                        if (response.status === 'success') {
                            window.location.href = 'cart.php';
                        }
                    } else {
                        alert('Phản hồi không hợp lệ từ server');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    alert('Lỗi khi gửi yêu cầu: ' + (xhr.responseText || error));
                }
            });
        });

        // Hiển thị màu sắc khi chọn
        $('#color_id').change(function() {
            var hex = $(this).find('option:selected').data('hex');
            if (hex) {
                $(this).css('background-color', hex);
            } else {
                $(this).css('background-color', '');
            }
        });
    });
</script>

<style>
    .star-rating {
        display: flex;
        gap: 5px;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        font-size: 1.5rem;
        color: #ccc;
        cursor: pointer;
    }

    .star-rating input:checked~label,
    .star-rating label:hover,
    .star-rating label:hover~label {
        color: #ffc107;
    }

    .star-rating input:checked+label {
        color: #ffc107;
    }
</style>

<!-- Đóng tài nguyên -->
<?php
mysqli_stmt_close($product_stmt);
mysqli_stmt_close($additional_images_stmt);
mysqli_stmt_close($feedback_stmt);
mysqli_free_result($product_result);
mysqli_free_result($additional_images_result);
mysqli_free_result($feedback_result);
mysqli_close($conn);
include 'includes/footer.php';
?>