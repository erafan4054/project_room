<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ลบข้อมูลพนักงาน
if (isset($_GET['delete'])) {
    $employee_id = $_GET['delete'];
    $sql = "DELETE FROM employee_tb WHERE employee_id='$employee_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('ลบเรียบร้อยแล้ว');
                window.location.href = 'employee.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>