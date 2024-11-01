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

// ตรวจสอบการลบข้อมูล
if (isset($_GET['delete'])) {
    $room_id = $_GET['delete'];
    $sql = "DELETE FROM room_tb WHERE room_id='$room_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('ลบออกเรียบร้อยแล้ว');
                window.location.href = 'room.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
