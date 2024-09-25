<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
$menu = "index";
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

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_price, reserve_hour, reserve_total, reserve_name, reserve_telphone FROM reserve_tb";
$result = $conn->query($sql);

// นับจำนวนการจองทั้งหมด
$sql_count = "SELECT COUNT(*) as total_reserve FROM reserve_tb";
$count_result = $conn->query($sql_count);
$total_reserve = $count_result->fetch_assoc()['total_reserve'];

// คำนวณรายได้รวม
$sql_income = "SELECT SUM(reserve_total) as total_income FROM reserve_tb";
$income_result = $conn->query($sql_income);
$total_income = $income_result->fetch_assoc()['total_income'];
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
      <h1><i class="nav-icon fas fa-home"></i> หน้าหลัก</h1>
    </div>
      <div class="col-md-6 text-right">
        <a href="jong.php?action=add" class="btn btn-danger"> <!-- สีเขียวbtn-success สีแดงbtn-danger ฟ้าbtn-primary เทาbtn-secondary-->
          <i class="fas fa-laptop-medical"></i>  เพิ่มข้อมูล
        </a>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
  <!-- Card สำหรับแสดงจำนวนการจองทั้งหมดและรายได้ทั้งหมด -->
  <div class="row">
    <!-- การ์ดสำหรับจำนวนการจองทั้งหมด -->
    <div class="col-lg-3 col-6">
      <div class="small-box bg-info">
        <div class="inner">
          <h3><?php echo $total_reserve; ?></h3>
          <p>จำนวนการจองทั้งหมด</p>
        </div>
        <div class="icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
      </div>
    </div>
    
    <!-- การ์ดสำหรับรายได้ทั้งหมด -->
    <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
        <div class="inner">
          <h3><?php echo number_format($total_income, 2); ?> ฿</h3>
          <p>รายได้รวมทั้งหมด</p>
        </div>
        <div class="icon">
          <i class="fas fa-money-bill-wave"></i>
        </div>
      </div>
    </div>
  </div>

 <!-- Main content -->
<section class="content">
  <div class="card">
    <div class="card-header card-navy card-outline"><br>
    <div class="card-body p-1">
      <div class="row">
        <div class="col-md-12">
          <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
            <thead>
              <tr role="row" class="info">
                <th tabindex="0" rowspan="1" colspan="1" style="width: 1%;">ลำดับ</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">วันที่จอง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 9%;">เวลาเริ่ม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 9%;">เวลาถึง.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ประเภท</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคา/ชม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ชั่วโมง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ยอดรวม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ชื่อลูกค้า</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เบอร์โทร</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 13%;"></th>
              </tr>
            </thead>
            <tbody>
            <?php
              if ($result) {
                if ($result->num_rows > 0) {
                    $number = 0;
                    while ($row = $result->fetch_assoc()) {
                        $number++;
                        // โดยถือว่า reserve_date อยู่ในรูปแบบ 'YYYY-MM-DD'
                        $date = new DateTime($row["reserve_date"]);
                        $formattedDate = $date->format('d-m-Y'); // รูปแบบวันที่เป็น '05-09-2567'
                        
                        echo "<tr>";
                        echo "<td>" . $number . "</td>";
                        echo "<td>" . $formattedDate . "</td>";
                        echo "<td>" . $row["reserve_time1"] . "</td>";
                        echo "<td>" . $row["reserve_time2"] . "</td>";
                        echo "<td>" . $row["reserve_type"] . "</td>";
                        echo "<td>" . $row["reserve_price"] . "</td>";
                        echo "<td>" . $row["reserve_hour"] . "</td>";
                        echo "<td>" . $row["reserve_total"] . "</td>";           
                        echo "<td>" . $row["reserve_name"] . "</td>";
                        echo "<td>" . $row["reserve_telphone"] . "</td>";
                        echo '<td>
                                  <a class="btn btn-info btn-xs" href="receipt.php?id=' . $row["reserve_id"] . '" target="_blank">
                                    <i class="fas fa-print"></i>
                                  </a>
                                </td>';
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='10'>ไม่มีข้อมูลที่สามารถใช้ได้</td></tr>";
                  }
              } else {
                  echo "Error: " . $sql . "<br>" . $conn->error;
              }
              $conn->close();
              ?>
            </tbody>
          </table>
        </div>
        <div class="col-md-1"></div>
      </div>
    </div>
  </div>
  <!-- /.col -->
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
</script>
</body>
</html>
