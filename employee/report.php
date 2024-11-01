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

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_price, reserve_hour, reserve_more, reserve_total, reserve_name, reserve_telphone 
        FROM reserve_tb 
        WHERE status = 'ดำเนินการแล้ว' 
        ORDER BY reserve_id DESC";  // เรียงข้อมูลตามลำดับ ID จากใหม่ไปเก่า
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
                        echo "<td>" . $row["reserve_time1"] . " </td>";
                        echo "<td>" . $row["reserve_time2"] . " </td>";
                        echo "<td>" . $row["reserve_type"] . "</td>";
                        echo "<td>" . $row["reserve_price"] . " บาท</td>";
                        echo "<td>" . $row["reserve_hour"] . " ชม.</td>";
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
                                          echo '1:30 ชม.';
                                          break;
                                      default:
                                          echo $row['reserve_more'] . ' ชม.';
                                          break;
                                  }
                              } else {
                                  echo 'ไม่มี';
                              }
                        echo "</td>";
                        echo "<td>" . $row["reserve_total"] . " บาท</td>";           
                        echo "<td>" . $row["reserve_name"] . "</td>";
                        echo "<td>" . $row["reserve_telphone"] . "</td>";
                        
                        echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='10'>--ไม่มีข้อมูลที่สามารถใช้ได้--</td></tr>";
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