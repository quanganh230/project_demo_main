<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

error_log("Starting add_to_cart.php");

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$action = isset($_POST['action']) ? $_POST['action'] : 'add';

if ($action === 'add') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $color_id = isset($_POST['color_id']) ? intval($_POST['color_id']) : 0;
    $size_id = isset($_POST['size_id']) ? intval($_POST['size_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    error_log("Add product: user_id=$user_id, product_id=$product_id, color_id=$color_id, size_id=$size_id, quantity=$quantity");

    if ($product_id <= 0 || $quantity < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Thông tin sản phẩm không hợp lệ']);
        exit;
    }

    if ($color_id == 0) $color_id = NULL;
    if ($size_id == 0) $size_id = NULL;

    if (!$conn) {
        error_log("Connection failed: No valid database connection");
        echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối cơ sở dữ liệu']);
        exit;
    }

    $check_query = "
        SELECT p.id 
        FROM Products p
        LEFT JOIN Product_colors pc ON p.id = pc.product_id AND (pc.color_id = ? OR ? IS NULL)
        LEFT JOIN Product_Sizes ps ON p.id = ps.product_id AND (ps.size_id = ? OR ? IS NULL)
        WHERE p.id = ?
    ";
    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('iiiii', $color_id, $color_id, $size_id, $size_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không có màu hoặc kích thước này']);
        exit;
    }

    $insert_query = "
        INSERT INTO Carts (user_id, product_id, color_id, size_id, quantity) 
        VALUES (?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ";
    $stmt = $conn->prepare($insert_query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('iiiii', $user_id, $product_id, $color_id, $size_id, $quantity);
    if ($stmt->execute()) {
        error_log("Insert successful: user_id=$user_id, product_id=$product_id");
        echo json_encode(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng!']);
    } else {
        error_log("Insert failed: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi thêm vào giỏ hàng: ' . $stmt->error]);
    }
} elseif ($action === 'increase' || $action === 'decrease') {
    $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
    $delta = $action === 'increase' ? 1 : -1;

    error_log("Update cart: cart_id=$cart_id, delta=$delta");

    if ($cart_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Thông tin không hợp lệ']);
        exit;
    }

    // Lấy số lượng hiện tại
    $select_query = "SELECT quantity FROM Carts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($select_query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('ii', $cart_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy mục trong giỏ hàng']);
        exit;
    }

    $current_quantity = $result['quantity'];
    $new_quantity = $current_quantity + $delta;

    if ($new_quantity <= 0) {
        $delete_query = "DELETE FROM Carts WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($delete_query);
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param('ii', $cart_id, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!']);
        } else {
            error_log("Delete failed: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa sản phẩm: ' . $stmt->error]);
        }
    } else {
        $update_query = "UPDATE Carts SET quantity = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_query);
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param('iii', $new_quantity, $cart_id, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Đã cập nhật số lượng!']);
        } else {
            error_log("Update failed: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi cập nhật số lượng: ' . $stmt->error]);
        }
    }
} elseif ($action === 'remove') {
    $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;

    error_log("Remove cart: cart_id=$cart_id");

    if ($cart_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Thông tin không hợp lệ']);
        exit;
    }

    $delete_query = "DELETE FROM Carts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('ii', $cart_id, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!']);
    } else {
        error_log("Delete failed: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa sản phẩm: ' . $stmt->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ']);
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
