<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

try {
    // 查找該用戶預訂的座位ID
    $stmt = $pdo->prepare("SELECT seat_id FROM reservations WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    // 如果有預訂記錄
    if ($reservation) {
        $seat_id = $reservation['seat_id'];  // 保存座位ID

        // 刪除該用戶的預訂資訊
        $deleteStmt = $pdo->prepare("DELETE FROM reservations WHERE user_id = ?");
        $deleteStmt->execute([$user_id]);

        // 更新座位的預訂狀態，將is_reserved設為0，reserved_by_user設為NULL
        $updateSeat = $pdo->prepare("UPDATE seats SET is_reserved = 0, reserved_by_user = NULL WHERE seat_id = ?");
        $updateSeat->execute([$seat_id]);

        if ($deleteStmt->rowCount() > 0 && $updateSeat->rowCount() > 0) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "no_reservation";  // 沒有找到該用戶的預訂
    }
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}
?>
