<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname); // สร้างการเชื่อมต่อกับฐานข้อมูล

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // แสดงข้อความและหยุดการทำงานหากการเชื่อมต่อล้มเหลว
}

// ตรวจสอบว่ามีการส่งค่า reserve_type และ reserve_date หรือไม่
if (isset($_POST['reserve_type']) && isset($_POST['reserve_date'])) {
    $reserve_type = $_POST['reserve_type']; // รับค่าประเภทห้องที่ส่งมาจากฟอร์มหรือ AJAX
    $reserve_date = $_POST['reserve_date']; // รับค่าวันที่ที่ส่งมาจากฟอร์มหรือ AJAX

    // ดึงเวลาที่ถูกจองแล้วตามประเภทห้องและวันที่
    $sql = "SELECT reserve_time1, reserve_time2 FROM reserve_tb 
            WHERE reserve_type = '$reserve_type' 
            AND reserve_date = '$reserve_date'"; // สร้างคำสั่ง SQL เพื่อค้นหาช่วงเวลาที่จองแล้วตามประเภทห้องและวันที่
    $result = $conn->query($sql); // รันคำสั่ง SQL และเก็บผลลัพธ์ในตัวแปร $result

    $reserved_times = []; // สร้าง array ว่างสำหรับเก็บช่วงเวลาที่ถูกจองแล้ว

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reserved_times[] = [
                'start' => substr($row['reserve_time1'], 0, 5), // ดึงเฉพาะส่วนของเวลาในรูปแบบ HH:MM
                'end' => substr($row['reserve_time2'], 0, 5) // ดึงเฉพาะส่วนของเวลาในรูปแบบ HH:MM
            ]; // เพิ่มช่วงเวลาที่จองแล้วลงใน array
        }
    }

    // สร้างตัวเลือกเวลาสำหรับเวลาเริ่มและเวลาถึง
    $time_options_start = '<option value="">--:--</option>';  // ค่าเริ่มต้น สำหรับเวลาเริ่ม
    $time_options_end = '<option value="">--:--</option>';    // ค่าเริ่มต้น สำหรับเวลาถึง

    for ($hour = 10; $hour <= 20; $hour++) { // วนลูปตั้งแต่ 10:00 น. ถึง 20:00 น.
        for ($minute = 0; $minute < 60; $minute += 30) { // วนลูปทุก 30 นาที (00 และ 30 นาที)
            $time = sprintf('%02d:%02d', $hour, $minute); // จัดรูปแบบเวลาให้เป็น HH:MM เช่น 10:00, 10:30
            $is_reserved = false; // ตั้งค่าเริ่มต้นว่าช่วงเวลานี้ยังไม่ถูกจอง

            // ตรวจสอบว่าเวลานั้นถูกจองหรือยัง
            foreach ($reserved_times as $reserved) {
                // ถ้าเวลาเริ่มอยู่ในช่วงเวลาที่ถูกจองแล้ว
                if ($time >= $reserved['start'] && $time < $reserved['end']) {
                    $is_reserved = true; // ตั้งค่าว่าเวลานี้ถูกจองแล้ว
                    break; // หยุดการตรวจสอบเพิ่มเติม
                }
            }

            // กำหนดสีสำหรับเวลาว่างหรือเวลาที่จองแล้ว
            $color = $is_reserved ? 'red' : 'green'; // ใช้สีแดงถ้าถูกจองแล้ว และสีเขียวถ้าว่าง

            // ตัวเลือกสำหรับเวลาเริ่ม
            $time_options_start .= "<option value='$time' style='color: $color;'>$time</option>";

            // ตัวเลือกสำหรับเวลาถึง (เพื่อให้เลือกเวลาถึงที่ตามหลังเวลาที่เลือกเริ่ม)
            $time_options_end .= "<option value='$time' style='color: $color;'>$time</option>";
        }
    }

    // ส่งข้อมูลกลับไปในรูปแบบ JSON
    echo json_encode([
        'time_options_start' => $time_options_start, // ส่ง HTML ของตัวเลือกเวลาเริ่ม
        'time_options_end' => $time_options_end // ส่ง HTML ของตัวเลือกเวลาถึง
    ]);
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
