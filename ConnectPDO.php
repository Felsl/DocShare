<?php
try {
    // Ưu tiên lấy từ ENV (Docker), nếu không có thì dùng hằng số cũ
    $host = getenv('DB_HOST') ?: SERVERNAME;
    $db   = getenv('DB_NAME') ?: DBNAME;
    $user = getenv('DB_USER') ?: USERNAME;
    $pass = getenv('DB_PASS') ?: PASSWORD;

    // DSN
    $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

    // Tạo kết nối PDO
    $GLOBALS['pdo'] = new PDO($dsn, $user, $pass);

    // Gán biến local nếu cần dùng
    $pdo = $GLOBALS['pdo'];

    // Thiết lập chế độ lỗi
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
