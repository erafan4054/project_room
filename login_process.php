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

    $sql = "
    SELECT 
        user_tb.user_id, 
        user_tb.username, 
        user_tb.password, 
        user_tb.user_type, 
        user_tb.user_name, 
        user_type_tb.user_type_name 
    FROM 
        user_tb
    LEFT JOIN 
        user_type_tb 
    ON 
        user_tb.user_type = user_type_tb.user_type_id 
    WHERE 
        user_tb.username = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $db_username, $db_password, $user_type, $user_name, $user_type_name);
                if ($stmt->fetch()) {
                    // เปรียบเทียบรหัสผ่านแบบ plain text
                    if ($password === $db_password) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $db_username;
                        $_SESSION['user_type'] = $user_type;
                        $_SESSION['user_name'] = $user_name;
                        $_SESSION['user_type_name'] = $user_type_name;

                        if ($user_type_name == 'admin') {
                            header("location: index.php");
                        } elseif ($user_type_name == 'employee') {
                            header("location: employee/index.php");
                        } else {
                            echo "ประเภทผู้ใช้ไม่ถูกต้อง.";
                        }
                        $stmt->close();  // ปิด statement หลังการใช้งาน
                        $conn->close();  // ปิด connection หลังการใช้งาน
                        exit;
                    } else {
                        echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง.";
                    }
                }
            } else {
                echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง.";
            }
        } else {
            echo "อ๊ะ! บางอย่างผิดพลาด. กรุณาลองใหม่อีกครั้งในภายหลัง.";
        }
        $stmt->close();  // ปิด statement ถ้ายังไม่ได้ปิด
    }

    $conn->close();  // ปิด connection ถ้ายังไม่ได้ปิด
}
?>
