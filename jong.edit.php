<?php
include("header.php");

// การแสดงผลข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีค่า reserve_id ส่งมาหรือไม่
if (isset($_GET['reserve_id'])) {
    $reserve_id = $_GET['reserve_id'];

    // ดึงข้อมูลการจองจากฐานข้อมูล
    $sql = "SELECT * FROM reserve_tb WHERE reserve_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reserve_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // แสดงข้อมูลในฟอร์มเพื่อให้แก้ไข
        $row = $result->fetch_assoc();
        ?>
        <form action="jong.edit.php" method="post">
            <input type="hidden" name="reserve_id" value="<?php echo $row['reserve_id']; ?>">
            <label for="reserve_type">ประเภทการจอง:</label>
            <input type="text" name="reserve_type" value="<?php echo $row['reserve_type']; ?>"><br>

            <label for="reserve_name">ชื่อผู้จอง:</label>
            <input type="text" name="reserve_name" value="<?php echo $row['reserve_name']; ?>"><br>

            <label for="reserve_telphone">เบอร์โทรศัพท์:</label>
            <input type="text" name="reserve_telphone" value="<?php echo $row['reserve_telphone']; ?>"><br>

            <label for="reserve_address">ที่อยู่:</label>
            <input type="text" name="reserve_address" value="<?php echo $row['reserve_address']; ?>"><br>

            <label for="reserve_price">ราคา:</label>
            <input type="text" name="reserve_price" value="<?php echo $row['reserve_price']; ?>"><br>

            <label for="reserve_date">วันที่จอง:</label>
            <input type="date" name="reserve_date" value="<?php echo $row['reserve_date']; ?>"><br>

            <label for="reserve_time1">เวลาเริ่ม:</label>
            <input type="time" name="reserve_time1" value="<?php echo $row['reserve_time1']; ?>"><br>

            <label for="reserve_time2">เวลาสิ้นสุด:</label>
            <input type="time" name="reserve_time2" value="<?php echo $row['reserve_time2']; ?>"><br>

            <input type="submit" name="submit" value="บันทึกการแก้ไข">
        </form>
        <?php
    } else {
        echo "ไม่พบข้อมูลการจองที่ต้องการแก้ไข";
    }
    $stmt->close();
}

// อัปเดตข้อมูลในฐานข้อมูล
if (isset($_POST['submit'])) {
    $reserve_id = $_POST['reserve_id'];
    $reserve_type = $_POST['reserve_type'];
    $reserve_name = $_POST['reserve_name'];
    $reserve_telphone = $_POST['reserve_telphone'];
    $reserve_address = $_POST['reserve_address'];
    $reserve_price = $_POST['reserve_price'];
    $reserve_date = $_POST['reserve_date'];
    $reserve_time1 = $_POST['reserve_time1'];
    $reserve_time2 = $_POST['reserve_time2'];

    $sql = "UPDATE reserve_tb SET reserve_type=?, reserve_name=?, reserve_telphone=?, reserve_address=?, reserve_price=?, reserve_date=?, reserve_time1=?, reserve_time2=? WHERE reserve_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $reserve_type, $reserve_name, $reserve_telphone, $reserve_address, $reserve_price, $reserve_date, $reserve_time1, $reserve_time2, $reserve_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('การแก้ไขข้อมูลสำเร็จแล้ว');
                window.location.href = 'show.php';
              </script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
