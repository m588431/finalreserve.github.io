<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];  // 獲取當前登入用戶的ID
$current_time = date('Y-m-d H:i:s');  // 獲取當前時間

try {
    // 釋放過期的預約（過期的定義是 date + time 早於當前時間）
    $release_sql = "
        SELECT seat_id FROM reservations 
        WHERE CONCAT(date, ' ', time) < ?
    ";
    $stmt = $pdo->prepare($release_sql);
    $stmt->execute([$current_time]);
    $expired_reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($expired_reservations) {
        foreach ($expired_reservations as $reservation) {
            $seat_id = $reservation['seat_id'];

            // 刪除過期預訂記錄
            $delete_stmt = $pdo->prepare("DELETE FROM reservations WHERE seat_id = ?");
            $delete_stmt->execute([$seat_id]);

            // 將座位狀態更新為未預訂
            $update_seat_stmt = $pdo->prepare("UPDATE seats SET is_reserved = 0, reserved_by_user = NULL WHERE seat_id = ?");
            $update_seat_stmt->execute([$seat_id]);
        }
    }

    // 查詢所有座位和相應的預訂資訊
    $sql = "
        SELECT s.seat_id, s.is_reserved, r.user_id, r.date, r.time, r.people, r.phone, r.pre_order
        FROM seats s
        LEFT JOIN reservations r ON s.seat_id = r.seat_id
    ";

    // 執行查詢
    $stmt = $pdo->query($sql);
    $seats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 遍歷每個座位，並標記是否由當前使用者預訂
    foreach ($seats as &$seat) {
        // 判斷該座位是否由當前用戶預訂，並附加預訂的詳細資訊
        if ($seat['user_id']) {
            $seat['reserved_by_user'] = ($seat['user_id'] == $user_id) ? true : false;
        } else {
            // 如果沒有預訂資訊，清空相關欄位
            $seat['reserved_by_user'] = false;
            $seat['date'] = null;
            $seat['time'] = null;
            $seat['people'] = null;
            $seat['phone'] = null;
            $seat['pre_order'] = null;
        }
    }

    // 將座位資訊作為 JSON 返回給前端
    echo json_encode($seats);

} catch (PDOException $e) {
    // 處理查詢或連接錯誤
    echo json_encode(['error' => '資料查詢失敗: ' . $e->getMessage()]);
}
?>
