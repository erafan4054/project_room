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

// รับค่าจากฟอร์มหลังจากส่งข้อมูล
$reserve_date = isset($_POST['reserve_date']) ? $_POST['reserve_date'] : '';
$reserve_time1 = isset($_POST['reserve_time1']) ? $_POST['reserve_time1'] : '';
$reserve_time2 = isset($_POST['reserve_time2']) ? $_POST['reserve_time2'] : '';
$reserve_type = isset($_POST['reserve_type']) ? $_POST['reserve_type'] : '';

// สร้างคำสั่ง SQL สำหรับการค้นหา
$sql = "SELECT * FROM reserve_tb WHERE 1=1";

// เพิ่มเงื่อนไขการค้นหาตามค่าที่ได้จากฟอร์ม
if (!empty($reserve_date)) {
    $sql .= " AND reserve_date = '$reserve_date'";
}
if (!empty($reserve_time1)) {
    $sql .= " AND reserve_time1 >= '$reserve_time1'";
}
if (!empty($reserve_time2)) {
    $sql .= " AND reserve_time2 <= '$reserve_time2'";
}
if (!empty($reserve_type)) {
    $sql .= " AND reserve_type = '$reserve_type'";
}

// รันคำสั่ง SQL และดึงข้อมูล
$result = mysqli_query($conn, $sql);
?>

<style>
.card {
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 4px 4px 20px rgba(0, 0, 0, 0.2);
}

.card-img-top {
    border-radius: 8px 8px 0 0;
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
}

.card-body {
    padding: 15px;
}

.card-type {
    font-size: 18px;
    font-weight: bold;
}

.card-text {
    color: #666;
}
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลลูกค้า</h1>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card1">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>

                <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                <input type="hidden" name="reserve_id"
                    value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">

                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">วันที่ :</label>
                        <input type="date" class="form-control" name="reserve_date" value="<?php echo $reserve_date; ?>"
                            required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                        <input type="time" class="form-control" name="reserve_time1"
                            value="<?php echo $reserve_time1; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                        <input type="time" class="form-control" name="reserve_time2"
                            value="<?php echo $reserve_time2; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom04">ประเภทห้อง :</label>
                        <select name="reserve_type" class="form-control" id="reserve_type" required
                            onchange="this.form.submit();">
                            <option value="">เลือกห้อง</option>
                            <option value="ห้องล่าง" <?php echo ($reserve_type == 'ห้องล่าง') ? 'selected' : ''; ?>>
                                ห้องล่าง
                            </option>
                            <option value="ห้องกลาง" <?php echo ($reserve_type == 'ห้องกลาง') ? 'selected' : ''; ?>>
                                ห้องกลาง
                            </option>
                            <option value="ห้องใหญ่" <?php echo ($reserve_type == 'ห้องใหญ่') ? 'selected' : ''; ?>>
                                ห้องใหญ่
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>
                <!-- ไม่มีปุ่มค้นหาอีกต่อไป เนื่องจากการ submit อัตโนมัติ -->


<!-- Room selection -->
<div class="form-row">
    <div class="col-md-12">
        <h4>เลือกห้องซ้อม :</h4>
        <div class="row">
            <?php
            // ค้นหาห้องทั้งหมด
            $sql = "SELECT * FROM room_tb";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($room = $result->fetch_assoc()) {
                    // ตรวจสอบสถานะห้องจากตารางการจอง
                    $room_type = $room['room_type'];
                    $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                   WHERE reserve_type = '$room_type' 
                                   AND reserve_date = '$reserve_date' 
                                   AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                    $status_result = $conn->query($status_sql);
                    $status_row = $status_result->fetch_assoc();
                    $is_reserved = $status_row['reserved_count'] > 0;

                    echo '<div class="col-md-4">';
                    echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="selectRoomType(\'' . $room['room_type'] . '\')">';
                    echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-type">' . $room['room_type'] . ' (' . $room['room_capacity'] . ' คน)</h5>';
                    echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                    echo '<p class="card-text">ราคา : ' . $room['room_price'] . ' /ชม.</p>';
                    
                    // ตรวจสอบสถานะห้อง
                    if ($is_reserved) {
                        echo '<p class="card-text text-unavailable">สถานะ : ไม่ว่าง</p>';
                    } else {
                        echo '<p class="card-text text-available">สถานะ : ว่าง</p>';
                    }
                    
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


<!-- JavaScript function to update the form -->
<script>
function selectRoomType(roomType) {
    document.getElementById('reserve_type').value = roomType;
    // Submit the form to update the room selection
    document.querySelector('form').submit();
}
</script>

                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ชื่อลูกค้า :</label>
                        <input type="text" class="form-control" name="reserve_name"
                            value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">เบอร์โทร :</label>
                        <input type="text" class="form-control" name="reserve_telphone"
                            value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ที่อยู่ :</label>
                        <input type="text" class="form-control" name="reserve_address"
                            value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ราคาห้อง :</label>
                        <input type="text" class="form-control" name="reserve_price"
                            value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>

                <button class="btn btn-danger" type="submit">บันทึก</button>
                <button class="btn btn-secondary" type="button"
                    onclick="window.location.href='show.php';">ยกเลิก</button>
            </form>
        </div>
    </div>
    </div>
    </div>
</section>

<!-- /.content -->
<?php include('footer.php'); ?>
<script>
$(function() {
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