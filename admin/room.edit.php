<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการแก้ไขข้อมูล
if (isset($_GET['edit'])) {
    $room_id = $_GET['edit'];
    $sql = "SELECT room_type, room_price, room_detail, room_status FROM room_tb WHERE room_id='$room_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<script>
            document.getElementById('room_id').value = '$room_id';
            document.getElementsByName('room_type')[0].value = '".$row['room_type']."';
            document.getElementsByName('room_price')[0].value = '".$row['room_price']."';
            document.getElementsByName('room_detail')[0].value = '".$row['room_detail']."';
            document.getElementsByName('room_status')[0].value = '".$row['room_status']."';
        </script>";
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
