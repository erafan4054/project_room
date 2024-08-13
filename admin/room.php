<?php
$menu = "room";
include("header.php");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'] ?? null;
    $room_type = $_POST['room_type'];
    $room_price = $_POST['room_price'];
    $room_capacity = $_POST['room_capacity'];
    $room_detail = $_POST['room_detail'];
    $room_img = $_FILES['room_img']['name'] ?? null;
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($room_img);

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if ($room_img && move_uploaded_file($_FILES["room_img"]["tmp_name"], $target_file)) {
        if ($room_id) {
            // การอัพเดทข้อมูล
            $sql = "UPDATE room_tb SET room_type='$room_type', room_price='$room_price', room_capacity='$room_capacity', room_detail='$room_detail', room_img='$room_img' WHERE room_id='$room_id'";
        } else {
            // การเพิ่มข้อมูล
            $sql = "INSERT INTO room_tb (room_type, room_price, room_capacity, room_detail, room_img) VALUES ('$room_type', '$room_price', '$room_detail', '$room_img')";
        }

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                alert('บันทึกใหม่สำเร็จแล้ว');
                window.location.href = window.location.href.split('?')[0];
            </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "ขออภัย เกิดข้อผิดพลาดในการอัปโหลดไฟล์ของคุณ.";
    }
}

// ตรวจสอบการลบข้อมูล
if (isset($_GET['delete'])) {
    include('room.delete.php');
}

// ตรวจสอบการแก้ไขข้อมูล
$edit_id = $_GET['edit'] ?? null;
if ($edit_id) {
    $sql = "SELECT room_type, room_price, room_capacity, room_detail FROM room_tb WHERE room_id='$edit_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('room_id').value = '$edit_id';
                document.getElementsByName('room_type')[0].value = '".$row['room_type']."';
                document.getElementsByName('room_price')[0].value = '".$row['room_price']."';
                document.getElementsByName('room_capacity')[0].value = '".$row['room_capacity']."';
                document.getElementsByName('room_detail')[0].value = '".$row['room_detail']."';
            });
        </script>";
    }
}

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT room_id, room_type, room_capacity, room_price, room_detail, room_img FROM room_tb";
$result = $conn->query($sql);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-guitar"></i> จัดการข้อมูลห้องดนตรี</h1>
  </div>
</section>

<!-- Main content -->
<section class="content">
  <div class="card">
    <div class="card-header card-navy card-outline">
      <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate> 
        <div class="form-row">
          <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี --> 
          <input type="hidden" name="room_id" id="room_id"> 
          <div class="col-md-3 mb-3">  
            <label for="validationCustom01">ประเภทห้อง :</label>
            <input type="text" class="form-control" name="room_type" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">ราคาห้อง/ชม. :</label>
            <input type="text" class="form-control" name="room_price" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">ความจุ/คน :</label>
            <input type="text" class="form-control" name="room_capacity" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">รูปภาพห้อง :</label>
            <input type="file" class="form-control" name="room_img">
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-12 mb-3">
            <label for="validationCustomUsername">รายละเอียดห้อง :</label>
            <textarea name="room_detail" class="form-control" rows="2" required></textarea>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
        </div>
        <button class="btn btn-danger" type="submit" name="submit">บันทึก</button>
        <button class="btn btn-secondary" type="button" onclick="resetForm()">ยกเลิก</button>
      </form>
      <br>
      <div class="card-body p-1">
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
              <thead>
                <tr role="row" class="info">
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 8%;">ลำดับ</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 12%;">ประเภท</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 12%;">ราคา</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 12%;">ความจุ</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">รูปภาพห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">รายละเอียดห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">แก้ไข/ลบ</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $number = 0;
                  while($row = $result->fetch_assoc()) {
                    $number = $number+1;
                    echo "<tr>";
                    echo "<td>" . $number . "</td>";
                    echo "<td>" . $row["room_type"] . "</td>";
                    echo "<td>" . $row["room_price"] . "</td>";
                    echo "<td>" . $row["room_capacity"] . "</td>";
                    echo "<td><img src='../uploads/" . $row["room_img"] . "' width='100'></td>";                    
                    echo "<td>" . $row["room_detail"] . "</td>";
                    echo '<td>
                            <a class="btn btn-warning btn-xs" href="?edit=' . $row["room_id"] . '">
                              <i class="fas fa-pencil-alt"></i> แก้ไข
                            </a>
                            <a class="btn btn-danger btn-xs" href="?delete=' . $row["room_id"] . '" onclick="return confirm(\'คุณแน่ใจที่จะลบใช่ไหม?\')">
                              <i class="fas fa-trash-alt"></i> ลบ
                            </a>
                          </td>';
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='6'>ยังไม่มีข้อมูลที่สามารถใช้ได้</td></tr>";
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
<!-- /.content -->

<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<script>
function resetForm() {
    document.getElementById('room_id').value = '';
    document.getElementsByName('room_type')[0].value = '';
    document.getElementsByName('room_price')[0].value = '';
    document.getElementsByName('room_detail')[0].value = '';
    document.getElementsByName('room_img')[0].value = '';
}
</script>

<?php include('footer.php'); ?>

<script>
$(function () {
  $(".datatable").DataTable();
});
</script>

</body>
</html>
