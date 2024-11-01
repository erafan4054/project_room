<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจาก AJAX
$reserve_date = $_POST['date'];
$reserve_type = $_POST['reserve_type'];  // เพิ่มประเภทห้อง

// ดึงข้อมูลเวลาที่จองแล้วตามประเภทห้องและวันที่เลือก
$sql = "SELECT reserve_time1, reserve_time2 FROM reserve_tb WHERE reserve_date = '$reserve_date' AND reserve_type = '$reserve_type'";
$result = $conn->query($sql);

$reservedTimes = array();
while ($row = $result->fetch_assoc()) {
    $start_time = $row['reserve_time1'];
    $end_time = $row['reserve_time2'];

    // เพิ่มเวลาทุกๆ 30 นาทีในช่วงที่จองแล้ว
    $current_time = strtotime($start_time);
    while ($current_time < strtotime($end_time)) {
        $reservedTimes[] = date('H:i', $current_time);
        $current_time = strtotime('+30 minutes', $current_time);
    }
}

// ส่งข้อมูลเวลาออกไปในรูปแบบ JSON
echo json_encode($reservedTimes);
$conn->close();
?>
