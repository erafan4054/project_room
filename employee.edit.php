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

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // การอัพเดทข้อมูล
        $employee_id = $_POST['employee_id'];
        $employee_name = $_POST['employee_name'];
        $employee_email = $_POST['employee_email'];
        $employee_telphone = $_POST['employee_telphone'];
        $employee_username = $_POST['employee_username'];
        $employee_password = $_POST['employee_password'];

        $sql = "UPDATE employee_tb SET employee_name='$employee_name', employee_email='$employee_email', employee_telphone='$employee_telphone', employee_username='$employee_username', employee_password='$employee_password' WHERE employee_id='$employee_id'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('แก้ไขข้อมูลสำเร็จแล้ว');
                    window.location.href = 'employee.php';
                  </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // การเพิ่มข้อมูล
        $employee_name = $_POST['employee_name'];
        $employee_email = $_POST['employee_email'];
        $employee_telphone = $_POST['employee_telphone'];
        $employee_username = $_POST['employee_username'];
        $employee_password = $_POST['employee_password'];

        $sql = "INSERT INTO employee_tb (employee_name, employee_email, employee_telphone, employee_username, employee_password )
                VALUES ('$employee_name', '$employee_email', '$employee_telphone', '$employee_username', '$employee_password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('บันทึกใหม่สำเร็จแล้ว');
                    window.location.href = 'employee.php';
                  </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// ดึงข้อมูลจากฐานข้อมูลสำหรับการแก้ไข
if (isset($_GET['edit'])) {
    $employee_id = $_GET['edit'];
    $sql = "SELECT employee_name, employee_email, employee_telphone, employee_username, employee_password FROM employee_tb WHERE employee_id='$employee_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    }
}

$conn->close();
?>
