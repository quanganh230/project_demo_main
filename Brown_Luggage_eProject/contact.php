<?php
require_once 'db_connect.php';
include 'includes/header.php';

// Khởi tạo biến lỗi
$error = null;
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Validate subject is one of the ENUM values
    $valid_subjects = ['Support', 'Complaint', 'Inquiry', 'Suggestion'];
    if (!in_array($subject, $valid_subjects)) {
        $error = "Vui lòng chọn một chủ đề hợp lệ.";
    } else {
        $query = "INSERT INTO contacts (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
        if (mysqli_query($conn, $query)) {
            // Output JavaScript for SweetAlert and redirect
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Thành công!',
                        text: 'Phản hồi của bạn đã được gửi thành công!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                });
            </script>";
        } else {
            $error = "Lỗi khi gửi phản hồi: " . mysqli_error($conn);
        }
    }
}
?>

<div>
    <h1 class="text-3xl font-bold text-center mb-5">Liên Hệ</h1>
</div>
<div class="container w-75 mx-auto">
    <?php if ($error) echo "<p class='text-red-500 text-center mb-4'>$error</p>"; ?>
    <form method="POST" class="w-100 mx-auto">
        <div class="mb-3">
            <label for="name" class="form-label">Họ Tên</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="subject" class="form-label">Chủ Đề</label>
            <select name="subject" class="form-control" required>
                <option value="">Chọn chủ đề</option>
                <option value="Support">Hỗ trợ</option>
                <option value="Complaint">Khiếu nại</option>
                <option value="Inquiry">Hỏi đáp</option>
                <option value="Suggestion">Gợi ý</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Tin Nhắn</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="bg-danger text-white px-4 py-2 rounded w-full">Gửi</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js"></script>
<?php if (!defined('FOOTER_INCLUDED')) {
    define('FOOTER_INCLUDED', true);
    include 'includes/footer.php';
} ?>