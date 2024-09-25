<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

// สร้างการเชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลการจองจากฐานข้อมูล
$sql = "SELECT reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_name FROM reserve_tb";
$result = $conn->query($sql);

$events = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // ตั้งค่าประเภทห้องและกำหนดสี
        $color = '';  // เริ่มต้นเป็นค่าว่าง
        
        switch ($row['reserve_type']) {
            case 'ล่าง':
                $color = 'orange';  // สีสำหรับห้องประเภทล่าง
                break;
            case 'บน':
                $color = 'green';   // สีสำหรับห้องประเภทบน
                break;
            case 'กลาง':
                $color = 'blue';    // สีสำหรับห้องประเภทกลาง
                break;
            default:
                $color = 'gray';    // สีสำหรับประเภทอื่นๆ หรือไม่ได้กำหนด
                break;
        }

        // จัดรูปแบบข้อมูลการจองให้เป็น JSON สำหรับ FullCalendar
        $events[] = array(
            'title' => $row['reserve_name'] . " จองห้อง " . $row['reserve_type'],  // แสดงชื่อและประเภทห้อง
            'start' => $row['reserve_date'] . "T" . $row['reserve_time1'],        // เวลาเริ่ม
            'end'   => $row['reserve_date'] . "T" . $row['reserve_time2'],         // เวลาถึง
            'color' => $color  // กำหนดสีตามประเภทห้อง
        );
    }
}

// ส่งข้อมูลการจองกลับในรูปแบบ JSON
echo json_encode($events);
?>
