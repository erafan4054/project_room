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
    max-height: 150px;
    object-fit: cover;
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

.card-disabled {
    pointer-events: none;
    opacity: 0.5;
}
</style>

<style>
    .modal-content {
        border-radius: 8px; /* เพิ่มมุมโค้งมน */
        padding: 20px; /* เพิ่ม padding ภายใน modal */
    }
    
    .modal-header {
        border-bottom: 1px solid #dee2e6; /* เพิ่มเส้นแบ่งด้านล่างของ header */
        padding-bottom: 15px; /* เพิ่มพื้นที่ด้านล่าง */
    }
    
    .modal-footer {
        border-top: 1px solid #dee2e6; /* เพิ่มเส้นแบ่งด้านบนของ footer */
        padding-top: 15px; /* เพิ่มพื้นที่ด้านบน */
    }
    
    .modal-title {
        font-size: 1.5rem; /* ขนาดตัวอักษรของ title */
        font-weight: bold; /* ทำให้ตัวอักษรเป็นตัวหนา */
    }
    
    .btn-close {
        background: #f8f9fa; /* สีพื้นหลังของปุ่ม close */
        border: none; /* ลบเส้นขอบ */
        font-size: 1.25rem; /* ขนาดของไอคอน */
    }
    
    .btn-secondary {
        background-color: #6c757d; /* สีพื้นหลังของปุ่มยกเลิก */
        border: none; /* ลบเส้นขอบ */
        color: #fff; /* สีของตัวอักษร */
    }
    
    .btn-secondary:hover {
        background-color: #5a6268; /* สีพื้นหลังเมื่อ hover */
    }
    
    .btn-danger {
        background-color: #dc3545; /* สีพื้นหลังของปุ่มบันทึก */
        border: none; /* ลบเส้นขอบ */
        color: #fff; /* สีของตัวอักษร */
    }
    
    .btn-danger:hover {
        background-color: #c82333; /* สีพื้นหลังเมื่อ hover */
    }
</style>


