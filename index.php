<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

$menu = "index";
include("header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/th.js'></script> <!-- เพิ่มภาษาไทย -->
</head>
<body>
    <div class="container">
        <h1>ปฏิทินการจองห้อง</h1>
        <div id='calendar'></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',   // ปฏิทินแบบรายเดือน
            locale: 'th',                  // ตั้งค่าเป็นภาษาไทย
            events: 'events.php',          // ไฟล์ PHP ที่ดึงข้อมูลการจอง
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            eventClick: function(info) {
                alert('การจอง: ' + info.event.title + '\nเริ่ม: ' + info.event.start + '\nถึง: ' + info.event.end);
            }
        });
        calendar.render();
    });
    </script>
    <script>$('#calendar').fullCalendar({
    events: 'events.php',  // ลิงก์ไปยัง PHP ที่ส่งข้อมูล event
    displayEventTime: true, // แสดงเวลา
    eventRender: function(event, element) {
        element.find('.fc-title').append("<br/>" + event.title); // แสดงชื่อและประเภทห้อง
    },
    ...
});
</script>
</body>
</html>
