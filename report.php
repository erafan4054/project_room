<?php
$menu = "report";
include("menu_session.php");  // ดึงข้อมูลผู้ใช้จาก session
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

// ดึงวันที่เริ่มต้นและสิ้นสุดจาก GET เพื่อกรองข้อมูล
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// ตรวจสอบและแปลงวันที่หากมีการกรอกวันที่ (จาก ค.ศ. เป็น พ.ศ.)
if ($start_date) {
  $start_date_time = strtotime($start_date);
  $start_date = date('Y-m-d', strtotime('+543 years', $start_date_time)); // แปลง ค.ศ. เป็น พ.ศ.
}
if ($end_date) {
  $end_date_time = strtotime($end_date);
  $end_date = date('Y-m-d', strtotime('+543 years', $end_date_time)); // แปลง ค.ศ. เป็น พ.ศ.
}

// Query สำหรับการ์ดสรุปรายงาน
$sql_income = "SELECT SUM(reserve_total) AS total_income FROM reserve_tb WHERE TRIM(status) = 'ดำเนินการแล้ว'";
$sql_total_customers = "SELECT COUNT(DISTINCT reserve_name) AS total_customers FROM reserve_tb WHERE TRIM(status) = 'ดำเนินการแล้ว'";
$sql_large_rooms = "SELECT COUNT(*) AS total_large_rooms FROM reserve_tb WHERE TRIM(status) = 'ดำเนินการแล้ว' AND reserve_type = 'ห้องใหญ่'";
$sql_medium_rooms = "SELECT COUNT(*) AS total_medium_rooms FROM reserve_tb WHERE TRIM(status) = 'ดำเนินการแล้ว' AND reserve_type = 'ห้องกลาง'";
$sql_bottom_rooms = "SELECT COUNT(*) AS total_bottom_rooms FROM reserve_tb WHERE TRIM(status) = 'ดำเนินการแล้ว' AND reserve_type = 'ห้องล่าง'";
$sql_total_reservations = "SELECT COUNT(*) AS total_reservations FROM reserve_tb WHERE TRIM(status) = 'ดำเนินการแล้ว'";

// เพิ่มเงื่อนไขวันที่ใน Query หากมีการเลือกวันที่เริ่มต้นและสิ้นสุด
if ($start_date && $end_date) {
    $sql_income .= " AND reserve_date BETWEEN '$start_date' AND '$end_date'";
    $sql_total_customers .= " AND reserve_date BETWEEN '$start_date' AND '$end_date'";
    $sql_large_rooms .= " AND reserve_date BETWEEN '$start_date' AND '$end_date'";
    $sql_medium_rooms .= " AND reserve_date BETWEEN '$start_date' AND '$end_date'";
    $sql_bottom_rooms .= " AND reserve_date BETWEEN '$start_date' AND '$end_date'";
    $sql_total_reservations .= " AND reserve_date BETWEEN '$start_date' AND '$end_date'";
}

// ดึงข้อมูลสำหรับการ์ดแต่ละใบ
$result_income = $conn->query($sql_income);
$result_customers = $conn->query($sql_total_customers);
$result_large_rooms = $conn->query($sql_large_rooms);
$result_medium_rooms = $conn->query($sql_medium_rooms);
$result_bottom_rooms = $conn->query($sql_bottom_rooms);
$result_total_reservations = $conn->query($sql_total_reservations);

// กำหนดค่าเริ่มต้นของตัวแปร
$total_income = 0;
$total_customers = 0;
$total_large_rooms = 0;
$total_medium_rooms = 0;
$total_bottom_rooms = 0;
$total_reservations = 0;

// ตรวจสอบผลลัพธ์ของแต่ละ Query และดึงค่ามาใช้งาน
if ($result_income && $result_income->num_rows > 0) {
  $row = $result_income->fetch_assoc();
  $total_income = $row['total_income']; // เก็บรายได้รวมจากผลลัพธ์ของ Query
}
if ($result_customers && $result_customers->num_rows > 0) {
  $row = $result_customers->fetch_assoc();
  $total_customers = $row['total_customers']; // เก็บจำนวนลูกค้าจากผลลัพธ์ของ Query
}
if ($result_large_rooms && $result_large_rooms->num_rows > 0) {
  $row = $result_large_rooms->fetch_assoc();
  $total_large_rooms = $row['total_large_rooms']; // เก็บจำนวนการจองห้องใหญ่จากผลลัพธ์ของ Query
}
if ($result_medium_rooms && $result_medium_rooms->num_rows > 0) {
  $row = $result_medium_rooms->fetch_assoc();
  $total_medium_rooms = $row['total_medium_rooms']; // เก็บจำนวนการจองห้องกลางจากผลลัพธ์ของ Query
}
if ($result_bottom_rooms && $result_bottom_rooms->num_rows > 0) {
  $row = $result_bottom_rooms->fetch_assoc();
  $total_bottom_rooms = $row['total_bottom_rooms']; // เก็บจำนวนการจองห้องล่างจากผลลัพธ์ของ Query
}
if ($result_total_reservations && $result_total_reservations->num_rows > 0) {
  $row = $result_total_reservations->fetch_assoc();
  $total_reservations = $row['total_reservations']; // เก็บจำนวนการจองทั้งหมดจากผลลัพธ์ของ Query
}
// Query สำหรับตารางข้อมูลการจอง
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_price, reserve_hour, reserve_more, reserve_total, reserve_name, reserve_telphone 
        FROM reserve_tb 
        WHERE TRIM(status) = 'ดำเนินการแล้ว'";

if ($start_date && $end_date) {
    $sql .= " AND reserve_date BETWEEN '$start_date' AND '$end_date'";
}

$sql .= " ORDER BY reserve_id DESC";



