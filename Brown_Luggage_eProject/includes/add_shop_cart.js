
let selectedColor = null;
let selectedSize = null;

// Hàm chọn màu
function selectColor(productId, color) {
    selectedColor = color;
    // Thêm hiệu ứng visual (tùy chọn)
    document.querySelectorAll('.color-circle').forEach(btn => {
        btn.style.border = btn.style.backgroundColor === color ? '2px solid black' : 'none';
    });
    btn.style.border
}

// Hàm chọn kích thước
function selectSize(productId, size, button) {
    selectedSize = size;
    // Thêm hiệu ứng visual (tùy chọn)
    document.querySelectorAll('.size-button').forEach(btn => {
        btn.classList.remove('border-danger');
        btn.classList.add('border-dark');
    });
    button.classList.add('border-danger');
    button.classList.remove('border-dark');
}

// Hàm thêm vào giỏ hàng và chuyển hướng
function addToCart(productId) {
    if (!selectedColor || !selectedSize) {
        alert('Vui lòng chọn màu và kích thước trước khi mua!');
        return;
    }

    // Gửi yêu cầu AJAX để thêm sản phẩm vào giỏ hàng
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'add_to_cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Chuyển hướng đến trang giỏ hàng
            window.location.href = 'cart.php';
        }
    };
    xhr.send(`product_id=${productId}&color=${selectedColor}&size=${selectedSize}`);
}
