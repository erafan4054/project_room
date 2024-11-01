<?php
$menu = "jong";
include("menu_session.php");  
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

$conn = new mysqli($servername, $username, $password, $dbname); // สร้างการเชื่อมต่อกับฐานข้อมูล

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); 
}

// รับค่าจากฟอร์มหลังจากส่งข้อมูล
$reserve_date = isset($_POST['reserve_date']) ? $_POST['reserve_date'] : ''; 
$reserve_time1 = isset($_POST['reserve_time1']) ? $_POST['reserve_time1'] : ''; 
$reserve_time2 = isset($_POST['reserve_time2']) ? $_POST['reserve_time2'] : ''; 

// สร้างคำสั่ง SQL สำหรับการค้นหา
$sql = "SELECT * FROM reserve_tb WHERE reserve_date = '$reserve_date'"; // ค้นหาการจองที่มีวันที่ตรงกับวันที่ที่ส่งมา

// เพิ่มเงื่อนไขการค้นหาช่วงเวลา
if (!empty($reserve_time1) && !empty($reserve_time2)) {
    $sql .= " AND NOT (
        (reserve_time1 >= '$reserve_time1' AND reserve_time1 < '$reserve_time2') OR
        (reserve_time2 > '$reserve_time1' AND reserve_time2 <= '$reserve_time2') OR
        ('$reserve_time1' >= reserve_time1 AND '$reserve_time1' < reserve_time2) OR
        ('$reserve_time2' > reserve_time1 AND '$reserve_time2' <= reserve_time2)
    )";
    // เพิ่มเงื่อนไขที่ห้ามเวลาจองซ้อนทับกัน โดยตรวจสอบว่าช่วงเวลาที่จะจองไม่ทับกับเวลาจองที่มีอยู่แล้ว
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

