<?php
require_once 'db_connect.php';
include 'includes/header.php';

// Khởi tạo biến lỗi
$error = null;

// Kiểm tra order_id từ URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0) {
    $error = "Mã đơn hàng không hợp lệ. Vui lòng kiểm tra lại.";
}

// Nếu không có lỗi, lấy thông tin đơn hàng
$order = null;
$order_details = [];
if (!$error) {
    // Lấy thông tin đơn hàng từ bảng Orders sử dụng prepared statement
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    if ($stmt === false) {
        $error = "Lỗi truy vấn cơ sở dữ liệu: " . $conn->error;
    } else {
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if (!$order) {
            $error = "Không tìm thấy đơn hàng với mã #$order_id.";
        }
    }
}

// Nếu không có lỗi, lấy danh sách sản phẩm trong đơn hàng
if (!$error) {
    $stmt = $conn->prepare("SELECT oi.*, p.name, pi.image_url  
                           FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id 
                           JOIN product_Images pi ON p.id = pi.product_id 
                           WHERE oi.order_id = ? AND pi.u_primary = 1");
    if ($stmt === false) {
        $error = "Lỗi truy vấn cơ sở dữ liệu: " . $conn->error;
    } else {
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $order_details[] = $row;
        }

        if (empty($order_details)) {
            $error = "Không tìm thấy chi tiết đơn hàng với mã #$order_id.";
        }
    }
}
?>


<div class="mt-3">
    <h1 class="text-3xl font-bold text-center mb-5">Thông Tin Thanh Toán</h1>
</div>
<div class="w-75 mx-auto">
<div class="container mx-auto my-5">


<?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Lỗi:</strong>
        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-2xl font-semibold mb-4">Thông Tin Đơn Hàng</h2>
            <p><strong>Mã Đơn Hàng:</strong> #<?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Họ Tên:</strong> <?php echo htmlspecialchars($order['fullname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Số Điện Thoại:</strong> <?php echo htmlspecialchars($order['phone_number']); ?></p>
            <p><strong>Địa Chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Ghi Chú:</strong> <?php echo htmlspecialchars($order['note'] ?: 'Không có ghi chú'); ?></p>
            <p><strong>Tổng Tiền:</strong> <?php echo number_format($order['total_money'], 2); ?> VNĐ</p>
        </div>

    </div>
    <div>
        <h2 class="text-2xl font-semibold mb-4">Danh Sách Sản Phẩm</h2>
        <?php foreach ($order_details as $item): ?>
            <div class="flex items-center mb-4">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="h-16 w-16 object-cover mr-4">
                <div>
                    <p class="font-semibold"><?php echo htmlspecialchars($item['name']); ?></p>
                    <p>Số lượng: <?php echo $item['quantity']; ?></p>
                    <p>Giá: <?php echo number_format($item['price'], 2); ?> VNĐ</p>
                    <p>Tổng: <?php echo number_format($item['total_money'], 2); ?> VNĐ</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Phần Thông Tin Chuyển Khoản -->

<?php endif; ?>
</div>
<div class="ml-3">
<h2 class="text-2xl font-semibold mb-4">Thông Tin Chuyển Khoản</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <p><strong>Ví điện tử: </strong>Viettel Money</p>
        <p><strong>Số Tài Khoản:</strong> 0231253646</p>
        <p><strong>Chủ Tài Khoản:</strong> Vu Cong Thanh</p>
        <p><strong>Nội Dung Chuyển Khoản:</strong> Thanh toán đơn hàng #<?php echo htmlspecialchars($order['id']); ?></p>
        <p class="text-red-500 mt-2">Vui lòng chuyển khoản số tiền <strong><?php echo number_format($order['total_money'], 2); ?> VNĐ</strong> để hoàn tất đơn hàng.</p>
    </div>
    
    <div class="text-center ">
        <h3 class="text-lg font-semibold mb-2">Quét mã QR để thanh toán</h3>
        <img src="image/index/money.jpg" alt="QR Code Thanh Toán" class="mx-auto h-48 w-48 object-contain">
        <p class="text-gray-600 mt-2">Sử dụng ứng dụng ngân hàng để quét mã QR và thanh toán.</p>
    </div>
</div>

</div>

<div class="mt-6 text-center">
    <a href="index.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" id="confirmPayment" class="bg-green-500 text-white px-4 py-2 rounded">Xác Nhận Thanh Toán</a>
</div>
</div>

<?php if (!defined('FOOTER_INCLUDED')) {
    define('FOOTER_INCLUDED', true);
    include 'includes/footer.php';
} ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#confirmPayment').click(function(e) {
            e.preventDefault(); // Ngăn hành động mặc định của liên kết
            const url = $(this).attr('href');

            Swal.fire({
                title: 'Xác nhận thanh toán',
                text: 'Bạn có chắc chắn đã chuyển khoản và muốn xác nhận thanh toán cho đơn hàng này?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Thành công!',
                        text: 'Thanh toán đã được xác nhận.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = url; // Chuyển hướng sau khi xác nhận
                    });
                }
            });
        });
    });
</script>
<script src="assets/js/main.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/main.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
