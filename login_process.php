<?php
session_start();
require 'db.php';  // รวมไฟล์การเชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo "กรุณากรอกข้อมูลให้ครบทุกช่อง.";
        exit;
    }

    // เตรียมคำสั่งเลือก
    $sql = "SELECT id, username, password FROM users WHERE username = ? AND password = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username, $password);
                if ($stmt->fetch()) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id'] = $id;
                    $_SESSION['username'] = $username;
                    header("location: admin/index.php");
                }
            } else {
                echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง.";
            }
        } else {
            echo "อ๊ะ! บางอย่างผิดพลาด. กรุณาลองใหม่อีกครั้งในภายหลัง.";
        }

        $stmt->close();
    }
}

$conn->close();
?>