<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> จัดการข้อมูลจอง</h1>
        </div>
      <div class="col-md-6 text-right">
        <a href="show.php?action=add" class="btn btn-danger">
          <i class="nav-icon fas fa-address-card"></i>  แสดงข้อมูลทั้งหมด
        </a>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card1 card-custom card-sticky" id="kt_page_sticky_card">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                <input type="hidden" name="reserve_id" value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">

                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom03">วันที่ :</label>
                        <input type="date" class="form-control" id="reserve_date" name="reserve_date" value="<?php echo $reserve_date; ?>" required onchange="convertToBuddhistYear();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">เวลา (เริ่ม) :</label>
                        <select class="form-control" name="reserve_time1" required onchange="this.form.submit();">
                            <option value="">--:--</option>
                            <?php 
                            // ตรวจสอบค่า reserve_time1 ถ้ามีค่าแล้วให้เลือกค่านั้น
                            for ($hour = 10; $hour <= 20; $hour++) {
                                for ($minute = 0; $minute < 60; $minute += 30) {
                                    $time = sprintf('%02d:%02d', $hour, $minute);
                                    // ตรวจสอบว่าค่านี้ตรงกับค่าที่ถูกเก็บไว้หรือไม่
                                    $selected = ($time == $reserve_time1) ? "selected" : "";
                                    echo "<option value='$time' $selected>$time</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="validationCustom03">เวลา (ถึง) :</label>
                        <select class="form-control" name="reserve_time2" required onchange="this.form.submit();">
                            <option value="">--:--</option>
                            <?php 
                            // ตรวจสอบค่า reserve_time2 ถ้ามีค่าแล้วให้เลือกค่านั้น
                            for ($hour = 10; $hour <= 20; $hour++) {
                                for ($minute = 0; $minute < 60; $minute += 30) {
                                    $time = sprintf('%02d:%02d', $hour, $minute);
                                    // ตรวจสอบว่าค่านี้ตรงกับค่าที่ถูกเก็บไว้หรือไม่
                                    $selected = ($time == $reserve_time2) ? "selected" : "";
                                    echo "<option value='$time' $selected>$time</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>

                </div>
            </form>
            
            <!-- ไม่มีปุ่มค้นหาอีกต่อไป เนื่องจากการ submit อัตโนมัติ -->

            <!-- Room selection -->
            <div class="form-row">
                <div class="col-md-12">
                    <h4>เลือกห้องซ้อม :</h4>
                    <div class="row">
                        <?php
                        // Display room data
                        $sql = "SELECT * FROM room_tb";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($room = $result->fetch_assoc()) {
                                // Detect room reservation status
                                $room_type = $room['room_type'];
                                $room_price = $room['room_price'];
                                $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                            WHERE reserve_type = '$room_type' 
                                            AND reserve_date = '$reserve_date' 
                                            AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                                $status_result = $conn->query($status_sql);
                                $status_row = $status_result->fetch_assoc();
                                $is_reserved = $status_row['reserved_count'] > 0;

                                // Display room card
                                echo '<div class="col-md-4">';
                                $cardClass = $is_reserved ? 'card-disabled' : 'card';
                                echo '<div class="' . $cardClass . '" id="card-' . $room['room_type'] . '" onclick="openReserveModal(\'' . $room['room_type'] . '\', ' . $room_price . ', ' . ($is_reserved ? 'true' : 'false') . ')">';
                                echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-type">' . $room['room_type'] . ' (ความจุ ' . $room['room_capacity'] . ' คน)</h5>';
                                echo '<p class="card-text">' . $room['room_detail'] . '( ราคา : ' . $room['room_price'] . ' บาท/ชม.)</p>';

                                if ($is_reserved) {
                                    echo '<p class="text-danger">ไม่สามารถจองได้</p>';
                                } else {
                                    echo '<p class="text-success">พร้อมใช้งาน</p>';
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

                <!-- Modal -->
                <div class="modal fade" id="reserveModal" tabindex="-1" aria-labelledby="reserveModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="jong.insert.php" method="post">
                                <input type="hidden" name="reserve_date" value="<?php echo htmlspecialchars($reserve_date); ?>">
                                <input type="hidden" name="reserve_time1" value="<?php echo htmlspecialchars($reserve_time1); ?>">
                                <input type="hidden" name="reserve_time2" value="<?php echo htmlspecialchars($reserve_time2); ?>">

                                <div class="modal-header">
                                    <h4 class="modal-title" id="reserveModalLabel"><i class="nav-icon fas fa-plus-square"></i> เพิ่มข้อมูลลูกค้า</h4>
                                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><i class="nav-icon fas fa-times"></i></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="reserve_name" class="form-label">ชื่อลูกค้า : </label>
                                            <input type="text" class="form-control" name="reserve_name" id="reserve_name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="reserve_telphone" class="form-label">เบอร์โทร : </label>
                                            <input type="text" class="form-control" name="reserve_telphone" id="reserve_telphone" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_address" class="form-label">ที่อยู่ : </label>
                                        <textarea class="form-control" name="reserve_address" id="reserve_address" rows="3" required></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="reserve_type" class="form-label">ประเภท : </label>
                                            <input class="form-control" type="text" id="modal_reserve_type" name="reserve_type" value="" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="reserve_price" class="form-label">ราคาห้อง : </label>
                                            <input type="number" class="form-control" name="reserve_price" id="reserve_price" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">ยกเลิก</button>
                                    <button type="submit" class="btn btn-danger">บันทึก</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- ดึงประเภทมาในmodal -->
                <script>
                function openReserveModal(roomType, roomPrice, isReserved) {
                    if (isReserved) {
                        alert('ห้องนี้ไม่สามารถจองได้');
                        return;
                    }
                    document.getElementById('modal_reserve_type').value = roomType;
                    document.getElementById('reserve_price').value = roomPrice; // Set default price per hour
                    var modal = new bootstrap.Modal(document.getElementById('reserveModal'));
                    modal.show();
                }
                document.getElementById('reserve_time2').addEventListener('change', function() {
                    var time1 = document.getElementById('reserve_time1').value;
                    var time2 = document.getElementById('reserve_time2').value;
                    var roomPricePerHour = parseFloat(document.getElementById('reserve_price').value);

                    if (time1 && time2 && !isNaN(roomPricePerHour)) {
                        var start = new Date("1970-01-01T" + time1 + ":00");
                        var end = new Date("1970-01-01T" + time2 + ":00");
                        var diff = (end - start) / (1000 * 60 * 60); // Difference in hours

                        if (diff > 0) {
                            var totalPrice = diff * roomPricePerHour;
                            document.getElementById('reserve_price').value = totalPrice.toFixed(2); // Show price with 2 decimal points
                        } else {
                            alert('เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด');
                            document.getElementById('reserve_price').value = 0; // Reset price
                        }
                    }
                });
                </script>



                <script>
                    // เปลี่ยนปีเป็นพ.ศ.ด้วยจาวาสคริป 
                    function convertToBuddhistYear() {
                        var dateInput = document.getElementById("reserve_date");
                        var dateValue = new Date(dateInput.value);

                        if (!isNaN(dateValue.getTime())) {
                            var buddhistYear = dateValue.getFullYear() + 543;
                            var formattedDate = dateValue.toISOString().split('T')[0];
                            var parts = formattedDate.split("-");
                            parts[0] = buddhistYear;
                            dateInput.value = parts.join("-");
                        }
                        dateInput.form.submit();
                    }
                </script>
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
