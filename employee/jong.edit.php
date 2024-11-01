<?php 
// เริ่ม session และดึงข้อมูล session (เช่น ผู้ใช้)
include("menu_session.php"); 
include("header.php");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งค่า reserve_id หรือไม่
if (isset($_GET['reserve_id'])) {
    $reserve_id = $_GET['reserve_id'];

    // ดึงข้อมูลการจองจากฐานข้อมูล
    $sql = "SELECT * FROM reserve_tb WHERE reserve_id = $reserve_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "ไม่พบข้อมูลการจองที่เลือก";
        exit();
    }
}

// อัปเดตข้อมูลหลังจากกดบันทึก
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserve_id = $_POST['reserve_id'];
    $reserve_date = $_POST['reserve_date'];
    $reserve_time1 = $_POST['reserve_time1'];
    $reserve_time2 = $_POST['reserve_time2'];
    $reserve_type = $_POST['reserve_type'];
    $reserve_price = $_POST['reserve_price'];
    $reserve_hour = $_POST['reserve_hour'];
    $reserve_total = $_POST['reserve_total'];
    $reserve_name = $_POST['reserve_name'];
    $reserve_telphone = $_POST['reserve_telphone'];
    $reserve_more = $_POST['reserve_more']; // เพิ่มชั่วโมงเพิ่ม

    // อัปเดตข้อมูลการจองในฐานข้อมูล
    $sql = "UPDATE reserve_tb SET 
    reserve_date = '$reserve_date',
    reserve_time1 = '$reserve_time1',
    reserve_time2 = '$reserve_time2',
    reserve_type = '$reserve_type',
    reserve_price = '$reserve_price',
    reserve_hour = '$reserve_hour',
    reserve_total = '$reserve_total',
    reserve_name = '$reserve_name',
    reserve_telphone = '$reserve_telphone',
    reserve_more = '$reserve_more' 
    WHERE reserve_id = $reserve_id";


    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('แก้ไขข้อมูลสำเร็จ'); window.location.href='show.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ฟังก์ชันดึงราคาห้องจากฐานข้อมูลตามประเภทห้อง
function getRoomPrice($conn, $reserve_type) {
  $sql = "SELECT room_price FROM room_tb 
          INNER JOIN room_type_tb ON room_tb.room_type = room_type_tb.room_type_id 
          WHERE room_type_tb.room_type_name = '$reserve_type'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['room_price'];
  }
  return 0;
}