.modal-header {
    padding-bottom: 15px;
    padding-top: 15px;
    border-bottom: 1px solid #000000; /* เพิ่มเส้นขอบด้านล่างสีดำ */
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
                        // แสดงข้อมูลห้อง
                        $sql = "SELECT room_tb.*, room_type_tb.room_type_name 
                                FROM room_tb 
                                LEFT JOIN room_type_tb ON room_tb.room_type = room_type_tb.room_type_id";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) { // ตรวจสอบว่ามีข้อมูลห้องที่ถูกดึงมาหรือไม่
                            while ($room = $result->fetch_assoc()) { // วนลูปเพื่อดึงข้อมูลแต่ละแถวเป็น associative array
                                $room_type_name = $room['room_type_name']; 
                                $room_price = $room['room_price']; 
                        
                                echo '<div class="col-md-4">'; 
                                echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="openReserveModal(\'' . $room_type_name . '\', ' . $room_price . ')">';
                                // สร้าง card สำหรับแสดงข้อมูลห้อง และเพิ่มฟังก์ชัน onclick เพื่อเปิด modal พร้อมส่งชื่อประเภทห้องและราคาไปยังฟังก์ชัน openReserveModal
                        
                                echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">'; 
                                echo '<div class="card-body">'; 
                                echo '<h5 class="card-type">' . $room_type_name . ' (ความจุ ' . $room['room_capacity'] . ' คน)</h5>';
                                echo '<p class="card-text">รายละเอียด : ' . $room['room_detail'] . ' </p>'; 
                                echo '<h6 class="text-success">ราคา : ' . $room['room_price'] . ' บาท/ชม.</h6>'; 
                        
                                echo '</div>'; 
                                echo '</div>'; 
                                echo '</div>'; 
                            }
                        } else {
                            echo "ไม่พบข้อมูลห้อง"; // แสดงข้อความเมื่อไม่มีข้อมูลห้องในฐานข้อมูล
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
                                            // วนลูปเพื่อสร้างตัวเลือกเวลาตั้งแต่ 10:00 น. ถึง 20:00 น. โดยแบ่งเป็นช่วง 30 นาที
                                            for ($hour = 10; $hour <= 20; $hour++) { // วนลูปตั้งแต่ชั่วโมงที่ (10:00 น. ถึง 20:00 น.)
                                                for ($minute = 0; $minute < 60; $minute += 30) { // วนลูปนาทีที่ 0 และ 30 (ทุกครึ่งชั่วโมง)
                                                    $time = sprintf('%02d:%02d', $hour, $minute); // จัดรูปแบบเวลาเป็น HH:MM เช่น 10:00, 10:30
                                                    echo "<option value='$time'>$time</option>"; // แสดงตัวเลือกเวลาที่สร้างขึ้นในรูปแบบ <option>
                                                }
                                            }
                                        ?>

                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="reserve_time2">เวลา (สิ้นสุด) :</label>
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
                                            <label for="reserve_price">ราคาห้อง/ชม. :</label>
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
                                            <input type="text" class="form-control" id="reserve_telphone" name="reserve_telphone" maxlength="10" required>
                                            <div class="invalid-feedback">**กรุณากรอกเบอร์โทรให้ครบ 10 ตัวเลข</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="modal-footer">
                                    <button class="btn btn-danger" type="submit" onclick="return validatePhone()">บันทึก</button>
                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">ยกเลิก</button>
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
// ฟังก์ชันสำหรับเปิด modal และเติมข้อมูลที่จำเป็น
function openReserveModal(reserveType, roomPrice) {
    // กำหนดค่าประเภทการจอง (reserveType) ให้กับ input field ที่มี id 'modal_reserve_type' ใน modal
    document.getElementById('modal_reserve_type').value = reserveType;
    
    // กำหนดค่าราคาห้อง (roomPrice) ให้กับ input field ที่มี id 'reserve_price' ใน modal
    document.getElementById('reserve_price').value = roomPrice;
    
    // เปิด modal ที่มี id 'reserveModal' โดยใช้ jQuery เพื่อให้แสดงขึ้นมา
    $('#reserveModal').modal('show');
}


// แปลงปี ค.ศ. เป็น พ.ศ.
document.getElementById('reserve_date').addEventListener('change', function() {
    var inputDate = new Date(this.value); // แปลงค่าวันที่จาก input ให้เป็น Date object
    if (!isNaN(inputDate)) {
        // แปลงปี ค.ศ. เป็น พ.ศ. โดยเพิ่ม 543 ปี
        var thaiYear = inputDate.getFullYear() + 543; // เพิ่ม 543 ปีเพื่อแปลงเป็น พ.ศ.
        inputDate.setFullYear(thaiYear); // ตั้งค่าปีใหม่ใน Date object

        // แปลงกลับเป็น string ในรูปแบบ yyyy-mm-dd
        var thaiDateStr = inputDate.toISOString().split('T')[0]; // แปลงวันที่เป็นสตริงตามรูปแบบที่ต้องการ
        
        // กำหนดค่าใหม่ใน input field
        this.value = thaiDateStr; // ตั้งค่าวันที่ใหม่ที่แปลงเป็น พ.ศ. ใน input field
    }
});

// จำกัดให้สามารถกรอกเฉพาะตัวเลข
document.getElementById('reserve_telphone').addEventListener('input', function (e) {
    // ใช้ regex ลบตัวอักษรที่ไม่ใช่ตัวเลขออก
    let value = e.target.value.replace(/\D/g, ''); // \D หมายถึงทุกอย่างที่ไม่ใช่ตัวเลข

    // จำกัดความยาวไม่เกิน 10 ตัวอักษร
    if (value.length > 10) {
        value = value.slice(0, 10); // ถ้าเกิน 10 ตัวให้ตัดออก
    }

    e.target.value = value; // กำหนดค่าใหม่กลับให้ input เฉพาะตัวเลขที่เหลืออยู่
});


// ฟังก์ชันกำหนดตัวเลขเบอร์โทรให้มีความยาวเท่ากับ 10 ตัวอักษร
function validatePhone() {
    const phoneInput = document.getElementById('reserve_telphone');
    const phoneValue = phoneInput.value;

    // ตรวจสอบว่าเบอร์โทรมีความยาวครบ 10 ตัวอักษรหรือไม่
    if (phoneValue.length !== 10) {
        phoneInput.classList.add('is-invalid'); // เพิ่มคลาสเพื่อแสดงข้อความแจ้งเตือน
        alert('กรุณากรอกเบอร์โทรให้ครบ 10 ตัวเลข');
        return false; // หยุดการส่งข้อมูล
    }

    phoneInput.classList.remove('is-invalid'); // ลบคลาสแจ้งเตือนหากครบ
    return true; // อนุญาตให้ส่งข้อมูล
}

// ฟังก์ชันคำนวณชั่วโมงและยอดรวม
function calculateTotal() {
    var reserveTime1 = document.getElementById('reserve_time1').value; // ดึงค่าจากเวลาที่เริ่มจอง
    var reserveTime2 = document.getElementById('reserve_time2').value; // ดึงค่าจากเวลาที่สิ้นสุดการจอง
    var roomPrice = parseFloat(document.getElementById('reserve_price').value); // ดึงค่าและแปลงราคาห้องเป็นตัวเลข

    if (reserveTime1 && reserveTime2 && !isNaN(roomPrice)) {
        // แปลงเวลาจากสตริงเป็น Date object
        var time1 = new Date('1970-01-01T' + reserveTime1 + ':00'); // แปลงเวลาเริ่มเป็น Date object
        var time2 = new Date('1970-01-01T' + reserveTime2 + ':00'); // แปลงเวลาสิ้นสุดเป็น Date object

        // ตรวจสอบว่าเวลาถึงมากกว่าเวลาที่เริ่มต้น
        if (time2 > time1) {
            // คำนวณจำนวนมิลลิวินาทีที่ต่างกัน
            var diffInMs = time2 - time1; // คำนวณส่วนต่างของเวลาเป็นมิลลิวินาที
            var diffInMinutes = diffInMs / (1000 * 60); // แปลงส่วนต่างเป็นนาที

            // คำนวณจำนวนชั่วโมงและนาที
            var hours = Math.floor(diffInMinutes / 60);  // จำนวนชั่วโมงเต็ม
            var minutes = diffInMinutes % 60;  // จำนวนนาทีที่เหลือจากการหาร

            // คำนวณราคาทั้งหมด
            var totalPrice = (hours * roomPrice) + (minutes > 0 ? 50 : 0); // คำนวณราคาโดยเพิ่ม 50 บาทถ้ามีนาทีเกิน
            document.getElementById('reserve_total').value = totalPrice.toFixed(2); // แสดงยอดรวมทั้งหมดที่คำนวณ

            // แสดงผลเป็น ชั่วโมง:นาที
            var formattedTime = hours + ':' + (minutes < 10 ? '0' : '') + minutes; // จัดรูปแบบเวลาให้ดูดีขึ้น
            document.getElementById('reserve_hour').value = formattedTime;  // แสดงชั่วโมง:นาทีใน input field
        } else {
            alert('เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด'); // แจ้งเตือนเมื่อเวลาสิ้นสุดน้อยกว่าเวลาเริ่มต้น
            document.getElementById('reserve_total').value = '0.00'; // รีเซ็ตค่าเมื่อเวลาผิด
            document.getElementById('reserve_hour').value = '0:00';  // รีเซ็ตชั่วโมง
        }
    } else {
        document.getElementById('reserve_total').value = '0.00'; // รีเซ็ตค่าเมื่อไม่มีเวลา
        document.getElementById('reserve_hour').value = '0:00';  // รีเซ็ตชั่วโมง
    }
}
// เมื่อเลือกเวลาเริ่มหรือเวลาถึงจะเรียกฟังก์ชัน calculateTotal
document.getElementById('reserve_time1').addEventListener('change', calculateTotal); // เรียกคำนวณเมื่อเวลาเริ่มเปลี่ยนแปลง
document.getElementById('reserve_time2').addEventListener('change', calculateTotal); // เรียกคำนวณเมื่อเวลาสิ้นสุดเปลี่ยนแปลง

</script>

<script>
// เช็คเวลาที่จองแล้วในวันและประเภทห้อง โดยการใช้ JavaScript ร่วมกับ jQuery และ AJAX     
document.getElementById('reserve_date').addEventListener('change', function() {
    var selectedDate = this.value; // ดึงวันที่ที่เลือก
    var reserveType = document.getElementById('modal_reserve_type').value;  // ดึงประเภทห้องที่ถูกเลือก

    // ส่งค่า selectedDate และ reserveType ไปยังเซิร์ฟเวอร์เพื่อดึงเวลาที่จองแล้ว
    $.ajax({
        url: 'jong.get.times.php',  // ไฟล์ PHP สำหรับตรวจสอบเวลาที่จองแล้ว
        type: 'POST', // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย POST method
        data: {
            date: selectedDate, // ส่งวันที่ที่เลือกไปยังเซิร์ฟเวอร์
            reserve_type: reserveType  // ส่งประเภทห้องไปยังเซิร์ฟเวอร์
        },
        success: function(response) {
            var reservedTimes = JSON.parse(response); // แปลงข้อมูลที่ได้รับจากเซิร์ฟเวอร์ให้เป็น array

            // ลบสไตล์ก่อนหน้า
            $('#reserve_time1 option, #reserve_time2 option').each(function() {
                $(this).prop('disabled', false).css('color', 'green'); 
            });

            // ปิดใช้งานหรือเปลี่ยนสีของ option ที่ตรงกับเวลาที่จองแล้ว
            $('#reserve_time1 option, #reserve_time2 option').each(function() {
                if (reservedTimes.includes(this.value)) {
                    $(this).prop('disabled', true).css('color', 'red'); 
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
        // เริ่มต้น DataTable สำหรับทุก element ที่มี class "datatable"
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

    // ฟังก์ชันสำหรับอัปเดตประเภทห้อง
    function updateRoomType(roomType) {
        document.getElementById('reserve_type').value = roomType; 
        // ตั้งค่าให้ element ที่มี id "reserve_type" มีค่าเป็นประเภทห้องที่ส่งเข้ามา (roomType)
    }
</script>


</body>

</html>

<?php
$conn->close();
?>