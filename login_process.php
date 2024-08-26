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
    $sql = "SELECT user_id, username, password, user_type FROM users WHERE username = ? AND password = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $username, $password, $user_type);
                if ($stmt->fetch()) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['user_type'] = $user_type;

                    // ตรวจสอบ user_type และเปลี่ยนเส้นทางตามประเภท
                    if ($user_type == 'admin') {
                        header("location: admin/index.php");
                    } elseif ($user_type == 'user') {
                        header("location: index.php");
                    } else {
                        echo "ประเภทผู้ใช้ไม่ถูกต้อง.";
                    }
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