// ถ้ามีการเลือกประเภทห้องให้ดึงราคาห้องจากฐานข้อมูล
$reserve_price = isset($row['reserve_type']) ? getRoomPrice($conn, $row['reserve_type']) : '';
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon fas fa-edit"></i> แก้ไขข้อมูลจอง</h1>
      </div>
      <div class="col-md-6 text-right">
        <a href="jong.php?action=add" class="btn btn-danger">
          <i class="fas fa-laptop-medical"></i>  เพิ่มข้อมูล
        </a>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="card">
    <div class="card-header card-navy card-outline">
      <form action="jong.edit.php?reserve_id=<?php echo $reserve_id; ?>" method="post">
        <input type="hidden" name="reserve_id" value="<?php echo $row['reserve_id']; ?>">

        <!-- ส่วนของวันที่จอง -->
        <div class="form-row">
        <div class="col-md-6 mb-3">
            <label for="reserve_date">วันที่ :</label>
            <input type="date" class="form-control" id="reserve_date" name="reserve_date" value="<?php echo $row['reserve_date']; ?>" required>
        </div>

        <!-- Dropdown สำหรับเวลาเริ่ม -->
        <div class="col-md-3 mb-3">
            <label for="reserve_time1">เวลาเริ่ม :</label>
            <select class="form-control" id="reserve_time1" name="reserve_time1" required>
                <option value="">--:--</option>
                <!-- ตัวเลือกเวลาเริ่มจะถูกสร้างขึ้นโดย AJAX -->
            </select>
        </div>

        <!-- Dropdown สำหรับเวลาถึง -->
        <div class="col-md-3 mb-3">
            <label for="reserve_time2">เวลาสิ้นสุด :</label>
            <select class="form-control" id="reserve_time2" name="reserve_time2" required>
                <option value="">--:--</option>
                <!-- ตัวเลือกเวลาถึงจะถูกสร้างขึ้นโดย AJAX -->
            </select>
        </div>
        </div>
        <!-- ส่วนเลือกประเภทห้องและราคาห้อง -->
        <div class="form-row">
            <div class="col-md-3 mb-3">
                <label for="reserve_type">ประเภทห้อง :</label>
                <select class="form-control" id="reserve_type" name="reserve_type">
                    <option value="">เลือกห้อง</option>
                    <option value="ห้องล่าง" <?php echo ($row['reserve_type'] == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                    <option value="ห้องกลาง" <?php echo ($row['reserve_type'] == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                    <option value="ห้องใหญ่" <?php echo ($row['reserve_type'] == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                </select>
            </div>

          <!-- ราคาห้อง -->
          <div class="col-md-3 mb-3">
            <label for="reserve_price">ราคาห้อง/ชม. :</label>
            <input type="number" class="form-control" id="reserve_price" name="reserve_price" value="<?php echo $reserve_price; ?>" readonly required>
          </div>

          <!-- จำนวนชั่วโมง -->
          <div class="col-md-2 mb-3">
            <label for="reserve_hour">จำนวนชั่วโมง :</label>
            <input type="text" class="form-control" id="reserve_hour" name="reserve_hour" value="<?php echo $row['reserve_hour']; ?>" required>
          </div>

          <!-- จำนวนชั่วโมงเพิ่ม -->
          <div class="col-md-2 mb-3">
            <label for="reserve_more">ชั่วโมงเพิ่ม :</label>
            <select class="form-control" id="reserve_more" name="reserve_more" required>
                <option value="0" <?php echo ($row['reserve_more'] == '0') ? 'selected' : ''; ?>>ไม่มี</option>
                <option value="0.5" <?php echo ($row['reserve_more'] == '0.5') ? 'selected' : ''; ?>>30 นาที</option>
                <option value="1" <?php echo ($row['reserve_more'] == '1') ? 'selected' : ''; ?>>1 ชม.</option>
                <option value="1.5" <?php echo ($row['reserve_more'] == '1.5') ? 'selected' : ''; ?>>1:30 นาที</option>
            </select>
        </div>


          <!-- ยอดรวม -->
          <div class="col-md-2 mb-3">
            <label for="reserve_total">ยอดรวม :</label>
            <input type="text" class="form-control" id="reserve_total" name="reserve_total" value="<?php echo $row['reserve_total']; ?>" required>
          </div>
        </div>

        <!-- ส่วนข้อมูลลูกค้า -->
        <div class="form-row">
          <div class="col-md-6 mb-3">
            <label for="reserve_name">ชื่อลูกค้า :</label>
            <input type="text" class="form-control" id="reserve_name" name="reserve_name" value="<?php echo $row['reserve_name']; ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="reserve_telphone">เบอร์โทร :</label>
            <input type="text" class="form-control" id="reserve_telphone" name="reserve_telphone" value="<?php echo $row['reserve_telphone']; ?>" required>
          </div>
        </div>
        <div class="col-md-6 mb-3">
        </div>
        <button class="btn btn-danger" type="submit">บันทึกการแก้ไข</button>
        <a href="show.php" class="btn btn-secondary">กลับไปหน้ารายการ</a>
        <!-- <button class="btn btn-secondary" type="button">ยกเลิก</button> -->
      </form>
    </div>
  </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  
// ฟังก์ชันที่ทำงานเมื่อมีการเปลี่ยนประเภทห้อง
$(document).ready(function() {
    $('#reserve_type').change(function() {
        var reserve_type = $(this).val();  // ดึงค่าประเภทห้องที่เลือก

        if (reserve_type !== '') {
            $.ajax({
                url: 'jong.edit.get.room.php',  // ไฟล์ PHP ที่จะดึงข้อมูลจากฐานข้อมูล
                method: 'POST',  // ใช้วิธี POST ในการส่งข้อมูล
                data: {reserve_type: reserve_type},  // ส่งประเภทห้องไปยัง PHP
                dataType: 'json',  // คาดหวังข้อมูลที่ส่งกลับมาเป็น JSON
                success: function(response) {
                    // ตรวจสอบข้อมูลที่ได้รับจาก PHP
                    console.log(response);

                    // อัปเดตฟิลด์เวลาหรือราคาตามข้อมูลที่ได้จากฐานข้อมูล
                    $('#reserve_price').val(response.room_price);  // เปลี่ยนราคาห้องตามข้อมูลที่ได้
                    $('#reserve_time1').html(response.time_options);  // เปลี่ยนตัวเลือกเวลาเริ่ม
                    $('#reserve_time2').html(response.time_options);  // เปลี่ยนตัวเลือกเวลาถึง
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });
        }
    });
});

</script>

<script>
$(document).ready(function() {
    // ดึงเวลาที่บันทึกไว้ใน PHP และตัดให้เหลือ HH:mm
    var selected_start_time = '<?php echo substr($row['reserve_time1'], 0, 5); ?>'; // เวลาเริ่มต้นที่ดึงมาจากฐานข้อมูล
    var selected_end_time = '<?php echo substr($row['reserve_time2'], 0, 5); ?>'; // เวลาสิ้นสุดที่ดึงมาจากฐานข้อมูล

    function fetchReservedTimes() {
        var reserve_type = $('#reserve_type').val();  // ประเภทห้องที่เลือก
        var reserve_date = $('#reserve_date').val();  // วันที่ที่เลือก

        if (reserve_type !== '' && reserve_date !== '') {
            $.ajax({
                url: 'jong.edit.get.times.php',  // ไฟล์ PHP ที่ใช้ตรวจสอบเวลาที่จองแล้ว
                method: 'POST',  // ใช้วิธี POST ในการส่งข้อมูล
                data: {
                    reserve_type: reserve_type,  // ประเภทห้องที่เลือก
                    reserve_date: reserve_date  // วันที่ที่เลือก
                },
                dataType: 'json',  // คาดหวังข้อมูลที่ส่งกลับมาเป็น JSON
                success: function(response) {
                    // อัปเดต dropdown
                    $('#reserve_time1').html(response.time_options_start);  // อัปเดตตัวเลือกเวลาเริ่ม
                    $('#reserve_time2').html(response.time_options_end);  // อัปเดตตัวเลือกเวลาถึง

                    // ตั้งค่าค่าเริ่มต้นที่ดึงมาจากฐานข้อมูล
                    $('#reserve_time1').val(selected_start_time);  // ตั้งค่าเวลาเริ่มต้นที่เลือกไว้ก่อนหน้า
                    $('#reserve_time2').val(selected_end_time);  // ตั้งค่าเวลาถึงที่เลือกไว้ก่อนหน้า
                },
                error: function() {
                    console.log("Error loading times"); // แสดงข้อความใน console เมื่อเกิดข้อผิดพลาด
                }
            });
        }
    }

    // เรียกใช้ฟังก์ชันเมื่อโหลดหน้าเพจ
    fetchReservedTimes();

    // เมื่อมีการเปลี่ยนประเภทห้องหรือวันที่
    $('#reserve_date, #reserve_type').change(fetchReservedTimes); // เรียกใช้ฟังก์ชันเมื่อประเภทห้องหรือวันที่เปลี่ยน
});
</script>

<script>
// ฟังก์ชันคำนวณชั่วโมงและยอดรวม รวมถึงชั่วโมงเพิ่ม
function calculateTotal() {
    var reserveTime1 = document.getElementById('reserve_time1').value; // ดึงค่าจากเวลาที่เริ่มจอง
    var reserveTime2 = document.getElementById('reserve_time2').value; // ดึงค่าจากเวลาที่สิ้นสุดการจอง
    var roomPrice = parseFloat(document.getElementById('reserve_price').value); // ดึงค่าและแปลงราคาห้องเป็นตัวเลข
    var reserveMore = parseFloat(document.getElementById('reserve_more').value) || 0; // ดึงค่าชั่วโมงเพิ่ม

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

            // คำนวณราคาจากจำนวนชั่วโมง
            var totalPrice = (hours * roomPrice) + (minutes > 0 ? 50 : 0); // คำนวณราคาโดยเพิ่ม 50 บาทถ้ามีนาทีเกิน

            // เพิ่มราคาจากชั่วโมงเพิ่ม (ชั่วโมงเพิ่มถือว่าเป็นครึ่งชั่วโมง)
            if (reserveMore > 0) {
                var extraCharge = Math.ceil(reserveMore / 0.5) * 50; // ปัดขึ้นเป็นจำนวนช่วงครึ่งชั่วโมง และคูณกับ 50 บาท
                totalPrice += extraCharge; // รวมราคาชั่วโมงเพิ่มเข้าไป
            }

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

// เมื่อเลือกเวลาเริ่มหรือเวลาถึง หรือชั่วโมงเพิ่ม จะเรียกฟังก์ชัน calculateTotal
document.getElementById('reserve_time1').addEventListener('change', calculateTotal); // เรียกคำนวณเมื่อเวลาเริ่มเปลี่ยนแปลง
document.getElementById('reserve_time2').addEventListener('change', calculateTotal); // เรียกคำนวณเมื่อเวลาสิ้นสุดเปลี่ยนแปลง
document.getElementById('reserve_more').addEventListener('input', calculateTotal); // เรียกคำนวณเมื่อชั่วโมงเพิ่มเปลี่ยนแปลง

</script>




<?php include('footer.php'); ?>
<script>
$(function () {
  $(".datatable").DataTable();
});
</script>

</body>
</html>
