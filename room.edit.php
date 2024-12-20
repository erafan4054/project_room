<?php
$menu = "room.edit";
include("menu_session.php");  // ดึงข้อมูลผู้ใช้จาก session
include("header.php");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'] ?? null;
    $room_type = $_POST['room_type'];
    $room_price = $_POST['room_price'];
    $room_capacity = $_POST['room_capacity'];
    $room_detail = $_POST['room_detail'];
    $room_img = $_FILES['room_img']['name'] ?? null;
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($room_img);

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // อัปโหลดไฟล์รูปภาพ
    if ($room_img && move_uploaded_file($_FILES["room_img"]["tmp_name"], $target_file)) {
        if ($room_id) {
            // การอัพเดทข้อมูล
            $sql = "UPDATE room_tb SET room_type='$room_type', room_price='$room_price', room_capacity='$room_capacity', room_detail='$room_detail', room_img='$room_img' WHERE room_id='$room_id'";
        } else {
            // การเพิ่มข้อมูล
            $sql = "INSERT INTO room_tb (room_type, room_price, room_capacity, room_detail, room_img) VALUES ('$room_type', '$room_price','$room_capacity', '$room_detail', '$room_img')";
        }

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                alert('บันทึกข้อมูลใหม่เรียบร้อยแล้ว');
                window.location.href = 'room.php';
            </script>";

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "ขออภัย เกิดข้อผิดพลาดในการอัปโหลดไฟล์ของคุณ.";
    }
}

// ตรวจสอบการแก้ไขข้อมูล
$edit_id = $_GET['edit'] ?? null;
if ($edit_id) {
    $sql = "SELECT room_type, room_price, room_capacity, room_detail FROM room_tb WHERE room_id='$edit_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('room_id').value = '$edit_id';
                document.getElementsByName('room_type')[0].value = '".htmlspecialchars($row['room_type'], ENT_QUOTES)."';
                document.getElementsByName('room_price')[0].value = '".htmlspecialchars($row['room_price'], ENT_QUOTES)."';
                document.getElementsByName('room_capacity')[0].value = '".htmlspecialchars($row['room_capacity'], ENT_QUOTES)."';
                document.getElementsByName('room_detail')[0].value = '".htmlspecialchars($row['room_detail'], ENT_QUOTES)."';
            });
        </script>";
    }
}

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT room_id, room_type, room_capacity, room_price, room_detail, room_img FROM room_tb";
$result = $conn->query($sql);

// ดึงข้อมูลประเภทห้องจากตาราง room_type_tb
$sql_room_type = "SELECT room_type_id, room_type_name FROM room_type_tb";
$result_room_type = $conn->query($sql_room_type);


?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <h1><i class="nav-icon fas fa-edit"></i> แก้ไขข้อมูลห้องดนตรี</h1>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
  <div class="card">
    <div class="card-header card-navy card-outline">
      <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate onsubmit="return validateForm()">  
        <div class="form-row">
          <input type="hidden" name="room_id" id="room_id"> 
          <div class="col-md-3 mb-3">  
            <label for="validationCustom01">ประเภทห้อง :</label>
                <select class="form-control" name="room_type" required>
                  <option value="">-- กรุณาเลือกประเภทห้อง --</option>
                    <?php
                    if ($result_room_type && $result_room_type->num_rows > 0) {
                        // แสดงผลข้อมูลจากตาราง room_type_tb
                        while ($row_room_type = $result_room_type->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row_room_type['room_type_id'], ENT_QUOTES) . '">';
                            echo htmlspecialchars($row_room_type['room_type_name'], ENT_QUOTES);
                            echo '</option>';
                        }
                    } else {
                        echo '<option value="">ไม่มีข้อมูลประเภทห้อง</option>';
                    }
                    ?>
                </select>
                <div class="invalid-feedback">**กรุณาเลือกประเภทห้อง</div>
            </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">ราคาห้อง/ชม. :</label>
            <input type="text" class="form-control" name="room_price" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">ความจุ/คน :</label>
            <input type="number" class="form-control" name="room_capacity" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">รูปภาพห้อง :</label>
            <input type="file" class="form-control" name="room_img">
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-12 mb-3">
            <label for="validationCustomUsername">รายละเอียดห้อง :</label>
            <textarea name="room_detail" class="form-control" rows="5" required></textarea>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
        </div>
        <button class="btn btn-danger" type="submit" name="submit">บันทึกการแก้ไข</button>
        <a href="room.php" class="btn btn-secondary">กลับไปหน้ารายการ</a>
      </form>
    </div>
  </div>
</section>

<script>
// ฟังก์ชันกรอกข้อมูลให้ครบทุกช่อง
function validateForm() {
    // ตรวจสอบว่าฟิลด์แต่ละช่องถูกกรอกครบถ้วนหรือไม่
    const roomType = document.getElementsByName('room_type')[0].value.trim();
    const roomPrice = document.getElementsByName('room_price')[0].value.trim();
    const roomCapacity = document.getElementsByName('room_capacity')[0].value.trim();
    const roomDetail = document.getElementsByName('room_detail')[0].value.trim();
    
    // ตรวจสอบว่าทุกฟิลด์มีข้อมูล
    if (!roomType || !roomPrice || !roomCapacity || !roomDetail) {
        alert('กรุณากรอกข้อมูลให้ครบทุกช่อง');
        return false; // หยุดการส่งข้อมูลถ้ายังกรอกไม่ครบ
    }

    return true; // ส่งข้อมูลเมื่อกรอกครบทุกฟิลด์
}


function resetForm() {
    document.getElementById('room_id').value = '';
    document.getElementsByName('room_type')[0].value = '';
    document.getElementsByName('room_price')[0].value = '';
    document.getElementsByName('room_capacity')[0].value = '';
    document.getElementsByName('room_detail')[0].value = '';
    document.getElementsByName('room_img')[0].value = '';
}
</script>

<?php include('footer.php'); ?>

<?php
$conn->close();
?>