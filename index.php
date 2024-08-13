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
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_price, reserve_name, reserve_address, reserve_telphone FROM reserve_tb";
$result = $conn->query($sql);

?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-file-alt"></i>  รายงาน</h1>
  </div><!-- /.container-fluid -->
</section>
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
                <th tabindex="0" rowspan="1" colspan="1" style="width: 12%;">วันที่จอง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เวลาเริ่ม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เวลาถึง.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ประเภท</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคาห้อง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ชื่อลูกค้า</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ที่อยู่</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">เบอร์โทร</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 7%;">ปริ้น</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result) {
                  if ($result->num_rows > 0) {
                      $number = 0;
                      while ($row = $result->fetch_assoc()) {
                          $number++;
                          echo "<tr>";
                          echo "<td>" . $number . "</td>";
                          echo "<td>" . $row["reserve_date"] . "</td>";
                          echo "<td>" . $row["reserve_time1"] . "</td>";
                          echo "<td>" . $row["reserve_time2"] . "</td>";
                          echo "<td>" . $row["reserve_type"] . "</td>";
                          echo "<td>" . $row["reserve_price"] . "</td>";          
                          echo "<td>" . $row["reserve_name"] . "</td>";
                          echo "<td>" . $row["reserve_address"] . "</td>";
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
