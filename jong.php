<?php
$menu = "jong";
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

// การลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = $conn->prepare("DELETE FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $delete_id);
    if ($sql->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $sql->close();
}

// การแก้ไขข้อมูล
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = $conn->prepare("SELECT * FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $edit_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $sql->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserve_id = intval($_POST['reserve_id']);
    $reserve_name = $conn->real_escape_string($_POST['reserve_name']);
    $reserve_time1 = $conn->real_escape_string($_POST['reserve_time1']);
    $reserve_time2 = $conn->real_escape_string($_POST['reserve_time2']);
    $reserve_type = $conn->real_escape_string($_POST['reserve_type']);
    $reserve_date = $conn->real_escape_string($_POST['reserve_date']);
    $reserve_telphone = $conn->real_escape_string($_POST['reserve_telphone']);
    $reserve_address = $conn->real_escape_string($_POST['reserve_address']);
    $reserve_price = $conn->real_escape_string($_POST['reserve_price']);

    if ($reserve_id > 0) {
        // Update existing record
        $sql = $conn->prepare("UPDATE reserve_tb SET reserve_name=?, reserve_time1=?, reserve_time2=?, reserve_type=?, reserve_date=?, reserve_telphone=?, reserve_address=?, reserve_price=? WHERE reserve_id=?");
        $sql->bind_param("ssssssssi", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_id);
    } else {
        // Insert new record
        $sql = $conn->prepare("INSERT INTO reserve_tb (reserve_name, reserve_time1, reserve_time2, reserve_type, reserve_date, reserve_telphone, reserve_address, reserve_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssssss", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price);
    }

    if ($sql->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $sql->close();
}

?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลลูกค้า</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate> 
                <div class="form-row">
                        <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                        <input type="hidden" name="reserve_id" value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ชื่อลูกค้า :</label>
                            <input type="text" class="form-control" name="reserve_name" value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">เบอร์โทร :</label>
                            <input type="text" class="form-control" name="reserve_telphone" value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ที่อยู่ :</label>
                            <input type="text" class="form-control" name="reserve_address" value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ราคาห้อง :</label>
                            <input type="text" class="form-control" name="reserve_price" value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>    
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">วันที่ :</label>
                            <input type="date" class="form-control" name="reserve_date" value="<?php echo isset($row['reserve_date']) ? $row['reserve_date'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>    
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                            <input type="time" class="form-control" name="reserve_time1" value="<?php echo isset($row['reserve_time1']) ? $row['reserve_time1'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                            <input type="time" class="form-control" name="reserve_time2" value="<?php echo isset($row['reserve_time2']) ? $row['reserve_time2'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ประเภทห้อง :</label>
                            <select name="reserve_type" class="form-control" id="reserve_type" required>
                                <option value="">เลือกห้อง</option>
                                <option value="ห้องล่าง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                                <option value="ห้องกลาง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                                <option value="ห้องใหญ่" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room selection -->
                    <div class="form-row">
                        <div class="col-md-12">
                            <h4>เลือกห้องซ้อม :</h4>
                            <div class="row">
                                <?php
                                $sql = "SELECT * FROM room_tb"; // ชื่อตาราง room_tb ที่เก็บข้อมูลห้อง
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($room = $result->fetch_assoc()) {
                                        echo '<div class="col-md-4">';
                                        echo '<div class="card" onclick="updateRoomType(\'' . $room['room_type'] . '\')">';
                                        echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                                        echo '<div class="card-body">';
                                        echo '<h5 class="card-type">' . $room['room_type'] . ' (' . $room['room_capacity'] . ' คน)</h5>';
                                        echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                                        echo '<p class="card-text">ราคา : ' . $room['room_price'] . ' /ชม.</p>';
                                        //echo '<p class="card-text">สถานะ : ' . $room[''] . '</p>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo "ไม่พบข้อมูลห้อง";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-danger" type="submit">บันทึก</button>
                    <button class="btn btn-secondary" type="button" onclick="window.location.href='show.php';">ยกเลิก</button>
                </form>                
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<?php include('footer.php'); ?>
<script>
$(function () {
  $(".datatable").DataTable();
  $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
  });
});

function updateRoomType(roomType) {
  document.getElementById('reserve_type').value = roomType;
}
</script>
</body>
</html>

<?php
$conn->close();
?>
