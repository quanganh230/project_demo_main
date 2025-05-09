<?php
require_once 'db_connect.php';
include 'includes/header.php';

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Tạo CSRF token nếu chưa tồn tại
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Hàm lấy mã hex của màu sắc
function getColorHex($conn, $color_id) {
    $query = "SELECT hex_code FROM Colors WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $color_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['hex_code'] ?? '#000000';
}

// Hàm lấy tên kích thước
function getSizeName($conn, $size_id) {
    $query = "SELECT name FROM Sizes WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $size_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['name'] ?? 'Unknown';
}

// Hàm lấy thông tin người dùng
function getUserById($conn, $user_id) {
    $query = "SELECT * FROM Users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Lấy danh sách cart_id từ URL và thiết lập session
if (isset($_GET['items'])) {
    $_SESSION['checkout_items'] = array_filter(explode(',', $_GET['items']), 'is_numeric');
}

$cart_items = [];
$total = 0;
$is_buy_now = isset($_GET['buy_now']);
$user_id = $_SESSION['user_id'];

if ($is_buy_now) {
    $product_id = mysqli_real_escape_string($conn, $_GET['buy_now']);
    $quantity = (int)$_GET['quantity'];
    $query = "SELECT p.*, pi.image_url 
              FROM Products p 
              JOIN Product_Images pi ON p.id = pi.product_id 
              WHERE p.id = ? AND pi.u_primary = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    if ($product) {
        $product['quantity'] = $quantity;
        $product['color'] = 'No color';
        $product['size'] = 'No size';
        $cart_items[] = $product;
        $total = ($product['price'] - $product['discount']) * $quantity;
    }
} else {
    if (isset($_SESSION['checkout_items']) && !empty($_SESSION['checkout_items'])) {
        $selected_items = $_SESSION['checkout_items'];
        error_log("Checkout items: " . print_r($selected_items, true));
        $placeholders = implode(',', array_fill(0, count($selected_items), '?'));
        $query = "SELECT c.*, p.name, p.price, pi.image_url 
                  FROM Carts c 
                  JOIN Products p ON c.product_id = p.id 
                  JOIN Product_Images pi ON p.id = pi.product_id 
                  WHERE c.user_id = ? AND c.id IN ($placeholders) AND pi.u_primary = 1";
        error_log("Query: $query");
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $types = str_repeat('i', count($selected_items) + 1);
        $params = array_merge([$user_id], $selected_items);
        error_log("Types: $types");
        error_log("Params: " . print_r($params, true));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            die("Query failed: " . $conn->error);
        }
        while ($row = $result->fetch_assoc()) {
            $row['color'] = $row['color_id'] ? getColorHex($conn, $row['color_id']) : 'No color';
            $row['size'] = $row['size_id'] ? getSizeName($conn, $row['size_id']) : 'No size';
            $cart_items[] = $row;
            $total += ($row['price']) * $row['quantity'];
        }
    } else {
        error_log("No items selected for checkout.");
        header('Location: cart.php');
        exit;
    }
}

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$user = getUserById($conn, $user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("CSRF Token from form: " . ($_POST['csrf_token'] ?? 'Not set'));
    error_log("CSRF Token from session: " . ($_SESSION['csrf_token'] ?? 'Not set'));

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Lỗi bảo mật: CSRF token không hợp lệ!";
    } else {
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $note = mysqli_real_escape_string($conn, $_POST['note']);

        $query = "INSERT INTO Orders (user_id, fullname, email, phone_number, address, note, total_money, status, payment_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('isssssd', $user_id, $fullname, $email, $phone, $address, $note, $total);
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            foreach ($cart_items as $item) {
                $price = $item['price'];
                $subtotal = $price * $item['quantity'];
                $product_id = isset($item['product_id']) ? $item['product_id'] : $item['id'];
                $query = "INSERT INTO order_items (order_id, product_id, price, quantity, total_money, payment_method) 
                          VALUES (?, ?, ?, ?, ?, 'online')";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('iidds', $order_id, $product_id, $price, $item['quantity'], $subtotal);
                $stmt->execute();
            }
            if (!$is_buy_now) {
                $ids = implode(',', array_map('intval', $selected_items));
                $query = "DELETE FROM Carts WHERE user_id = ? AND id IN ($ids)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
            }
            $update_user = "UPDATE Users SET phone = ?, address = ? WHERE id = ?";
            $stmt = $conn->prepare($update_user);
            $stmt->bind_param('ssi', $phone, $address, $user_id);
            $stmt->execute();
            unset($_SESSION['csrf_token']);
            unset($_SESSION['checkout_items']);
            header('Location: payment.php?order_id=' . $order_id);
            exit;
        } else {
            $error = "Lỗi khi tạo đơn hàng: " . $conn->error;
        }
    }
}
?>

    <div class="mt-3">
        <h1 class="text-3xl font-bold text-center mb-5">Thanh Toán</h1>
    </div>
    <div class="container mx-auto my-5 justify-content-center ">
        
        <?php if (isset($error)) echo "<p class='text-red-500 text-center mb-4'>$error</p>"; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ">
            <div class="">
                <h2 class="text-2xl font-semibold mb-4">Thông Tin Đặt Hàng</h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Họ Tên</label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số Điện Thoại</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa Chỉ</label>
                        <input type="text" name="address" class="form-control" value="<?php echo isset($user['address']) ? htmlspecialchars($user['address']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Ghi Chú</label>
                        <textarea name="note" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded w-full">Xác Nhận Đặt Hàng</button>
                </form>
            </div>
            <div class="ml-5">
                <h2 class="text-2xl font-semibold mb-4">Tóm Tắt Đơn Hàng</h2>
                <?php foreach ($cart_items as $item): ?>
                <div class="flex items-center mb-4">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product" class="h-16 w-16 object-cover mr-4">
                    <div>
                        <p class="font-semibold"><?php echo htmlspecialchars($item['name']); ?></p>
                        <p>
                            Màu sắc: 
                            <?php if ($item['color'] !== 'No color'): ?>
                                <span class="color-circle" style="background-color: <?php echo htmlspecialchars($item['color']); ?>;"></span>
                            <?php else: ?>
                                Không có màu
                            <?php endif; ?>
                        </p>
                        <p>Kích thước: <?php echo htmlspecialchars($item['size']); ?></p>
                        <p>Số lượng: <?php echo $item['quantity']; ?></p>
                        <p><?php echo number_format(($item['price']) * $item['quantity'], 2); ?> VNĐ</p>
                    </div>
                </div>
                <?php endforeach; ?>
                <p class="text-xl font-bold">Tổng cộng: <?php echo number_format($total, 2); ?> VNĐ</p>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>