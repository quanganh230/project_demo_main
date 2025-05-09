<?php
// Get colors from database


$colors = getColors($conn);

// Get current selected color from GET parameter


$brands = getBrands($conn);
?>

<div class="filter-form mt-3">
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#filterModal">
    <i class="bi bi-funnel"></i>
    </button>

    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Lọc sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="GET" action="" id="filterForm">
                        <!-- Min Price -->
                        <div class="mb-3">
                            <label for="min_price" class="form-label">Min Price:</label>
                            <input type="number" class="form-control" name="min_price" value="<?php echo $min_price !== '' ? htmlspecialchars($min_price) : ''; ?>" step="0.01" placeholder="0.00">
                        </div>

                        <!-- Max Price -->
                        <div class="mb-3">
                            <label for="max_price" class="form-label">Max Price:</label>
                            <input type="number" class="form-control" name="max_price" value="<?php echo $max_price !== '' ? htmlspecialchars($max_price) : ''; ?>" step="0.01" placeholder="0.00">
                        </div>

                        <!-- Color Selection -->
                        <div class="mb-3">
                            <label class="form-label">Màu:</label>
                            <div class="d-flex flex-wrap gap-2" id="colorOptions">
                                <?php foreach ($colors as $color_id => $hex_code): ?>
                                    <div class="color-circle-wrapper" data-color-id="<?php echo $color_id; ?>">
                                        <div class="color-circle" style="background-color: <?php echo htmlspecialchars($hex_code); ?>;" 
                                             data-color-id="<?php echo $color_id; ?>"></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="color" id="selectedColor" value="<?php echo $current_color; ?>">
                        </div>

                        <!-- Brand (Placeholder - cần thêm hàm lấy danh sách thương hiệu) -->
                        <div class="mb-3">
                            <label for="brand" class="form-label">Thương Hiệu:</label>
                        
                                <select class="form-control" name="brand" id="brand">
                                    <option value="">Chọn thương hiệu</option>
                                    <?php foreach ($brands as $brand_id => $brand_name): ?>
                                        <option value="<?php echo $brand_id; ?>" <?php echo isset($_GET['brand']) && $_GET['brand'] == $brand_id ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($brand_name); ?>
                                        </option>
                                    <?php endforeach; ?>
</select>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle color selection
    document.addEventListener('DOMContentLoaded', function () {
        const colorCircles = document.querySelectorAll('.color-circle');
        const selectedColorInput = document.getElementById('selectedColor');

        colorCircles.forEach(circle => {
            circle.addEventListener('click', function () {
                const colorId = this.getAttribute('data-color-id');
                const isSelected = this.classList.contains('selected');

                // Remove selected class from all circles
                colorCircles.forEach(c => c.classList.remove('selected'));

                // Toggle selected state
                if (!isSelected) {
                    this.classList.add('selected');
                    selectedColorInput.value = colorId;
                } else {
                    selectedColorInput.value = '';
                }
            });
        });

        // Set initial selected color if exists
        if (selectedColorInput.value) {
            const initialColor = document.querySelector(`.color-circle[data-color-id="${selectedColorInput.value}"]`);
            if (initialColor) {
                initialColor.classList.add('selected');
            }
        }
    });
</script>



<style>
    .color-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border 0.3s ease;
    }
    .color-circle.selected {
        border: 2px solid red;
    }
    .color-circle-wrapper {
        display: inline-block;
        margin: 2px;
    }
</style>