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
        $user_id = $_POST['user_id'];
        $user_name = $_POST['user_name'];
        $user_email = $_POST['user_email'];
        $user_telphone = $_POST['user_telphone'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "UPDATE users SET user_name='$user_name', user_email='$user_email', user_telphone='$user_telphone', username='$username', password='$password' WHERE user_id='$user_id'";

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
        $user_id = $_POST['user_id'];
        $user_name = $_POST['user_name'];
        $user_email = $_POST['user_email'];
        $user_telphone = $_POST['user_telphone'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "INSERT INTO users (`user_name`, `user_email`, `user_telphone`, `username`, `password`, `user_type`)
                VALUES ('$user_name', '$user_email', '$user_telphone', '$username', '$password','admin')";

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
    $user_id = $_GET['edit'];
    $sql = "SELECT user_name, user_email, user_telphone, username, password FROM users WHERE user_id='$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    }
}

$conn->close();
?>