$result = $conn->query($sql);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <h1><i class="nav-icon fas fa-file-alt"></i> รายงานการจอง</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
<div class="row">
    <!-- การ์ดจำนวนประเภทห้องใหญ่ -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-success">
        <div class="inner">
          <h3><?php echo $total_large_rooms; ?></h3>
          <p>จำนวนห้องใหญ่</p>
        </div>
        <div class="icon">
          <i class="fas fa-door-open"></i>
        </div>
      </div>
    </div>

    <!-- การ์ดจำนวนประเภทห้องกลาง -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-purple">
        <div class="inner">
          <h3><?php echo $total_medium_rooms; ?></h3>
          <p>จำนวนห้องกลาง</p>
        </div>
        <div class="icon">
          <i class="fas fa-door-closed"></i>
        </div>
      </div>
    </div>

    <!-- การ์ดจำนวนประเภทห้องล่าง -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-info">
        <div class="inner">
          <h3><?php echo $total_bottom_rooms; ?></h3>
          <p>จำนวนห้องล่าง</p>
        </div>
        <div class="icon">
          <i class="fas fa-building"></i>
        </div>
      </div>
    </div>

    <!-- การ์ดจำนวนการจองทั้งหมด -->
    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-primary">
        <div class="inner">
          <h3><?php echo $total_reservations; ?></h3>
          <p>จำนวนการจองทั้งหมด</p>
        </div>
        <div class="icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
      </div>
    </div>

    <!-- การ์ดรายได้ทั้งหมด -->
    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3><?php echo number_format($total_income); ?> บาท</h3>
          <p>รายได้ทั้งหมด</p>
        </div>
        <div class="icon">
          <i class="fas fa-money-bill-wave"></i>
        </div>
      </div>
    </div>
</div>

<!-- ตารางข้อมูลการจอง -->
<div class="card-body p-1">
<div class="card">
  <div class="card-header card-navy card-outline">
    <!-- ฟอร์มกรองข้อมูล -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-5">
                <label for="start_date">เริ่มต้น:</label>
                <input type="date" class="form-control" name="start_date" value="<?php echo $start_date ? date('Y-m-d', strtotime($start_date)) : ''; ?>">
            </div>
            <div class="col-md-5">
                <label for="end_date">สิ้นสุด:</label>
                <input type="date" class="form-control" name="end_date" value="<?php echo $end_date ? date('Y-m-d', strtotime($end_date)) : ''; ?>">
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">ค้นหา</button>
            </div>
        </div>
    </form>
    <div class="row">
      <div class="col-md-12">
        <table id="example1" class="table table-bordered table-striped dataTable" role="grid">
          <thead>
            <tr role="row" class="info">
                <th tabindex="0" rowspan="1" colspan="1" style="width: 1%;">ลำดับ</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">วันที่จอง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เวลาเริ่ม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">เวลาสิ้นสุด</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ประเภท</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคา/ชม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 9%;">ชั่วโมง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">ชั่วโมงเพิ่ม</th> <!-- เพิ่มคอลัมน์ใหม่ -->
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ยอดรวม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 13%;">ชื่อลูกค้า</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">เบอร์โทร</th>   
            </tr>
          </thead>
          <tbody>
          <?php
            // ตรวจสอบว่าผลลัพธ์มีข้อมูลหรือไม่
            if ($result && $result->num_rows > 0) {
                $number = 0; // กำหนดตัวแปร $number เริ่มต้นที่ 0 เพื่อใช้เป็นลำดับที่ในการแสดงข้อมูล

                // วนลูปเพื่อดึงข้อมูลแต่ละแถวจากผลลัพธ์
                while ($row = $result->fetch_assoc()) {
                    $number++; // เพิ่มค่าของ $number ทีละ 1 สำหรับแต่ละแถวที่แสดง
                    $date = new DateTime($row["reserve_date"]); // สร้าง DateTime object จากวันที่ที่ดึงมาจากฐานข้อมูล
                    $formattedDate = $date->format('d-m-Y'); // แปลงวันที่ให้อยู่ในรูปแบบ 'วัน-เดือน-ปี'
           
                    echo "<tr>";
                    echo "<td>$number</td>";
                    echo "<td>$formattedDate</td>";
                    echo "<td>{$row['reserve_time1']}</td>";
                    echo "<td>{$row['reserve_time2']}</td>";
                    echo "<td>{$row['reserve_type']}</td>";
                    echo "<td>{$row['reserve_price']} บาท</td>";
                    echo "<td>{$row['reserve_hour']} ชม.</td>";
                    echo "<td>";
                              if (!empty($row['reserve_more']) && $row['reserve_more'] != '0') {
                                  // ตรวจสอบค่าของ reserve_more และแสดงผลตามที่กำหนด
                                  switch ($row['reserve_more']) {
                                      case '0.5':
                                          echo '30 นาที';
                                          break;
                                      case '1':
                                          echo '1 ชม.';
                                          break;
                                      case '1.5':
                                          echo '1:30 นาที';
                                          break;
                                      default:
                                          echo $row['reserve_more'] . ' ชม.';
                                          break;
                                  }
                              } else {
                                  echo 'ไม่มี';
                              }
                    echo "</td>";
                    echo "<td>{$row['reserve_total']} บาท</td>";
                    echo "<td>{$row['reserve_name']}</td>";
                    echo "<td>{$row['reserve_telphone']}</td>";
                    
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>--ไม่มีข้อมูลที่สามารถใช้ได้--</td></tr>";
            }
          ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</section>
<!-- /.content -->

<?php $conn->close(); ?>
<?php include('footer.php'); ?>

<script>
$(function () {
  $(".datatable").DataTable();
});
</script>
</body>
</html>
