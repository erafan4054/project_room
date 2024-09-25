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

// สร้างคำสั่ง SQL สำหรับการค้นหา
$sql = "SELECT * FROM reserve_tb WHERE reserve_date = '$reserve_date'";

// เพิ่มเงื่อนไขการค้นหาช่วงเวลาที่ไม่ซ้อนทับกัน
if (!empty($reserve_time1) && !empty($reserve_time2)) {
    $sql .= " AND NOT (
        (reserve_time1 >= '$reserve_time1' AND reserve_time1 < '$reserve_time2') OR
        (reserve_time2 > '$reserve_time1' AND reserve_time2 <= '$reserve_time2') OR
        ('$reserve_time1' >= reserve_time1 AND '$reserve_time1' < reserve_time2) OR
        ('$reserve_time2' > reserve_time1 AND '$reserve_time2' <= reserve_time2)
    )";
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
    max-height: 200px;
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


.modal-content {
    border-radius: 8px;
    padding: 20px;
}

.modal-header, .modal-footer {
    padding-bottom: 15px;
    padding-top: 15px;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: bold;
}

.btn-close {
    background: #f8f9fa;
    border: none;
    font-size: 1.25rem;
}

.btn-secondary, .btn-danger {
    border: none;
    color: #fff;
}
</style>

<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> จัดการข้อมูลจอง</h1>
      </div>
      <div class="col-md-6 text-right">
        <a href="show.php?action=add" class="btn btn-danger">
          <i class="nav-icon fas fa-address-card"></i> รายการบันทึกทั้งหมด
        </a>
      </div>
    </div>
  </div>
</section>

