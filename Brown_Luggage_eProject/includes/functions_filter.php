<?php
// Get filtered products with pagination
function getProducts($conn, $records_per_page, $page, $search, $category, $min_price, $max_price, $color = '', $size = '')
{
    $start_from = ($page - 1) * $records_per_page;

    // Build query conditions
    $conditions = [];
    $params = [];
    $types = '';

    if (!empty($search)) {
        $conditions[] = "(p.name LIKE ? OR c.name LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'ss';
    }

    if (!empty($category)) {
        $conditions[] = "p.category_id = ?";
        $params[] = $category;
        $types .= 'i';
    }

    if ($min_price !== '') {
        $conditions[] = "p.price >= ?";
        $params[] = $min_price;
        $types .= 'd';
    }

    if ($max_price !== '') {
        $conditions[] = "p.price <= ?";
        $params[] = $max_price;
        $types .= 'd';
    }

    if (!empty($color)) {
        $conditions[] = "pc.color_id = ?";
        $params[] = $color;
        $types .= 'i';
    }

    if (!empty($size)) {
        $conditions[] = "ps.size_id = ?";
        $params[] = $size;
        $types .= 'i';
    }

    $where_clause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

    // Get total records
    $sql_total = "SELECT COUNT(DISTINCT p.id) as total FROM Products p
                  LEFT JOIN Product_colors pc ON p.id = pc.product_id
                  LEFT JOIN Product_Sizes ps ON p.id = ps.product_id
                  LEFT JOIN Categories c ON p.category_id = c.id" . $where_clause;
    $stmt_total = $conn->prepare($sql_total);
    if (!empty($params)) {
        $stmt_total->bind_param($types, ...$params);
    }
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $row_total = $result_total->fetch_assoc();
    $total_records = $row_total['total'];
    $total_pages = ceil($total_records / $records_per_page);

    // Get records for current page
    $sql = "SELECT p.id AS product_id, p.name AS product_name, p.price AS product_price, 
            GROUP_CONCAT(DISTINCT c.hex_code) AS colour_hex_code, pi.image_url AS product_image, 
            GROUP_CONCAT(dpi.image_url SEPARATOR ',') AS additional_images,
            AVG(f.rating) AS average_rating
            FROM Products p
            LEFT JOIN Product_colors pc ON p.id = pc.product_id
            LEFT JOIN Colors c ON pc.color_id = c.id
            LEFT JOIN Product_Sizes ps ON p.id = ps.product_id
            LEFT JOIN Product_images pi ON p.id = pi.product_id AND pi.u_primary = 1
            LEFT JOIN detail_product_images dpi ON p.id = dpi.product_id
            LEFT JOIN Feedbacks f ON p.id = f.product_id
            " . $where_clause . "
            GROUP BY p.id, p.name, p.price, pi.image_url
            ORDER BY p.id
            LIMIT ?, ?";

    $stmt = $conn->prepare($sql);
    $params[] = $start_from;
    $params[] = $records_per_page;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    return [
        'products' => $result,
        'total_pages' => $total_pages,
        'total_records' => $total_records
    ];
}

// Get distinct categories for filter dropdown
function getCategories($conn)
{
    $categories = [];
    $sql = "SELECT DISTINCT name AS category FROM Categories ORDER BY name";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
    return $categories;
}

// Get distinct colors for filter dropdown
function getColors($conn)
{

    $colors = [];
    $sql = "SELECT  DISTINCT id,  hex_code AS color FROM Colors ORDER BY hex_code";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $colors[$row['id']] = $row['color'];
    }
   
    return $colors;
}

// Get distinct sizes for filter dropdown
function getSizes($conn)
{
    $sizes = [];
    $sql = "SELECT DISTINCT id, name AS size FROM Sizes ORDER BY name";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $sizes[$row['id']] = $row['size'];
    }
    return $sizes;
}

function getBrands($conn) {
    $brands = [];
    $sql = "SELECT DISTINCT id, name AS brand FROM Brands ORDER BY name"; // Thay 'Brands' bằng tên bảng thực tế
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $brands[$row['id']] = $row['brand'];
    }
    return $brands;
}
