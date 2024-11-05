<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (phone, password) VALUES (?, ?)");
        $stmt->execute([$phone, $password]);
        echo "註冊成功";
    } catch (PDOException $e) {
        echo "註冊失敗：" . $e->getMessage();
    }
}
?>
