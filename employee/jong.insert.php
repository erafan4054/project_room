<?php 
include("header.php");

// การแสดงผลข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
$reserve_date = isset($_POST['reserve_date']) ? $_POST['reserve_date'] : '';
$reserve_time1 = isset($_POST['reserve_time1']) ? $_POST['reserve_time1'] : '';
$reserve_time2 = isset($_POST['reserve_time2']) ? $_POST['reserve_time2'] : '';
$reserve_name = isset($_POST['reserve_name']) ? $_POST['reserve_name'] : '';
$reserve_telphone = isset($_POST['reserve_telphone']) ? $_POST['reserve_telphone'] : '';
$reserve_type = isset($_POST['reserve_type']) ? $_POST['reserve_type'] : '';
$reserve_price = isset($_POST['reserve_price']) ? $_POST['reserve_price'] : '';
$reserve_total = isset($_POST['reserve_total']) ? $_POST['reserve_total'] : '';
$reserve_hour = isset($_POST['reserve_hour']) ? $_POST['reserve_hour'] : ''; 

// สร้างคำสั่ง SQL สำหรับการแทรกข้อมูล
$sql = "INSERT INTO reserve_tb (reserve_date, reserve_time1, reserve_time2, reserve_name, reserve_telphone, reserve_type, reserve_price, reserve_total, reserve_hour) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";


// เตรียมคำสั่ง SQL
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// ผูกค่าตัวแปรกับคำสั่ง SQL
$stmt->bind_param("sssssssss", $reserve_date, $reserve_time1, $reserve_time2, $reserve_name, $reserve_telphone, $reserve_type, $reserve_price, $reserve_total, $reserve_hour);

// รันคำสั่ง SQL
if ($stmt->execute()) {
    echo "<script>
                    alert('บันทึกใหม่สำเร็จแล้ว');
                    window.location.href = 'show.php';
                  </script>";
} else {
    echo "เกิดข้อผิดพลาด: " . $stmt->error;
}


// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$conn->close();
?>
