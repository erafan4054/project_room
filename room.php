<?php
$menu = "room";
include("menu_session.php");  // ดึงข้อมูลผู้ใช้จาก session
include("header.php");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลจากฐานข้อมูล
// $sql = "SELECT room_id, room_type, room_capacity, room_price, room_detail, room_img FROM room_tb";
// $result = $conn->query($sql);

// ดึงข้อมูลจากฐานข้อมูล
$sql = "
    SELECT 
        room_tb.room_id, 
        room_tb.room_capacity, 
        room_tb.room_price, 
        room_tb.room_detail, 
        room_tb.room_img, 
        room_type_tb.room_type_name 
    FROM 
        room_tb
    LEFT JOIN 
        room_type_tb 
    ON 
        room_tb.room_type = room_type_tb.room_type_id";
$result = $conn->query($sql);


?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon fas fa-guitar"></i> จัดการข้อมูลห้องดนตรี</h1>
      </div>
      <div class="col-md-6 text-right">
        <a href="room.insert.php" class="btn btn-danger">
          <i class="fas fa-laptop-medical"></i> เพิ่มข้อมูล
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Main content -->
<section class="content">
  <div class="card">
    <div class="card-header card-navy card-outline"><br>
      <div class="card-body p-1">
        <div class="row">
          <div class="col-md-12">
            <table id="example1" class="table table-bordered table-striped">
              <thead>
              <tr role="row" class="info">
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 8%;">ลำดับ</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ประเภท</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคา/ชม.</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ความจุ</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">รูปภาพห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 30%;">รายละเอียดห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 13%;">แก้ไข/ลบ</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    $number = 0;
                    while ($row = $result->fetch_assoc()) {
                        $number++;
                        echo "<tr>";
                        echo "<td>$number</td>";
                        echo "<td>" . htmlspecialchars($row["room_type_name"], ENT_QUOTES) . "</td>";
                        echo "<td>" . htmlspecialchars($row["room_price"], ENT_QUOTES) .' บาท'. "</td>";
                        echo "<td>" . htmlspecialchars($row["room_capacity"], ENT_QUOTES) .' คน'. "</td>";
                        echo "<td><img src='uploads/" . htmlspecialchars($row["room_img"], ENT_QUOTES) . "' width='100'></td>";
                        echo "<td>" . htmlspecialchars($row["room_detail"], ENT_QUOTES) . "</td>";
                        echo '<td>
                                <a class="btn btn-warning btn-xs" href="room.edit.php?edit=' . $row["room_id"] . '">
                                  <i class="fas fa-pencil-alt"></i> แก้ไข
                                </a>
                                <a class="btn btn-danger btn-xs" href="room.delete.php?delete=' . $row["room_id"] . '" onclick="return confirm(\'คุณแน่ใจที่จะลบใช่ไหม?\')">
                                  <i class="fas fa-trash-alt"></i> ลบ
                                </a>
                              </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>ยังไม่มีข้อมูลที่สามารถใช้ได้</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>         
        </div>
      </div>
    </div>
  </div>
</section>

<?php $conn->close(); ?>

<script>
$(document).ready(function() {
  $('#example1').DataTable();
});
</script>

<?php include('footer.php'); ?>
</body>
</html>
