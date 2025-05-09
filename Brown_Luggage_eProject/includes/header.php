<?php
session_start(); // Start the session to access $_SESSION variables
require_once 'db_connect.php';// Include database connection

$search = isset($search) ? $search : (isset($_GET['search']) ? $_GET['search'] : '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brown Luggage - Premium Luggage & Bags</title>
    <meta name="description" content="Shop premium luggage, backpacks, handbags, and accessories at Brown Luggage. Enjoy exclusive deals and quality products.">
    <meta name="keywords" content="luggage, backpacks, handbags, accessories, Brown Luggage">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        /* Tùy chỉnh chiều cao của thanh tìm kiếm */
        .icon-bar .form-control-sm {
            height: 28px; /* Giảm chiều cao của input */
            padding: 0.2rem 0.5rem; /* Giảm padding để thu nhỏ */
            font-size: 0.85rem; /* Giảm kích thước chữ */
            line-height: 1.2; /* Điều chỉnh line-height để căn giữa nội dung */
        }
        .icon-bar .btn-sm {
            height: 28px; /* Giảm chiều cao của button */
            padding: 0.2rem 0.5rem; /* Giảm padding */
            line-height: 1; /* Điều chỉnh line-height để căn giữa biểu tượng */
        }
        .icon-bar .btn-sm i {
            font-size: 0.9rem; /* Giảm kích thước biểu tượng tìm kiếm */
        }
        /* Đảm bảo header không bị ảnh hưởng */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 1rem; /* Giữ padding cố định */
        }
    </style>
</head>
<body>
    <img src="https://mia.vn/media/uploads/2025-04-15/topbar-pc.jpg" alt="Mua nhiều Giảm nhiều promotional banner" class="slogan">

    <header style="position: static;">
        <div class="logo">
            <a href="index.php"><span>Brown Luggage</span></a>
        </div>
        <nav>
            <ul class="">
                <li><a href="vali.php" class="fs-5">Vali</a></li>
                <li><a href="balo.php" class="fs-5">Balo</a></li>
                <li><a href="bag.php" class="fs-5">Túi xách</a></li>
                <li><a href="accessory.php" class="fs-5">Phụ kiện</a></li>
                <li><a href="contact.php" class="fs-5">LIÊN HỆ</a></li>
            </ul>
        </nav>
        <div class="icon-bar d-flex justify-content-end align-items-center p-2">
            <form method="GET" action="" class="d-flex align-items-center">
                <input class="form-control form-control-sm w-24 me-1 align-items-center mt-2" name="search" type="text" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm kiếm sản phẩm" aria-label="Tìm kiếm">
              
            </form>

            <div class="search-container position-relative d-inline-block ms-2">
                <a href="cart.php" class="position-relative">
                    <i class="bi bi-bag fs-6 p-2"></i>
                    <?php
                    // Đếm số mục trong giỏ hàng (số phần tử trong $_SESSION['cart'])
                    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    if ($cart_count > 0):
                    ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $cart_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="ml-3"><p>Đăng Xuất</p></a>
            
            <?php endif; ?>
        </div>
    </header>