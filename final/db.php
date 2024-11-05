<?php
$host = 'localhost'; // 資料庫伺服器
$dbname = 'cafe_reservation'; // 資料庫名稱
$user = 'root'; // 資料庫使用者
$pass = ''; // 資料庫密碼

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("資料庫連接失敗: " . $e->getMessage());
}
?>
