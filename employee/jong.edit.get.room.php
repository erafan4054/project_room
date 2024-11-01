<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "project_room"; 

$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); 
}
if (isset($_POST['reserve_type'])) {
    $reserve_type = $_POST['reserve_type']; // รับค่าประเภทห้องที่ส่งมาจากฟอร์มหรือ AJAX

    // ใช้ JOIN เพื่อดึงราคาห้องจาก room_tb โดยใช้ชื่อห้อง
    $sql = "SELECT room_tb.room_price 
            FROM room_tb 
            INNER JOIN room_type_tb ON room_tb.room_type = room_type_tb.room_type_id 
            WHERE room_type_tb.room_type_name = '$reserve_type'"; 

    $result = $conn->query($sql); // รันคำสั่ง SQL และเก็บผลลัพธ์ในตัวแปร $result

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // ดึงแถวข้อมูลจากผลลัพธ์ของการค้นหา
        $room_price = $row['room_price']; // ดึงราคาห้องจากแถวข้อมูล

        // สร้างตัวเลือกเวลาสำหรับเวลาเริ่มและเวลาถึง
        $time_options = ''; // สร้างตัวแปรว่างเพื่อเก็บ HTML ของตัวเลือกเวลา
        for ($hour = 10; $hour <= 20; $hour++) { // วนลูปตั้งแต่ 10:00 น. ถึง 20:00 น.
            for ($minute = 0; $minute < 60; $minute += 30) { // วนลูปทุก 30 นาที (00 และ 30 นาที)
                $time = sprintf('%02d:%02d', $hour, $minute); // จัดรูปแบบเวลาให้เป็น HH:MM เช่น 10:00, 10:30
                $time_options .= "<option value='$time'>$time</option>"; // เพิ่มตัวเลือกเวลาในรูปแบบ HTML
            }
        }

        // ส่งข้อมูลเป็น JSON กลับไปยัง AJAX
        echo json_encode([
            'room_price' => $room_price, // ส่งราคาห้องกลับไป
            'time_options' => $time_options // ส่ง HTML ของตัวเลือกเวลา
        ]);
    } else {
        // กรณีไม่พบข้อมูลในฐานข้อมูล
        echo json_encode([
            'room_price' => 0,
            'time_options' => ''
        ]);
    }
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
