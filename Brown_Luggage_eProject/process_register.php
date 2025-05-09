<?php
session_start();
require_once 'db_connect.php';

// Đảm bảo yêu cầu là POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
    exit;
}

// Lấy và xử lý dữ liệu đầu vào
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
$phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
$address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
$city = mysqli_real_escape_string($conn, $_POST['city'] ?? '');
$role = in_array($_POST['role'] ?? '', ['customer', 'admin', 'staff']) ? $_POST['role'] : 'customer';

// Kiểm tra email đã tồn tại
$sql = "SELECT id FROM Users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode(['success' => false, 'message' => 'Email đã được sử dụng.']);
    mysqli_stmt_close($stmt);
    exit;
}
mysqli_stmt_close($stmt);

// Thêm người dùng mới
$sql = "INSERT INTO Users (name, email, password, phone, address, city, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'sssssss', $name, $email, $password, $phone, $address, $city, $role);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Đăng ký thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi đăng ký: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
?>