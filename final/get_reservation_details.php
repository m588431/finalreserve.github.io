<?php
session_start();
include 'db.php';  // 包含資料庫連接

$response = ['success' => false];

if (isset($_SESSION['user_id'])) {  // 假設用戶已登入，並且 session 中有 user_id
    $user_id = $_SESSION['user_id'];

    // 從資料庫中獲取該用戶的最新預訂資訊
    $sql = "SELECT seat_id, date, time, people, phone, pre_order FROM reservations WHERE user_id = ? ORDER BY id DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reservation) {
        $response['success'] = true;
        $response['data'] = $reservation;
    } else {
        $response['success'] = false;
        $response['message'] = '尚未找到預訂';
    }
} else {
    $response['message'] = '未登入';
}

echo json_encode($response);
?>
