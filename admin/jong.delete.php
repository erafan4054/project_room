<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// การลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = $conn->prepare("DELETE FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $delete_id);
    
    if ($sql->execute()) {
        echo "<script>alert('ลบข้อมูลเรียบร้อยแล้ว');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $sql->close();
}

$conn->close();
?>
