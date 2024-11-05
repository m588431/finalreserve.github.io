<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "請先登入";
    exit;
}

$user_id = $_SESSION['user_id'];
$seat_id = isset($_POST['seat']) ? $_POST['seat'] : null;
$date = isset($_POST['date']) ? $_POST['date'] : null;
$time = isset($_POST['time']) ? $_POST['time'] : null;
$people = isset($_POST['people']) ? $_POST['people'] : null;  // 檢查 'people' 是否存在
$phone = isset($_POST['phone']) ? $_POST['phone'] : null;  // 檢查 'phone' 是否存在
$pre_order = isset($_POST['pre_order']) && $_POST['pre_order'] == '1' ? 1 : 0;  // 設定 'pre_order'

// 檢查必填字段
if (empty($seat_id) || empty($date) || empty($time) || $people === null || $phone === null) {
    echo "請確保所有字段都已填寫";
    exit;
}

// 檢查使用者是否已經有預訂
$checkReservation = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ?");
$checkReservation->execute([$user_id]);
$existingReservation = $checkReservation->fetch(PDO::FETCH_ASSOC);

if ($existingReservation) {
    echo "您已經有一個預訂，無法再預訂其他座位。";
    exit;
}

// 檢查座位是否已被預訂
$stmt = $pdo->prepare("SELECT is_reserved FROM seats WHERE seat_id = ?");
$stmt->execute([$seat_id]);
$seat = $stmt->fetch();

if ($seat['is_reserved']) {
    echo "座位已被預訂";
    exit;
}

// 插入預訂資訊並更新座位狀態
$stmt = $pdo->prepare("INSERT INTO reservations (user_id, seat_id, date, time, people, phone, pre_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $seat_id, $date, $time, $people, $phone, $pre_order]);

// 更新座位狀態為已預訂
$stmt = $pdo->prepare("UPDATE seats SET is_reserved = 1, reserved_by_user = ? WHERE seat_id = ?");
$stmt->execute([$user_id, $seat_id]);

echo "success";
?>