<section class="content">
    <div class="card1 card-custom card-sticky">
        <div class="card-header card-navy card-outline"><br>
            <div class="row">
                <div class="col-md-12">
                    <h4>เลือกห้องซ้อม :</h4>
                    <div class="row">
                        <?php
                        // Display room data
                        $sql = "SELECT * FROM room_tb";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($room = $result->fetch_assoc()) {
                                $room_type = $room['room_type'];
                                $room_price = $room['room_price'];

                                echo '<div class="col-md-4">';
                                echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="openReserveModal(\'' . $room['room_type'] . '\', ' . $room_price . ')">';
                                echo '<img src="../uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-type">' . $room['room_type'] . ' (ความจุ ' . $room['room_capacity'] . ' คน)</h5>';
                                echo '<p class="card-text">' . $room['room_detail'] . ' </p>';
                                echo '<h6 class="text-success">ราคา : ' . $room['room_price'] . ' บาท/ชม.</h6>';

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
                                <!-- Move the date and time inputs to the modal -->
                                <div class="modal-header">
                                    <h4 class="modal-title" id="reserveModalLabel"><i class="nav-icon fas fa-plus-square"></i> เพิ่มข้อมูลลูกค้า</h4>
                                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><i class="nav-icon fas fa-times"></i></button>
                                </div>

                                <div class="modal-body">
                                    <!-- Date and Time Inputs -->
                                    <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="reserve_date">วันที่ :</label>
                                        <input type="date" class="form-control" id="reserve_date" name="reserve_date" value="<?php echo $reserve_date; ?>" required>
                                    </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="reserve_time1">เวลา (เริ่ม) :</label>
                                            <select class="form-control" id="reserve_time1" name="reserve_time1" required onchange="calculateTotal()">
                                            <option value="">--:--</option>
                                                <?php 
                                                for ($hour = 10; $hour <= 20; $hour++) {
                                                    for ($minute = 0; $minute < 60; $minute += 30) {
                                                        $time = sprintf('%02d:%02d', $hour, $minute);
                                                        echo "<option value='$time'>$time</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="reserve_time2">เวลา (ถึง) :</label>
                                            <select class="form-control" id="reserve_time2" name="reserve_time2" required onchange="calculateTotal()">
                                            <option value="">--:--</option>
                                                <?php 
                                                for ($hour = 10; $hour <= 20; $hour++) {
                                                    for ($minute = 0; $minute < 60; $minute += 30) {
                                                        $time = sprintf('%02d:%02d', $hour, $minute);
                                                        echo "<option value='$time'>$time</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Customer Information Inputs -->
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label for="reserve_type">ประเภทห้อง :</label>
                                            <input type="text" class="form-control" id="modal_reserve_type" name="reserve_type" readonly required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="reserve_price">ราคาห้อง/ชม.:</label>
                                            <input type="number" class="form-control" id="reserve_price" name="reserve_price"  step="0.01" readonly required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="reserve_hour">กี่ชั่วโมง :</label> 
                                            <input type="text" class="form-control" id="reserve_hour" name="reserve_hour" readonly required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="reserve_total">ยอดรวม :</label>
                                            <input type="number" class="form-control" id="reserve_total" name="reserve_total" readonly>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="reserve_name">ชื่อลูกค้า :</label>
                                            <input type="text" class="form-control" id="reserve_name" name="reserve_name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="reserve_telphone">เบอร์โทร :</label>
                                            <input type="text" class="form-control" id="reserve_telphone" name="reserve_telphone" required>
                                        </div>
                                    </div>
                                        <div class="mb-3">
                                            <label for="reserve_address">ที่อยู่ :</label>
                                            <textarea class="form-control" id="reserve_address" name="reserve_address" rows="3" required></textarea>
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
                <!-- End of Modal -->

            </div>
        </div>
    </div>
</section>


<script>
// เปิด modal และเติมข้อมูลที่จำเป็น
function openReserveModal(reserveType, roomPrice) {
    document.getElementById('modal_reserve_type').value = reserveType;
    document.getElementById('reserve_price').value = roomPrice;
    $('#reserveModal').modal('show');
}


// แปลงปี ค.ศ. เป็น พ.ศ.
document.getElementById('reserve_date').addEventListener('change', function() {
        var inputDate = new Date(this.value);
        if (!isNaN(inputDate)) {
            // แปลงปี ค.ศ. เป็น พ.ศ. โดยเพิ่ม 543 ปี
            var thaiYear = inputDate.getFullYear() + 543;
            inputDate.setFullYear(thaiYear);

            // แปลงกลับเป็น string ในรูปแบบ yyyy-mm-dd
            var thaiDateStr = inputDate.toISOString().split('T')[0];
            
            // กำหนดค่าใหม่ใน input field
            this.value = thaiDateStr;
        }
    });

// คำนวณราคา    
function calculateTotal() {
    var reserveTime1 = document.getElementById('reserve_time1').value;
    var reserveTime2 = document.getElementById('reserve_time2').value;
    var roomPrice = parseFloat(document.getElementById('reserve_price').value);

    if (reserveTime1 && reserveTime2 && !isNaN(roomPrice)) {
        // แปลงเวลาจากสตริงเป็น Date object
        var time1 = new Date('1970-01-01T' + reserveTime1 + ':00');
        var time2 = new Date('1970-01-01T' + reserveTime2 + ':00');

        // ตรวจสอบว่าเวลาถึงมากกว่าเวลาที่เริ่มต้น
        if (time2 > time1) {
            // คำนวณจำนวนมิลลิวินาทีที่ต่างกัน
            var diffInMs = time2 - time1;
            var diffInMinutes = diffInMs / (1000 * 60); // แปลงเป็นนาที

            // คำนวณจำนวนชั่วโมงและนาที
            var hours = Math.floor(diffInMinutes / 60);  // จำนวนชั่วโมงเต็ม
            var minutes = diffInMinutes % 60;  // จำนวนครึ่งชั่วโมงที่เหลือ

            // คำนวณราคาทั้งหมด
            var totalPrice = (hours * roomPrice) + (minutes > 0 ? 50 : 0);
            document.getElementById('reserve_total').value = totalPrice.toFixed(2); // แสดงยอดรวมทั้งหมด

            // แสดงผลเป็น ชั่วโมง:นาที
            var formattedTime = hours + ':' + (minutes < 10 ? '0' : '') + minutes;
            document.getElementById('reserve_hour').value = formattedTime;  // แสดงชั่วโมง:นาที
        } else {
            alert('เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด');
            document.getElementById('reserve_total').value = '0.00'; // รีเซ็ตค่าเมื่อเวลาผิด
            document.getElementById('reserve_hour').value = '0:00';  // รีเซ็ตชั่วโมง
        }
    } else {
        document.getElementById('reserve_total').value = '0.00'; // รีเซ็ตค่าเมื่อไม่มีเวลา
        document.getElementById('reserve_hour').value = '0:00';  // รีเซ็ตชั่วโมง
    }
}

</script>
<script>
document.getElementById('reserve_date').addEventListener('change', function() {
    var selectedDate = this.value;
    var reserveType = document.getElementById('modal_reserve_type').value;  // ดึงประเภทห้องที่ถูกเลือก

    // ส่งค่า selectedDate และ reserveType ไปยังเซิร์ฟเวอร์เพื่อดึงเวลาที่จองแล้ว
    $.ajax({
        url: 'jong.get.times.php',  // ไฟล์ PHP สำหรับตรวจสอบเวลาที่จองแล้ว
        type: 'POST',
        data: {
            date: selectedDate,
            reserve_type: reserveType  // ส่งประเภทห้อง
        },
        success: function(response) {
            var reservedTimes = JSON.parse(response);

            // ลบสไตล์ก่อนหน้า
            $('#reserve_time1 option, #reserve_time2 option').each(function() {
                $(this).prop('disabled', false).css('color', 'black'); // รีเซ็ตสไตล์
            });

            // ปิดใช้งานหรือเปลี่ยนสีของ option ที่ตรงกับเวลาที่จองแล้ว
            $('#reserve_time1 option, #reserve_time2 option').each(function() {
                if (reservedTimes.includes(this.value)) {
                    $(this).prop('disabled', true).css('color', 'red'); // เปลี่ยนสีเป็นสีแดงหรือปิดใช้งาน
                }
            });
        }
    });
});

</script>

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