<?php
session_start(); // เริ่มต้น session สำหรับจัดการข้อมูลผู้ใช้ที่ล็อกอิน

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); 
    exit; 
}

// ตรวจสอบประเภทผู้ใช้ว่าคือ user หรือไม่ ถ้าไม่ใช่ให้เปลี่ยนเส้นทาง
if ($_SESSION['user_type_name'] !== 'admin') {
    header("Location: login.php"); 
    exit; 
}

$menu = "index";
include("header.php"); 

// เชื่อมต่อฐานข้อมูล
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "project_room"; 

$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); 
}

// ดึงข้อมูลการจองจากฐานข้อมูล
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_name, reserve_telphone, status 
        FROM reserve_tb";
$result = $conn->query($sql); // รันคำสั่ง SQL และเก็บผลลัพธ์ในตัวแปร $result

$events = []; // สร้าง array ว่างสำหรับเก็บข้อมูลการจองที่จะแสดงบน FullCalendar

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // แปลงปี พ.ศ. เป็น ค.ศ.
        $reserve_date = new DateTime($row['reserve_date']); // สร้าง DateTime object จากวันที่ที่ดึงมา
        $year_in_christian_era = $reserve_date->format('Y') - 543; // แปลงปี พ.ศ. เป็น ค.ศ. โดยลบ 543 ปี
        $reserve_date->setDate($year_in_christian_era, $reserve_date->format('m'), $reserve_date->format('d')); // ตั้งค่าปีใหม่ใน DateTime object

        // ตั้งค่าสีตามประเภทของห้อง
        $backgroundColor = '#9999FF'; // ค่าเริ่มต้น (สีน้ำเงิน สำหรับห้องล่าง)
        if ($row['reserve_type'] === 'ห้องใหญ่') {
            $backgroundColor = '#32CD32'; // สีเขียว สำหรับห้องใหญ่
        } elseif ($row['reserve_type'] === 'ห้องกลาง') {
            $backgroundColor = '#CC66FF'; // สีม่วง สำหรับห้องกลาง
        }

        // ตั้งค่าสีตามสถานะ
        $statusColor = ''; 
        if ($row['status'] === 'รอดำเนินการ') {
            $statusColor = 'red'; // สีแดงสำหรับรอดำเนินการ
        } elseif ($row['status'] === 'ดำเนินการแล้ว') {
            $statusColor = 'green'; // สีเขียวสำหรับดำเนินการแล้ว
        }

        // จัดการข้อมูลเป็นรูปแบบที่ FullCalendar ต้องการ
        $events[] = [
            'title' => '<strong>' . $row['reserve_time1'] . ' - ' . $row['reserve_time2'] . '</strong><br>' .
                    $row['reserve_name'] . ' - ' . $row['reserve_type'] . '<br>' .
                    '<span style="color:' . $statusColor . ';">' . $row['status'] . '</span><br>' .
                    $row['reserve_telphone'], // ข้อความที่จะแสดงบนปฏิทิน พร้อมเวลาจอง ชื่อผู้จอง และสถานะ
            'start' => $reserve_date->format('Y-m-d') . 'T' . $row['reserve_time1'], // วันและเวลาเริ่มต้นการจองในรูปแบบ ISO8601
            'end'   => $reserve_date->format('Y-m-d') . 'T' . $row['reserve_time2'], // วันและเวลาสิ้นสุดการจองในรูปแบบ ISO8601
            'backgroundColor' => $backgroundColor,  // สีพื้นหลังตามประเภทของห้อง
            'borderColor' => $backgroundColor, // สีขอบตามประเภทของห้อง
            'extendedProps' => [
                'telphone' => $row['reserve_telphone'],  // เพิ่มเบอร์โทรศัพท์เป็น property เสริม
                'name' => $row['reserve_name'],  // เพิ่มชื่อของลูกค้า
                'status' => $row['status']  // เพิ่มสถานะเป็น property เสริม
            ]
        ];
    }
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Calendar</title>

    <!-- นำเข้า FullCalendar CSS และ JS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <!-- นำเข้า FullCalendar ภาษาไทย -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/th.js"></script>

    <style>
      /* ตั้งค่าให้แถวมีความสูงสม่ำเสมอ */
      .fc-timegrid-slot {
        min-height: 50px; /* ปรับความสูงของแถว */
      }
    </style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar'); // เลือก element ที่มี id เป็น 'calendar' สำหรับแสดงปฏิทิน

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',  // ตั้งค่าเริ่มต้นให้แสดงมุมมองรายสัปดาห์ (สามารถเปลี่ยนเป็น 'timeGridDay' เพื่อมุมมองรายวันได้)
            locale: 'th',  // ตั้งค่าภาษาไทยให้กับปฏิทิน
            events: <?php echo json_encode($events); ?>,  // ส่งข้อมูล events ที่ได้จาก PHP มาใช้ในปฏิทิน
            
            eventContent: function(arg) {
                let arrayOfDomNodes = [];
                let containerEl = document.createElement('div'); // สร้าง element div สำหรับใส่เนื้อหา
                containerEl.innerHTML = arg.event.title; // แปลง HTML ที่ได้จากข้อมูล event เป็นเนื้อหาที่จะแสดงในปฏิทิน
                arrayOfDomNodes.push(containerEl); // เพิ่ม element div ลงใน array
                return { domNodes: arrayOfDomNodes }; // ส่งกลับ array ของ DOM nodes เพื่อแสดงผลในปฏิทิน
            },
            
            slotLabelFormat: [
                { hour: '2-digit', minute: '2-digit', meridiem: false, prefix: 'เวลา ' }  // กำหนดรูปแบบการแสดงผลเวลา โดยเพิ่มคำว่า "เวลา" ข้างหน้า
            ],
            
            allDaySlot: false,  // ซ่อนช่องเวลาที่เป็นช่วงทั้งวัน (ไม่ต้องการแสดงการจองที่เป็นแบบตลอดวัน)
            
            eventClick: function(info) {
                // เมื่อคลิกที่อีเวนต์ในปฏิทิน จะสร้างข้อความเพื่อแสดงรายละเอียดการจอง
                var message = 'ชื่อลูกค้า : ' + info.event.extendedProps.name + '\n' +  // ชื่อผู้จอง
                              'เวลา : ' + info.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) +
                              ' - ' + info.event.end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + // เวลาที่เริ่มและสิ้นสุด
                              '\nสถานะ : ' + info.event.extendedProps.status +  // สถานะของการจอง
                              '\nเบอร์โทร : ' + info.event.extendedProps.telphone; // เบอร์โทรศัพท์ผู้จอง
                alert(message); // แสดงข้อความในกล่องข้อความแจ้งเตือน
            },
            
            headerToolbar: {
                left: 'prev,next today',  // ปุ่มย้อนกลับ ถัดไป และปุ่ม "วันนี้" จะแสดงที่ด้านซ้าย
                center: 'title', // แสดงชื่อปฏิทินที่ตรงกลาง
                right: 'timeGridDay,timeGridWeek,dayGridMonth'  // ปุ่มสลับมุมมองรายวัน รายสัปดาห์ และรายเดือนที่ด้านขวา
            },
            
            slotMinTime: "10:00:00",  // ตั้งเวลาเริ่มต้นของปฏิทินที่ 10:00 น.
            slotMaxTime: "21:00:00",  // ตั้งเวลาสิ้นสุดของปฏิทินที่ 21:00 น.
            slotDuration: "00:30:00",  // ตั้งระยะเวลาช่องเวลาแต่ละช่องเป็น 30 นาที
            height: 'auto',  // ตั้งความสูงของปฏิทินให้ปรับตามเนื้อหา
            expandRows: true // ปรับแถวให้ขยายเพื่อให้พอดีกับความสูงของปฏิทิน
        });

        calendar.render(); // แสดงปฏิทินบนหน้าจอ
    });
</script>

</head>
<body>

<!-- แสดงปฏิทิน -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon far fa-calendar-alt"></i> ตารางปฏิทินการจอง</h1>
      </div>
      <div class="col-md-6 text-right">
        <a href="jong.php?action=add" class="btn btn-danger">
          <i class="fas fa-laptop-medical"></i>  จองห้องซ้อมดนตรี
        </a>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<section class="content">
  <div class="container-fluid">
    <div id="calendar"></div> <!-- ตำแหน่งที่จะวางปฏิทิน -->
  </div>
</section>

<?php include('footer.php'); ?>

</body>
</html>
