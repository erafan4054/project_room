<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบประเภทผู้ใช้ว่าคือ user หรือไม่ ถ้าไม่ใช่ให้เปลี่ยนเส้นทาง
if ($_SESSION['user_type_name'] !== 'employee') {
    header("Location: login.php"); // ไปยังหน้า "ไม่มีสิทธิ์เข้าถึง"
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
$result = $conn->query($sql);

$events = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // แปลงปี พ.ศ. เป็น ค.ศ.
        $reserve_date = new DateTime($row['reserve_date']);
        $year_in_christian_era = $reserve_date->format('Y') - 543;
        $reserve_date->setDate($year_in_christian_era, $reserve_date->format('m'), $reserve_date->format('d'));

        // ตั้งค่าสีตามประเภทของห้อง
        $backgroundColor = '#9999FF'; // ค่าเริ่มต้น (สีน้ำเงิน สำหรับห้องล่าง)
        if ($row['reserve_type'] === 'ห้องใหญ่') {
            $backgroundColor = '#32CD32'; // สีเขียว สำหรับห้องใหญ่
        } elseif ($row['reserve_type'] === 'ห้องกลาง') {
            $backgroundColor = '#CC66FF'; // สีม่วง สำหรับห้องกลาง
        }

        // ตั้งค่าสีตามสถานะ
        $statusColor = ''; 
        if ($row['status'] === 'รอดำเนินการ') {  // แก้จาก "(รอดำเนินการ)" เป็น "รอดำเนินการ"
            $statusColor = 'red'; // สีแดงสำหรับรอดำเนินการ
        } elseif ($row['status'] === 'ดำเนินการแล้ว') {  // แก้จาก "(เนินการแล้ว)" เป็น "ดำเนินการแล้ว"
            $statusColor = 'green'; // สีเขียวสำหรับดำเนินการแล้ว
        }

        // จัดการข้อมูลเป็นรูปแบบที่ FullCalendar ต้องการ
        $events[] = [
            'title' => '<strong>' . $row['reserve_time1'] . ' - ' . $row['reserve_time2'] . '</strong><br>' .
                    $row['reserve_name'] . ' - ' . $row['reserve_type'] . '<br>' .
                    '<span style="color:' . $statusColor . ';">' . $row['status'] . '</span><br>' .
                    $row['reserve_telphone'],
            'start' => $reserve_date->format('Y-m-d') . 'T' . $row['reserve_time1'],
            'end'   => $reserve_date->format('Y-m-d') . 'T' . $row['reserve_time2'],
            'backgroundColor' => $backgroundColor,  // สีตามประเภทของห้อง
            'borderColor' => $backgroundColor, // สีขอบตามประเภทของห้อง
            'extendedProps' => [
                'telphone' => $row['reserve_telphone'],  // เพิ่มเบอร์โทรศัพท์เป็น property เสริม
                'name' => $row['reserve_name'],  // เพิ่มชื่อของลูกค้า
                'status' => $row['status']  // เพิ่มสถานะเป็น property เสริม
            ]
        ];
    }
}

$conn->close();
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
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',  // (timeGridDay) มุมมองรายวัน, (timeGridWeek) มุมมองรายสัปดาห์
            locale: 'th',  // ตั้งค่าภาษาไทย
            events: <?php echo json_encode($events); ?>,  // ส่งข้อมูล events จาก PHP
            eventContent: function(arg) {
                let arrayOfDomNodes = [];
                let containerEl = document.createElement('div');
                containerEl.innerHTML = arg.event.title; // แปลง HTML ที่เราสร้างเป็นเนื้อหา
                arrayOfDomNodes.push(containerEl);
                return { domNodes: arrayOfDomNodes };
            },
            slotLabelFormat: [
                { hour: '2-digit', minute: '2-digit', meridiem: false, prefix: 'เวลา ' }  // เพิ่มคำว่า "เวลา"
            ],
            allDaySlot: false,  // ซ่อนช่วงเวลาทั้งวัน
            eventClick: function(info) {
                var message = 'ชื่อลูกค้า : ' + info.event.extendedProps.name + '\n' +
                              'เวลา : ' + info.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) +
                              ' - ' + info.event.end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) +
                              '\nสถานะ : ' + info.event.extendedProps.status +
                              '\nเบอร์โทร : ' + info.event.extendedProps.telphone;
                alert(message);
            },
            headerToolbar: {
                left: 'prev,next today',  
                center: 'title',
                right: 'timeGridDay,timeGridWeek,dayGridMonth'  
            },
            slotMinTime: "10:00:00",  
            slotMaxTime: "21:00:00",  
            slotDuration: "00:30:00",  
            height: 'auto',
            expandRows: true
        });

        calendar.render();
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
