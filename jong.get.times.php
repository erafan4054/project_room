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
$reserve_date = $_POST['date']; // รับค่าวันที่ที่ส่งมาจาก AJAX
$reserve_type = $_POST['reserve_type'];  // รับค่าประเภทห้องที่ส่งมาจาก AJAX

// ดึงข้อมูลเวลาที่จองแล้วตามประเภทห้องและวันที่เลือก
$sql = "SELECT reserve_time1, reserve_time2 FROM reserve_tb WHERE reserve_date = '$reserve_date' AND reserve_type = '$reserve_type'";
// สร้างคำสั่ง SQL เพื่อค้นหาช่วงเวลาที่จองแล้วตามประเภทห้องและวันที่
$result = $conn->query($sql); // รันคำสั่ง SQL และเก็บผลลัพธ์ในตัวแปร $result

$reservedTimes = array(); // สร้าง array ว่างสำหรับเก็บเวลาที่ถูกจองแล้ว

while ($row = $result->fetch_assoc()) { // วนลูปดึงข้อมูลจากผลลัพธ์ของการค้นหา
    $start_time = $row['reserve_time1']; // เวลาเริ่มต้นของการจองที่ดึงมา
    $end_time = $row['reserve_time2']; // เวลาสิ้นสุดของการจองที่ดึงมา

    // เพิ่มเวลาทุกๆ 30 นาทีในช่วงที่จองแล้ว
    $current_time = strtotime($start_time); // แปลงเวลาเริ่มต้นเป็น timestamp
    while ($current_time < strtotime($end_time)) { // วนลูปจนกว่าจะถึงเวลาสิ้นสุด
        $reservedTimes[] = date('H:i', $current_time); // เพิ่มเวลาในรูปแบบ HH:MM ลงใน array
        $current_time = strtotime('+30 minutes', $current_time); // เพิ่มเวลา 30 นาทีในแต่ละรอบ
    }
}


// ส่งข้อมูลเวลาออกไปในรูปแบบ JSON
echo json_encode($reservedTimes);
$conn->close();
?>
