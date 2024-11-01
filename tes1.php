<?php
$menu = "room";
include("header.php");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = ""; // รหัสผ่านฐานข้อมูล
$dbname = "project_room"; // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $room_type = $_POST['room_type'];
    $room_price = $_POST['room_price'];
    $room_detail = $_POST['room_detail'];
    $room_img = $_FILES['room_img']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($room_img);

    // ตรวจสอบและสร้างโฟลเดอร์ 'uploads' ถ้ายังไม่มี
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // ย้ายไฟล์ที่อัพโหลดไปยังโฟลเดอร์ปลายทาง
    if (move_uploaded_file($_FILES["room_img"]["tmp_name"], $target_file)) {
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $sql = "INSERT INTO room_tb (room_id, room_type, room_price, room_detail, room_img)
                VALUES ('$room_id', '$room_type', '$room_price', '$room_detail', '$room_img')";

        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT room_id, room_type, room_price, room_detail, room_img FROM room_tb";
$result = $conn->query($sql);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-user-friends"></i> ข้อมูลห้องดนตรี</h1>
  </div>
</section>

<!-- Main content -->
<section class="content">
  <div class="card">
    <div class="card-header card-navy card-outline">
      <form action="" method="post" class="needs-validation" novalidate>
        <div class="form-row">
          <div class="col-md-4 mb-3">
            <label for="validationCustom01">รหัสห้อง :</label>
            <input type="text" class="form-control" name="room_id" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-4 mb-3">  
            <label for="validationCustom01">ประเภทห้อง :</label>
            <input type="text" class="form-control" name="room_type" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-4 mb-3">
            <label for="validationCustom01">ราคาห้อง :</label>
            <input type="text" class="form-control" name="room_price" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="validationCustom01">รูปภาพห้อง :</label>
            <input type="file" class="form-control" name="room_img" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="validationCustomUsername">รายละเอียดห้อง :</label>
            <textarea name="room_detail" class="form-control" rows="2" required></textarea>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">บันทึก</button>
        <button class="btn btn-secondary" type="button">ยกเลิก</button>
      </form>
      <br>
      <div class="card-body p-1">
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
              <thead>
                <tr role="row" class="info">
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">รหัสห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ประเภทห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ราคาห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">รูปภาพห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">รายละเอียดห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                                    echo "<td>" . $row["room_id"] . "</td>";
                                    echo "<td>" . $row["room_type"] . "</td>";
                                    echo "<td>" . $row["room_price"] . "</td>";
                                    echo "<td><img src='uploads/" . $row["room_img"] . "' width='100'></td>";
                                    echo "<td>" . $row["room_detail"] . "</td>";
                                    echo '<td>
                            <a class="btn btn-warning btn-xs" href="" target="_blank">
                              <i class="fas fa-pencil-alt"></i> แก้ไข
                            </a>
                            <a class="btn btn-danger btn-xs" href="" target="_blank">
                              <i class="fas fa-trash-alt"></i> ลบ
                            </a>
                          </td>';
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='5'>No data available</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
          <div class="col-md-1"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->

<?php include('footer.php'); ?>

<script>
$(function () {
  $(".datatable").DataTable();
});
</script>

</body>
</html>

<?php
$conn->close();
?>









<?php
$menu = "employee";
include("header.php");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = ""; // รหัสผ่านฐานข้อมูล
$dbname = "project_room"; // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_username = $_POST['employee_username'];
    $employee_password = $_POST['employee_password'];
    $employee_name = $_POST['employee_name'];

    // เพิ่มข้อมูลลงในฐานข้อมูล
    $sql = "INSERT INTO employee_tb (employee_username, employee_password, employee_name)
            VALUES ('$employee_username', '$employee_password', '$employee_name')";

    if ($conn->query($sql) === TRUE) {
        
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT employee_id, employee_username, employee_password, employee_name FROM employee_tb";
$result = $conn->query($sql);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-user-friends"></i> จัดการข้อมูลพนักงาน</h1>
  </div>
</section>

<!-- Main content -->
<section class="content">
  <div class="card">
    <div class="card-header card-navy card-outline">
      <form action="" method="post" class="needs-validation" novalidate>
        <div class="form-row">
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">ID :</label>
            <input type="text" class="form-control" name="employee_id" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">  
            <label for="validationCustom01">Username :</label>
            <input type="text" class="form-control" name="employee_username" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">Password :</label>
            <input type="text" class="form-control" name="employee_password" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">Name :</label>
            <input type="text" class="form-control" name="employee_name" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">บันทึก</button>
        <button class="btn btn-secondary" type="button">ยกเลิก</button>
      </form>
      <br>
      <div class="card-body p-1">
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
              <thead>
                <tr role="row" class="info">
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ID</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">Username</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">Password</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">Name</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["employee_id"] . "</td>";
                    echo "<td>" . $row["employee_username"] . "</td>";
                    echo "<td>" . $row["employee_password"] . "</td>";
                    echo "<td>" . $row["employee_name"] . "</td>";
                    echo '<td>
                            <a class="btn btn-warning btn-xs" href="" target="_blank">
                              <i class="fas fa-pencil-alt"></i> แก้ไข
                            </a>
                            <a class="btn btn-danger btn-xs" href="" target="_blank">
                              <i class="fas fa-trash-alt"></i> ลบ
                            </a>
                          </td>';
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='5'>No data available</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
          <div class="col-md-1"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->

<?php include('footer.php'); ?>

<script>
$(function () {
  $(".datatable").DataTable();
});
</script>

</body>
</html>

<?php
$conn->close();
?>

// ใส่ที่menu_l //
<li class="nav-item">
            <a href="doc.php" class="nav-link <?php if($menu=="doc"){echo "active";} ?>">
              <i class="nav-icon fas fa-tools"></i>
              <p>จัดการข้อมูลรับซ่อม
                <i class="right fa fa-chevron-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="jong.php" class="nav-link <?php if($submenu=="booking"){echo "active";} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>เพิ่มข้อมูลรับซ่อม</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="show.php" class="nav-link <?php if($submenu=="display"){echo "active";} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>แสดงรายการซ่อม</p>
                </a>
              </li>
            </ul>
          </li>

<!-- หน้าเพิ่มข้อมูลพนักงานที่มีแก้ไขและลบอยู่ในโฟเดอเดียวกัน -->

<?php
$menu = "employee";
include("header.php");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = ""; // รหัสผ่านฐานข้อมูล
$dbname = "project_room"; // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // การอัพเดทข้อมูล
        $employee_id = $_POST['employee_id'];
        $employee_username = $_POST['employee_username'];
        $employee_password = $_POST['employee_password'];
        $employee_name = $_POST['employee_name'];

        $sql = "UPDATE employee_tb SET employee_username='$employee_username', employee_password='$employee_password', employee_name='$employee_name' WHERE employee_id='$employee_id'";

        if ($conn->query($sql) === TRUE) {
          echo "<script>
          alert('บันทึกใหม่สำเร็จแล้ว');
          window.location.href = window.location.href.split('?')[0];
      </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // การเพิ่มข้อมูล
        $employee_username = $_POST['employee_username'];
        $employee_password = $_POST['employee_password'];
        $employee_name = $_POST['employee_name'];

        $sql = "INSERT INTO employee_tb (employee_username, employee_password, employee_name)
                VALUES ('$employee_username', '$employee_password', '$employee_name')";

        if ($conn->query($sql) === TRUE) {
            echo "บันทึกใหม่สำเร็จแล้ว";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// ตรวจสอบการลบข้อมูล
if (isset($_GET['delete'])) {
    $employee_id = $_GET['delete'];
    $sql = "DELETE FROM employee_tb WHERE employee_id='$employee_id'";

    if ($conn->query($sql) === TRUE) {
        echo "ลบเรียบร้อยแล้ว";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT employee_id, employee_username, employee_password, employee_name FROM employee_tb";
$result = $conn->query($sql);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-user-friends"></i> จัดการข้อมูลพนักงาน</h1>
  </div>
</section>

<!-- Main content -->
<section class="content">
  <div class="card">
    <div class="card-header card-navy card-outline">
      <form action="" method="post" class="needs-validation" novalidate>
        <div class="form-row">
          <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
          <input type="hidden" name="employee_id">
          <div class="col-md-4 mb-3">  
            <label for="validationCustom01">ชื่อผู้ใช้ :</label>
            <input type="text" class="form-control" name="employee_username" >
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-4 mb-3">
            <label for="validationCustom01">รหัสผ่าน :</label>
            <input type="text" class="form-control" name="employee_password" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-4 mb-3">
            <label for="validationCustom01">ชื่อพนักงาน :</label>
            <input type="text" class="form-control" name="employee_name" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
        </div>
        <button class="btn btn-primary" type="submit" name="submit">บันทึก</button>
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
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ลำดับ</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">ชื่อผู้ใช้</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">รหัสผ่าน</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">ชื่อพนักงาน</th>
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
                    echo "<td>" . $row["employee_username"] . "</td>";
                    echo "<td>" . $row["employee_password"] . "</td>";
                    echo "<td>" . $row["employee_name"] . "</td>";
                    echo '<td>
                            <a class="btn btn-warning btn-xs" href="?edit=' . $row["employee_id"] . '">
                              <i class="fas fa-pencil-alt"></i> แก้ไข
                            </a>
                            <a class="btn btn-danger btn-xs" href="?delete=' . $row["employee_id"] . '" onclick="return confirm(\'คุณแน่ใจที่จะลบใช่ไหม?\')">
                              <i class="fas fa-trash-alt"></i> ลบ
                            </a>
                          </td>';
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='5'>ยังไม่มีข้อมูลที่สามารถใช้ได้</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
          <div class="col-md-1"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->

<?php
// ตรวจสอบการแก้ไขข้อมูล
if (isset($_GET['edit'])) {
    $employee_id = $_GET['edit'];
    $sql = "SELECT employee_username, employee_password, employee_name FROM employee_tb WHERE employee_id='$employee_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo '<script>
            document.getElementsByName("employee_id")[0].value = "' . $employee_id . '";
            document.getElementsByName("employee_username")[0].value = "' . $row["employee_username"] . '";
            document.getElementsByName("employee_password")[0].value = "' . $row["employee_password"] . '";
            document.getElementsByName("employee_name")[0].value = "' . $row["employee_name"] . '";
            document.getElementsByName("submit")[0].name = "update";
        </script>';
    }
}
?>

<?php include('footer.php'); ?>

<script>
$(function () {
  $(".datatable").DataTable();
});

function resetForm() {
    document.querySelector("form").reset();
    document.getElementsByName("submit")[0].name = "submit";
}
</script>

</body>
</html>

<?php
$conn->close();
?>

<!-- //หน้าเพิ่มข้อมูลพนักงานที่มีแก้ไขและลบอยู่ในโฟเดอเดียวกัน -->

<!-- หน้าข้อมูลห้องที่มีแก้ไขและลบอยู่ในโฟเดอเดียวกัน -->

<?php
$menu = "room";
include("header.php");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = ""; // รหัสผ่านฐานข้อมูล
$dbname = "project_room"; // ชื่อฐานข้อมูล

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
    $room_detail = $_POST['room_detail'];
    $room_img = $_FILES['room_img']['name'] ?? null;
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($room_img);

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if ($room_img && move_uploaded_file($_FILES["room_img"]["tmp_name"], $target_file)) {
        if ($room_id) {
            // การอัพเดทข้อมูล
            $sql = "UPDATE room_tb SET room_type='$room_type', room_price='$room_price', room_detail='$room_detail', room_img='$room_img' WHERE room_id='$room_id'";
        } else {
            // การเพิ่มข้อมูล
            $sql = "INSERT INTO room_tb (room_type, room_price, room_detail, room_img) VALUES ('$room_type', '$room_price', '$room_detail', '$room_img')";
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
    $room_id = $_GET['delete'];
    $sql = "DELETE FROM room_tb WHERE room_id='$room_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            alert('ลบเรียบร้อยแล้ว');
            window.location.href = window.location.href.split('?')[0];
        </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT room_id, room_type, room_price, room_detail, room_img FROM room_tb";
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
            <label for="validationCustom01">ราคาห้อง :</label>
            <input type="text" class="form-control" name="room_price" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">รูปภาพห้อง :</label>
            <input type="file" class="form-control" name="room_img">
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustomUsername">รายละเอียดห้อง :</label>
            <textarea name="room_detail" class="form-control" rows="2" required></textarea>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
        </div>
        <button class="btn btn-primary" type="submit" name="submit">บันทึก</button>
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
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ลำดับ</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ประเภทห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ราคาห้อง</th>
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
                    echo "<td><img src='uploads/" . $row["room_img"] . "' width='100'></td>";
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
          <div class="col-md-1"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->

<?php
// ตรวจสอบการแก้ไขข้อมูล
if (isset($_GET['edit'])) {
    $room_id = $_GET['edit'];
    $sql = "SELECT room_type, room_price, room_detail FROM room_tb WHERE room_id='$room_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<script>
            document.getElementById('room_id').value = '$room_id';
            document.getElementsByName('room_type')[0].value = '".$row['room_type']."';
            document.getElementsByName('room_price')[0].value = '".$row['room_price']."';
            document.getElementsByName('room_detail')[0].value = '".$row['room_detail']."';
        </script>";
    }
}

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
<!-- //หน้าข้อมูลห้องที่มีแก้ไขและลบอยู่ในโฟเดอเดียวกัน -->

          <!-- หน้าจองอดีตที่จะเพิ่มระบบซ่อม -->
          <?php
$menu = "jong";
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

// การลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = $conn->prepare("DELETE FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $delete_id);
    if ($sql->execute()) {
        // ใส่ echo "<script>alert('ลบข้อมูลสำเร็จ');</script>"; เพื่อให้แจ้งเตือน
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $sql->close();
}

// การแก้ไขข้อมูล
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = $conn->prepare("SELECT * FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $edit_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $sql->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserve_id = intval($_POST['reserve_id']);
    $reserve_name = $conn->real_escape_string($_POST['reserve_name']);
    $reserve_time1 = $conn->real_escape_string($_POST['reserve_time1']);
    $reserve_time2 = $conn->real_escape_string($_POST['reserve_time2']);
    $reserve_type = $conn->real_escape_string($_POST['reserve_type']);
    $reserve_date = $conn->real_escape_string($_POST['reserve_date']);
    $reserve_telphone = $conn->real_escape_string($_POST['reserve_telphone']);
    $reserve_address = $conn->real_escape_string($_POST['reserve_address']);
    $reserve_price = $conn->real_escape_string($_POST['reserve_price']);

    if ($reserve_id > 0) {
        // Update existing record
        $sql = $conn->prepare("UPDATE reserve_tb SET reserve_name=?, reserve_time1=?, reserve_time2=?, reserve_type=?, reserve_date=?, reserve_telphone=?, reserve_address=?, reserve_price=? WHERE reserve_id=?");
        $sql->bind_param("ssssssssi", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_id);
    } else {
        // Insert new record
        $sql = $conn->prepare("INSERT INTO reserve_tb (reserve_name, reserve_time1, reserve_time2, reserve_type, reserve_date, reserve_telphone, reserve_address, reserve_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssssss", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price);
    }

    if ($sql->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $sql->close();
}

$conn->close();
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-address-card"></i> เพิ่มข้อมูลลูกค้า</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-navy card-outline"></div>
                <div class="card-body">
                <!-- Main content -->
                <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="form-row">
                        <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                        <input type="hidden" name="reserve_id" value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ชื่อลูกค้า :</label>
                            <input type="text" class="form-control" name="reserve_name" value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                            <input type="time" class="form-control" name="reserve_time1" value="<?php echo isset($row['reserve_time1']) ? $row['reserve_time1'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                            <input type="time" class="form-control" name="reserve_time2" value="<?php echo isset($row['reserve_time2']) ? $row['reserve_time2'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ประเภทห้อง :</label>
                            <select name="reserve_type" class="form-control" required>
                                <option value="">เลือกห้อง</option>
                                <option value="ห้องล่าง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                                <option value="ห้องกลาง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                                <option value="ห้องใหญ่" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>                
                    </div>
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">เบอร์โทร :</label>
                            <input type="text" class="form-control" name="reserve_telphone" value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ที่อยู่ :</label>
                            <input type="text" class="form-control" name="reserve_address" value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">วันที่ :</label>
                            <input type="date" class="form-control" name="reserve_date" value="<?php echo isset($row['reserve_date']) ? $row['reserve_date'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ราคาห้อง :</label>
                            <select name="reserve_price" class="form-control" required>
                                <option value="">ราคาห้อง</option>
                                <option value="300" <?php echo (isset($row['reserve_price']) && $row['reserve_price'] == '300') ? 'selected' : ''; ?>>300</option>
                                <option value="200" <?php echo (isset($row['reserve_price']) && $row['reserve_price'] == '200') ? 'selected' : ''; ?>>200</option>
                                <option value="150" <?php echo (isset($row['reserve_price']) && $row['reserve_price'] == '150') ? 'selected' : ''; ?>>150</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">บันทึก</button>
                    <button class="btn btn-secondary" type="button" onclick="window.location.href='show.php';">ยกเลิก</button>
                </form>                
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

<!-- //หน้าจองอดีตที่จะเพิ่มระบบซ่อม -->

<!-- หน้าจองข้อมูลที่มีแก้ไขและลบอยู่ในโฟเดอเดียวกัน -->
<?php
$menu = "jong";
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

// การลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = $conn->prepare("DELETE FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $delete_id);
    if ($sql->execute()) {
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $sql->close();
}

// การแก้ไขข้อมูล
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = $conn->prepare("SELECT * FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $edit_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $sql->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserve_id = intval($_POST['reserve_id']);
    $reserve_name = $conn->real_escape_string($_POST['reserve_name']);
    $reserve_time1 = $conn->real_escape_string($_POST['reserve_time1']);
    $reserve_time2 = $conn->real_escape_string($_POST['reserve_time2']);
    $reserve_type = $conn->real_escape_string($_POST['reserve_type']);
    $reserve_date = $conn->real_escape_string($_POST['reserve_date']);
    $reserve_telphone = $conn->real_escape_string($_POST['reserve_telphone']);
    $reserve_address = $conn->real_escape_string($_POST['reserve_address']);
    $reserve_price = $conn->real_escape_string($_POST['reserve_price']);
    $reserve_detail = $conn->real_escape_string($_POST['reserve_detail']);

    if ($reserve_id > 0) {
        $sql = $conn->prepare("UPDATE reserve_tb SET reserve_name=?, reserve_time1=?, reserve_time2=?, reserve_type=?, reserve_date=?, reserve_telphone=?, reserve_address=?, reserve_price=?, reserve_detail=? WHERE reserve_id=?");
        $sql->bind_param("sssssssssi", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_detail, $reserve_id);
    } else {
        $sql = $conn->prepare("INSERT INTO reserve_tb (reserve_name, reserve_time1, reserve_time2, reserve_type, reserve_date, reserve_telphone, reserve_address, reserve_price, reserve_detail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("sssssssss", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_detail);
    }

    if ($sql->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $sql->close();
}

$conn->close();
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-address-card"></i> เพิ่มข้อมูลลูกค้า</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-navy card-outline"></div>
                <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="form-row">
                        <input type="hidden" name="reserve_id" value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ชื่อลูกค้า :</label>
                            <input type="text" class="form-control" name="reserve_name" value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                            <input type="time" class="form-control" name="reserve_time1" value="<?php echo isset($row['reserve_time1']) ? $row['reserve_time1'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                            <input type="time" class="form-control" name="reserve_time2" value="<?php echo isset($row['reserve_time2']) ? $row['reserve_time2'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ประเภทห้อง :</label>
                            <select name="reserve_type" class="form-control" required onchange="updateRoomInfo()">
                                <option value="">เลือกห้อง</option>
                                <option value="ห้องล่าง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                                <option value="ห้องกลาง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                                <option value="ห้องใหญ่" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>                
                    </div>
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">เบอร์โทร :</label>
                            <input type="text" class="form-control" name="reserve_telphone" value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ที่อยู่ :</label>
                            <input type="text" class="form-control" name="reserve_address" value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">วันที่ :</label>
                            <input type="date" class="form-control" name="reserve_date" value="<?php echo isset($row['reserve_date']) ? $row['reserve_date'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ราคาห้อง :</label>
                            <input type="text" class="form-control" id="reserve_price" name="reserve_price" value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required readonly>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>           
                        <div class="col-md-6 mb-3">
                            <label for="roomImage">รูปห้อง :</label>
                            <div id="roomImage">
                                <img id="roomImg" src="" alt="Room Image" style="max-width: 100%; height: auto;">
                            </div>
                        </div>                   
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom05">รายละเอียด :</label>
                            <textarea class="form-control" name="reserve_detail" rows="7" id="reserve_detail" required><?php echo isset($row['reserve_detail']) ? $row['reserve_detail'] : ''; ?></textarea>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">บันทึก</button>
                    <button class="btn btn-secondary" type="button" onclick="window.location.href='show.php';">ยกเลิก</button>
                </form>                
                </div>
            </div>
        </div>
    </div>
  <!-- /.col -->
</section>
<!-- /.content -->
<?php include('footer.php'); ?>
<script>
function updateRoomInfo() {
    const reserveType = document.querySelector('select[name="reserve_type"]').value;
    const reservePriceInput = document.getElementById('reserve_price');
    const roomImg = document.getElementById('roomImg');
    const reserveDetailInput = document.getElementById('reserve_detail');
    
    let price = '';
    let imgSrc = '';
    let detail = '';

    switch (reserveType) {
        case 'ห้องล่าง':
            price = '150';
            imgSrc = 'uploads/ห้องซ้อมล่าง.jpg';
            detail = 'รายละเอียดห้องล่าง ห้องจะอยู่ข้างล่างทางขวา มีอุปกรณ์ ไมค์โครโฟน 2 ตัว กีตาร์ไฟฟ้า 2 ตัว  เบส 1 ตัว กลองชุด 1 ชุด และเปียโน บันจุคน 5 คน';
            break;
        case 'ห้องกลาง':
            price = '200';
            imgSrc = 'uploads/ห้องซ้อมกลาง.jpg';
            detail = 'รายละเอียดห้องกลาง ห้องจะอยู่ข้างบนทางซ้าย มีอุปกรณ์ครบทุกอย่าง ไมค์โครโฟน 2 ตัว กีตาร์ไฟฟ้า 2 ตัว  เบส 1 ตัว กลองใหญ่ 1 ชุด และเปียโน  บันจุคน 7 คน';
            break;
        case 'ห้องใหญ่':
            price = '300';
            imgSrc = 'uploads/ห้องซ้อมใหญ่.jpg';
            detail = 'รายละเอียดห้องใหญ่ ห้องจะอยู่ข้างบน มีอุปกรณ์ครบทุกอย่าง ไมค์โครโฟน 3 ตัว กีตาร์ไฟฟ้า 2 ตัว โปร่ง 1 ตัว เบส 1 ตัว กลองใหญ่ 1 ชุด และเปียโนตัวใหญ่ บันจุคน 10 คน';
            break;
        default:
            price = '';
            imgSrc = '';
            detail = '';
    }

    reservePriceInput.value = price;
    roomImg.src = imgSrc;
    reserveDetailInput.value = detail;
}

document.addEventListener('DOMContentLoaded', function () {
    updateRoomInfo();
});

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
<!--//หน้าจองข้อมูลที่มีแก้ไขและลบอยู่ในโฟเดอเดียวกัน  -->



<!--หน้าจองข้อมูลที่มีแก้ไขและลบอยู่ในโฟเดอเดียวกันที่ล่าสุด2  -->
<?php
$menu = "jong";
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

// การลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = $conn->prepare("DELETE FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $delete_id);
    if ($sql->execute()) {
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $sql->close();
}

// การแก้ไขข้อมูล
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = $conn->prepare("SELECT * FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $edit_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $sql->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserve_id = intval($_POST['reserve_id']);
    $reserve_name = $conn->real_escape_string($_POST['reserve_name']);
    $reserve_time1 = $conn->real_escape_string($_POST['reserve_time1']);
    $reserve_time2 = $conn->real_escape_string($_POST['reserve_time2']);
    $reserve_type = $conn->real_escape_string($_POST['reserve_type']);
    $reserve_date = $conn->real_escape_string($_POST['reserve_date']);
    $reserve_telphone = $conn->real_escape_string($_POST['reserve_telphone']);
    $reserve_address = $conn->real_escape_string($_POST['reserve_address']);
    $reserve_price = $conn->real_escape_string($_POST['reserve_price']);
    $reserve_detail = $conn->real_escape_string($_POST['reserve_detail']);

    if ($reserve_id > 0) {
        $sql = $conn->prepare("UPDATE reserve_tb SET reserve_name=?, reserve_time1=?, reserve_time2=?, reserve_type=?, reserve_date=?, reserve_telphone=?, reserve_address=?, reserve_price=?, reserve_detail=? WHERE reserve_id=?");
        $sql->bind_param("sssssssssi", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_detail, $reserve_id);
    } else {
        $sql = $conn->prepare("INSERT INTO reserve_tb (reserve_name, reserve_time1, reserve_time2, reserve_type, reserve_date, reserve_telphone, reserve_address, reserve_price, reserve_detail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("sssssssss", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_detail);
    }

    if ($sql->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $sql->close();
}

$conn->close();
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-address-card"></i> เพิ่มข้อมูลลูกค้า</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-navy card-outline"></div>
                <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="form-row">
                        <input type="hidden" name="reserve_id" value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ชื่อลูกค้า :</label>
                            <input type="text" class="form-control" name="reserve_name" value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                            <input type="time" class="form-control" name="reserve_time1" value="<?php echo isset($row['reserve_time1']) ? $row['reserve_time1'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                            <input type="time" class="form-control" name="reserve_time2" value="<?php echo isset($row['reserve_time2']) ? $row['reserve_time2'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ประเภทห้อง :</label>
                            <select name="reserve_type" class="form-control" required onchange="updateRoomInfo()">
                                <option value="">เลือกห้อง</option>
                                <option value="ห้องล่าง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                                <option value="ห้องกลาง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                                <option value="ห้องใหญ่" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>                
                    </div>
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">เบอร์โทร :</label>
                            <input type="text" class="form-control" name="reserve_telphone" value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ที่อยู่ :</label>
                            <input type="text" class="form-control" name="reserve_address" value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">วันที่ :</label>
                            <input type="date" class="form-control" name="reserve_date" value="<?php echo isset($row['reserve_date']) ? $row['reserve_date'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ราคาห้อง :</label>
                            <input type="text" class="form-control" id="reserve_price" name="reserve_price" value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required readonly>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>           
                        <div class="col-md-6 mb-3">
                            <label for="roomImage">รูปห้อง :</label>
                            <div id="roomImage">
                                <img id="roomImg" src="" alt="Room Image" style="max-width: 100%; height: auto;">
                            </div>
                        </div>                   
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom05">รายละเอียด :</label>
                            <textarea class="form-control" name="reserve_detail" rows="7" id="reserve_detail" required><?php echo isset($row['reserve_detail']) ? $row['reserve_detail'] : ''; ?></textarea>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">บันทึก</button>
                    <button class="btn btn-secondary" type="button" onclick="window.location.href='show.php';">ยกเลิก</button>
                </form>                
                </div>
            </div>
        </div>
    </div>
  <!-- /.col -->
</section>
<!-- /.content -->
<?php include('footer.php'); ?>
<script>
function updateRoomInfo() {
    const reserveType = document.querySelector('select[name="reserve_type"]').value;
    const reservePriceInput = document.getElementById('reserve_price');
    const roomImg = document.getElementById('roomImg');
    const reserveDetailInput = document.getElementById('reserve_detail');
    
    let price = '';
    let imgSrc = '';
    let detail = '';

    switch (reserveType) {
        case 'ห้องล่าง':
            price = '150';
            imgSrc = 'uploads/ห้องซ้อมล่าง.jpg';
            detail = 'รายละเอียดห้องล่าง ห้องจะอยู่ข้างล่างทางขวา มีอุปกรณ์ ไมค์โครโฟน 2 ตัว กีตาร์ไฟฟ้า 2 ตัว  เบส 1 ตัว กลองชุด 1 ชุด และเปียโน บันจุคน 5 คน';
            break;
        case 'ห้องกลาง':
            price = '200';
            imgSrc = 'uploads/ห้องซ้อมกลาง.jpg';
            detail = 'รายละเอียดห้องกลาง ห้องจะอยู่ข้างบนทางซ้าย มีอุปกรณ์ครบทุกอย่าง ไมค์โครโฟน 2 ตัว กีตาร์ไฟฟ้า 2 ตัว  เบส 1 ตัว กลองใหญ่ 1 ชุด และเปียโน  บันจุคน 7 คน';
            break;
        case 'ห้องใหญ่':
            price = '300';
            imgSrc = 'uploads/ห้องซ้อมใหญ่.jpg';
            detail = 'รายละเอียดห้องใหญ่ ห้องจะอยู่ข้างบน มีอุปกรณ์ครบทุกอย่าง ไมค์โครโฟน 3 ตัว กีตาร์ไฟฟ้า 2 ตัว โปร่ง 1 ตัว เบส 1 ตัว กลองใหญ่ 1 ชุด และเปียโนตัวใหญ่ บันจุคน 10 คน';
            break;
        default:
            price = '';
            imgSrc = '';
            detail = '';
    }

    reservePriceInput.value = price;
    roomImg.src = imgSrc;
    reserveDetailInput.value = detail;
}

document.addEventListener('DOMContentLoaded', function () {
    updateRoomInfo();
});

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
<!--?/หน้าจองข้อมูลที่มีแก้ไขและลบอยู่ในโฟเดอเดียวกันที่ล่าสุด2  -->

<!--หน้าจองข้อมูลที่ยังไม่มีแก้ไขและลบได้  -->
<?php
$menu = "jong";
include("header.php");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = ""; // รหัสผ่านฐานข้อมูล
$dbname = "project_room"; // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบเมื่อมีการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $reserve_name = $_POST['reserve_name'];
    $reserve_time1 = $_POST['reserve_time1'];
    $reserve_type = $_POST['reserve_type'];
    $reserve_date = $_POST['reserve_date'];
    $reserve_telphone = $_POST['reserve_telphone'];
    $reserve_address = $_POST['reserve_address'];
    $reserve_price = $_POST['reserve_price'];
    $reserve_time2 = $_POST['reserve_time2'];

    // สร้างคำสั่ง SQL ในการบันทึกข้อมูล
    $sql = "INSERT INTO reserve_tb (reserve_name, reserve_time1, reserve_time2, reserve_type, reserve_date, reserve_telphone, reserve_address, reserve_price)
            VALUES ('$reserve_name', '$reserve_time1', '$reserve_time2', '$reserve_type', '$reserve_date', '$reserve_telphone', '$reserve_address', '$reserve_price')";

    // บันทึกข้อมูลลงในฐานข้อมูล
    if ($conn->query($sql) === TRUE) {
        echo "สร้างบันทึกใหม่สำเร็จแล้ว";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-address-card"></i> เพิ่มข้อมูลลูกค้า</h1>
  </div><!-- /.container-fluid -->
</section>
<section class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-navy card-outline"></div>
                <div class="card-body">
                <!-- Main content -->
                <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ชื่อลูกค้า :</label>
                            <input type="text" class="form-control" name="reserve_name" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">เบอร์โทร :</label>
                            <input type="text" class="form-control" name="reserve_telphone" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ที่อยู่ :</label>
                            <input type="text" class="form-control" name="reserve_address" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ราคาห้อง :</label>
                            <input type="text" class="form-control" name="reserve_price" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ประเภทห้อง :</label>
                            <select name="reserve_type" class="form-control" required>
                                <option value="">เลือกห้อง</option>
                                <option value="ห้องล่าง">ห้องล่าง</option>
                                <option value="ห้องกลาง">ห้องกลาง</option>
                                <option value="ห้องใหญ่">ห้องใหญ่</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                            <input type="time" class="form-control" name="reserve_time1" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                            <input type="time" class="form-control" name="reserve_time2" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">วันที่ :</label>
                            <input type="date" class="form-control" name="reserve_date" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                                             
                    </div>
                    <button class="btn btn-primary" type="submit">บันทึก</button>
                    <button class="btn btn-secondary" type="button">ยกเลิก</button>
                </form>                
                </div>
            </div>
        </div>
  <!-- /.col -->
</section>
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
<!--หน้าจองข้อมูลที่ยังไม่มีแก้ไขและลบได้  -->

<!--หน้าจองข้อมูลที่มีกาด แต่ไม่สามารถกดได้  -->
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

// การลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = $conn->prepare("DELETE FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $delete_id);
    if ($sql->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $sql->close();
}

// การแก้ไขข้อมูล
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = $conn->prepare("SELECT * FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $edit_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $sql->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserve_id = intval($_POST['reserve_id']);
    $reserve_name = $conn->real_escape_string($_POST['reserve_name']);
    $reserve_time1 = $conn->real_escape_string($_POST['reserve_time1']);
    $reserve_time2 = $conn->real_escape_string($_POST['reserve_time2']);
    $reserve_type = $conn->real_escape_string($_POST['reserve_type']);
    $reserve_date = $conn->real_escape_string($_POST['reserve_date']);
    $reserve_telphone = $conn->real_escape_string($_POST['reserve_telphone']);
    $reserve_address = $conn->real_escape_string($_POST['reserve_address']);
    $reserve_price = $conn->real_escape_string($_POST['reserve_price']);

    if ($reserve_id > 0) {
        // Update existing record
        $sql = $conn->prepare("UPDATE reserve_tb SET reserve_name=?, reserve_time1=?, reserve_time2=?, reserve_type=?, reserve_date=?, reserve_telphone=?, reserve_address=?, reserve_price=? WHERE reserve_id=?");
        $sql->bind_param("ssssssssi", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_id);
    } else {
        // Insert new record
        $sql = $conn->prepare("INSERT INTO reserve_tb (reserve_name, reserve_time1, reserve_time2, reserve_type, reserve_date, reserve_telphone, reserve_address, reserve_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssssss", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price);
    }

    if ($sql->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $sql->close();
}

?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-address-card"></i> เพิ่มข้อมูลลูกค้า</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-navy card-outline"></div>
                <div class="card-body">
                <!-- Main content -->
                <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="form-row">
                        <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                        <input type="hidden" name="reserve_id" value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ชื่อลูกค้า :</label>
                            <input type="text" class="form-control" name="reserve_name" value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">เบอร์โทร :</label>
                            <input type="text" class="form-control" name="reserve_telphone" value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ที่อยู่ :</label>
                            <input type="text" class="form-control" name="reserve_address" value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ราคาห้อง :</label>
                            <input type="text" class="form-control" name="reserve_price" value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>    
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ประเภทห้อง :</label>
                            <select name="reserve_type" class="form-control" required>
                                <option value="">เลือกห้อง</option>
                                <option value="ห้องล่าง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                                <option value="ห้องกลาง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                                <option value="ห้องใหญ่" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>    
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                            <input type="time" class="form-control" name="reserve_time1" value="<?php echo isset($row['reserve_time1']) ? $row['reserve_time1'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                            <input type="time" class="form-control" name="reserve_time2" value="<?php echo isset($row['reserve_time2']) ? $row['reserve_time2'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">วันที่ :</label>
                            <input type="date" class="form-control" name="reserve_date" value="<?php echo isset($row['reserve_date']) ? $row['reserve_date'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room selection -->
                    <div class="form-row">
                        <div class="col-md-12">
                            <h4>เลือกห้อง :</h4>
                            <div class="row">
                                <?php
                                $sql = "SELECT * FROM room_tb"; // ชื่อตาราง room_tb ที่เก็บข้อมูลห้อง
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($room = $result->fetch_assoc()) {
                                        echo '<div class="col-md-4">';
                                        echo '<div class="card">';
                                        echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                                        echo '<div class="card-body">';
                                        echo '<h5 class="card-type">' . $room['room_type'] . ' (' . $room['room_capacity'] . ' คน)</h5>';
                                        echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                                        echo '<p class="card-text">สถานะ : ' . $room['room_type'] . '</p>';
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
                    </div>
                    
                    <button class="btn btn-primary" type="submit">บันทึก</button>
                    <button class="btn btn-secondary" type="button" onclick="window.location.href='show.php';">ยกเลิก</button>
                </form>                
                </div>
            </div>
        </div>
    </div>
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

<?php
$conn->close();
?>
<!--//หน้าจองข้อมูลที่มีกาด แต่ไม่สามารถกดได้  -->

<!-- css การ์ดเลือกห้อง -->
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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>

<!-- //css การ์ดเลือกห้อง -->


<!-- รายงาน -->
<?php
$menu = "report";
 include("header.php"); ?>

<script type="text/javascript">
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
</script>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
      <h1><i class="nav-icon fas fa-file-alt"></i>  รายงาน</h1>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
  <section class="content">
    
    
    
    <div class="card">
      <div class="card-header card-navy card-outline">
        
      </div>
      <br>
      <div class="card-body p-1">
        <div class="row">
          <div class="col-md-1">
            
          </div>
          <div class="col-md-12">
            <table id="example1" class="table table-bordered table-striped dataTable" 
                   role="grid" aria-describedby="example1_info">
              <thead>
                <tr role="row" class="info">
                  <th  tabindex="0" rowspan="1" colspan="1" style="width: 2%;">ลำดับ</th>
                  <th  tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ประเภท</th>
                  <th  tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคาห้อง</th>
                  <th  tabindex="0" rowspan="1" colspan="1" style="width: 10%;">วันที่จอง</th>
                  <th  tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เวลา/ชม.</th>
                  <th  tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ชื่อลูกค้า</th>
                  <th  tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ที่อยู่</th>
                  <th  tabindex="0" rowspan="1" colspan="1" style="width: 15%;">เบอร์โทร</th>
                  
                  <th  tabindex="0" rowspan="1" colspan="1" style="width: 5%;">พิมพ์</th>
                  
                </tr>
              </thead>
              <tbody>
                
                <tr>
                   <td>
                    1
                  </td>
                  <td>
                    ห้องบน
                  </td>
                  <td>
                    150
                  </td>
                  <td>
                    21/01/24
                  </td>
                   <td>
                   2
                  </td>
                   <td>
                  Erafan
                  </td>
                  <td>
                  1/3
                  </td>
                  <td>
                  0980778545
                  </td>
                  <td>
                  

                    <a class="btn btn-danger btn-xs" href="" target="_blank">
                      <i class="fas fa-print">
                      </i> 
                    </a>
                  </td>
                  
                  
                  <tr>
                   <td>
                    2
                  </td>
                  <td>
                    ห้องกลาง
                  </td>
                  <td>
                    200
                  </td>
                  <td>
                    04/01/24
                  </td>
                   <td>
                   1
                  </td>
                   <td>
                  San
                  </td>
                  <td>
                  1/3
                  </td>
                  <td>
                  0980778545
                  </td>
                  <td>
                  

                    <a class="btn btn-danger btn-xs" href="" target="_blank">
                      <i class="fas fa-print">
                      </i> 
                    </a>
                  </td>
                  <tr>
                   <td>
                    3
                  </td>
                  <td>
                    ห้องบน
                  </td>
                  <td>
                    150
                  </td>
                  <td>
                    05/01/24
                  </td>
                   <td>
                   2
                  </td>
                   <td>
                  Krom
                  </td>
                  <td>
                  3/9
                  </td>
                  <td>
                  0980778545
                  </td>
                  <td>
                  

                    <a class="btn btn-danger btn-xs" href="" target="_blank">
                      <i class="fas fa-print">
                      </i> 
                    </a>
                  </td>
                  <tr>
                   <td>
                    4
                  </td>
                  <td>
                    ห้องกลาง
                  </td>
                  <td>
                    200
                  </td>
                  <td>
                    27/01/24
                  </td>
                   <td>
                   2
                  </td>
                   <td>
                  ris
                  </td>
                  <td>
                  1/3
                  </td>
                  <td>
                  0980778545
                  </td>
                  <td>
                  

                    <a class="btn btn-danger btn-xs" href="" target="_blank">
                      <i class="fas fa-print">
                      </i> 
                    </a>
                  </td>
                  <tr>
                   <td>
                    5
                  </td>
                  <td>
                    ห้องใหญ่
                  </td>
                  <td>
                    300
                  </td>
                  <td>
                    21/01/24
                  </td>
                   <td>
                   1
                  </td>
                   <td>
                  din
                  </td>
                  <td>
                  7/8
                  </td>
                  <td>
                  0980778545
                  </td>
                  <td>
                  

                    <a class="btn btn-danger btn-xs" href="" target="_blank">
                      <i class="fas fa-print">
                      </i> 
                    </a>
                  </td>
               
                
              </tbody>
            </table>
            
          </div>
          <div class="col-md-1" >
            
          </div>
        </div>
      </div>
      
    </div>
    
    
    
  </div>
  <!-- /.col -->
</div>
</section>
<!-- /.content -->

    
<?php include('footer.php'); ?>

<script>
  $(function () {
    $(".datatable").DataTable();
    // $('#example2').DataTable({
    //   "paging": true,
    //   "lengthChange": false,
    //   "searching": false,
    //   "ordering": true,
    //   "info": true,
    //   "autoWidth": false,
    // http://fordev22.com/
    // });
  });
</script>
  
</body>
</html>
<!-- http://fordev22.com/ -->



<!-- หน้าจองล่าสุด22-08-67(เพิ่มข้อมูลลูกค้าอยู่ด้านบน) -->
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

// การลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = $conn->prepare("DELETE FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $delete_id);
    if ($sql->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $sql->close();
}

// การแก้ไขข้อมูล
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = $conn->prepare("SELECT * FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $edit_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $sql->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserve_id = intval($_POST['reserve_id']);
    $reserve_name = $conn->real_escape_string($_POST['reserve_name']);
    $reserve_time1 = $conn->real_escape_string($_POST['reserve_time1']);
    $reserve_time2 = $conn->real_escape_string($_POST['reserve_time2']);
    $reserve_type = $conn->real_escape_string($_POST['reserve_type']);
    $reserve_date = $conn->real_escape_string($_POST['reserve_date']);
    $reserve_telphone = $conn->real_escape_string($_POST['reserve_telphone']);
    $reserve_address = $conn->real_escape_string($_POST['reserve_address']);
    $reserve_price = $conn->real_escape_string($_POST['reserve_price']);

    if ($reserve_id > 0) {
        // Update existing record
        $sql = $conn->prepare("UPDATE reserve_tb SET reserve_name=?, reserve_time1=?, reserve_time2=?, reserve_type=?, reserve_date=?, reserve_telphone=?, reserve_address=?, reserve_price=? WHERE reserve_id=?");
        $sql->bind_param("ssssssssi", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_id);
    } else {
        // Insert new record
        $sql = $conn->prepare("INSERT INTO reserve_tb (reserve_name, reserve_time1, reserve_time2, reserve_type, reserve_date, reserve_telphone, reserve_address, reserve_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssssss", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price);
    }

    if ($sql->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $sql->close();
}

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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>


<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลลูกค้า</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card1">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate> 
                <div class="form-row">
                        <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                        <input type="hidden" name="reserve_id" value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ชื่อลูกค้า :</label>
                            <input type="text" class="form-control" name="reserve_name" value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">เบอร์โทร :</label>
                            <input type="text" class="form-control" name="reserve_telphone" value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ที่อยู่ :</label>
                            <input type="text" class="form-control" name="reserve_address" value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ราคาห้อง :</label>
                            <input type="text" class="form-control" name="reserve_price" value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>    
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">วันที่ :</label>
                            <input type="date" class="form-control" name="reserve_date" value="<?php echo isset($row['reserve_date']) ? $row['reserve_date'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>    
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                            <input type="time" class="form-control" name="reserve_time1" value="<?php echo isset($row['reserve_time1']) ? $row['reserve_time1'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                            <input type="time" class="form-control" name="reserve_time2" value="<?php echo isset($row['reserve_time2']) ? $row['reserve_time2'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ประเภทห้อง :</label>
                            <select name="reserve_type" class="form-control" id="reserve_type" required>
                                <option value="">เลือกห้อง</option>
                                <option value="ห้องล่าง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                                <option value="ห้องกลาง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                                <option value="ห้องใหญ่" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room selection -->
                    <div class="form-row">
                        <div class="col-md-12">
                            <h4>เลือกห้องซ้อม :</h4>
                            <div class="row">
                                <?php
                                $sql = "SELECT * FROM room_tb"; // ตาราง room_tb ที่เก็บข้อมูลห้อง
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($room = $result->fetch_assoc()) {
                                        echo '<div class="col-md-4">';
                                        echo '<div class="card" onclick="updateRoomType(\'' . $room['room_type'] . '\')">';
                                        echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                                        echo '<div class="card-body">';
                                        echo '<h5 class="card-type">' . $room['room_type'] . ' (' . $room['room_capacity'] . ' คน)</h5>';
                                        echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                                        echo '<p class="card-text">ราคา : ' . $room['room_price'] . ' /ชม.</p>';
                                        echo '<p class="card-text">สถานะ : ' . $room['room_status'] . '</p>';
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
                    </div>

                    <button class="btn btn-danger" type="submit">บันทึก</button>
                    <button class="btn btn-secondary" type="button" onclick="window.location.href='show.php';">ยกเลิก</button>
                </form>                
                </div>
            </div>
        </div>
    </div>
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

function updateRoomType(roomType) {
  document.getElementById('reserve_type').value = roomType;
}
</script>
</body>
</html>

<?php
$conn->close();
?>
<!-- //หน้าจองล่าสุด22-08-67(เพิ่มข้อมูลลูกค้าอยู่ด้านบน) -->

<!-- หน้าจองล่าสุด26-08-67(เพิ่มข้อมูลลูกค้าอยู่ด้านบน) -->
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

// การลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = $conn->prepare("DELETE FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $delete_id);
    if ($sql->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $sql->close();
}

// การแก้ไขข้อมูล
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = $conn->prepare("SELECT * FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $edit_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $sql->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserve_id = intval($_POST['reserve_id']);
    $reserve_name = $conn->real_escape_string($_POST['reserve_name']);
    $reserve_time1 = $conn->real_escape_string($_POST['reserve_time1']);
    $reserve_time2 = $conn->real_escape_string($_POST['reserve_time2']);
    $reserve_type = $conn->real_escape_string($_POST['reserve_type']);
    $reserve_date = $conn->real_escape_string($_POST['reserve_date']);
    $reserve_telphone = $conn->real_escape_string($_POST['reserve_telphone']);
    $reserve_address = $conn->real_escape_string($_POST['reserve_address']);
    $reserve_price = $conn->real_escape_string($_POST['reserve_price']);

    if ($reserve_id > 0) {
        // Update existing record
        $sql = $conn->prepare("UPDATE reserve_tb SET reserve_name=?, reserve_time1=?, reserve_time2=?, reserve_type=?, reserve_date=?, reserve_telphone=?, reserve_address=?, reserve_price=? WHERE reserve_id=?");
        $sql->bind_param("ssssssssi", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_id);
    } else {
        // Insert new record
        $sql = $conn->prepare("INSERT INTO reserve_tb (reserve_name, reserve_time1, reserve_time2, reserve_type, reserve_date, reserve_telphone, reserve_address, reserve_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssssss", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price);
    }

    if ($sql->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $sql->close();
}

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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>


<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลลูกค้า</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card1">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate> 

                    <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                    <input type="hidden" name="reserve_id" value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">
                            
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">วันที่ :</label>
                            <input type="date" class="form-control" name="reserve_date" value="<?php echo isset($row['reserve_date']) ? $row['reserve_date'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>    
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                            <input type="time" class="form-control" name="reserve_time1" value="<?php echo isset($row['reserve_time1']) ? $row['reserve_time1'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                            <input type="time" class="form-control" name="reserve_time2" value="<?php echo isset($row['reserve_time2']) ? $row['reserve_time2'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ประเภทห้อง :</label>
                            <select name="reserve_type" class="form-control" id="reserve_type" required>
                                <option value="">เลือกห้อง</option>
                                <option value="ห้องล่าง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                                <option value="ห้องกลาง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                                <option value="ห้องใหญ่" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room selection -->
                    <div class="form-row">
                        <div class="col-md-12">
                            <h4>เลือกห้องซ้อม :</h4>
                            <div class="row">
                                <?php
                                $sql = "SELECT * FROM room_tb"; // ตาราง room_tb ที่เก็บข้อมูลห้อง
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($room = $result->fetch_assoc()) {
                                        echo '<div class="col-md-4">';
                                        echo '<div class="card" onclick="updateRoomType(\'' . $room['room_type'] . '\')">';
                                        echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                                        echo '<div class="card-body">';
                                        echo '<h5 class="card-type">' . $room['room_type'] . ' (' . $room['room_capacity'] . ' คน)</h5>';
                                        echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                                        echo '<p class="card-text">ราคา : ' . $room['room_price'] . ' /ชม.</p>';
                                        echo '<p class="card-text">สถานะ : ' . $room['room_status'] . '</p>';
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
                    
                    <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ชื่อลูกค้า :</label>
                            <input type="text" class="form-control" name="reserve_name" value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">เบอร์โทร :</label>
                            <input type="text" class="form-control" name="reserve_telphone" value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ที่อยู่ :</label>
                            <input type="text" class="form-control" name="reserve_address" value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ราคาห้อง :</label>
                            <input type="text" class="form-control" name="reserve_price" value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-danger" type="submit">บันทึก</button>
                    <button class="btn btn-secondary" type="button" onclick="window.location.href='show.php';">ยกเลิก</button>
                </form>                
                </div>
            </div>
        </div>
    </div>
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

function updateRoomType(roomType) {
  document.getElementById('reserve_type').value = roomType;
}
</script>
</body>
</html>

<?php
$conn->close();
?>

<!-- //หน้าจองล่าสุด26-08-67(เพิ่มข้อมูลลูกค้าอยู่ด้านบน) -->

<!-- หน้าtes2ล่าสุด26-08-67(ไม่รวมกับจอง เพราะอินเสิพไม่ได้) -->


<?php
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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลลูกค้า</h1>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card1">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>

                <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                <input type="hidden" name="reserve_id"
                    value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">

                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">วันที่ :</label>
                        <input type="date" class="form-control" name="reserve_date" value="<?php echo $reserve_date; ?>"
                            required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                        <input type="time" class="form-control" name="reserve_time1"
                            value="<?php echo $reserve_time1; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                        <input type="time" class="form-control" name="reserve_time2"
                            value="<?php echo $reserve_time2; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom04">ประเภทห้อง :</label>
                        <select name="reserve_type" class="form-control" id="reserve_type" required
                            onchange="this.form.submit();">
                            <option value="">เลือกห้อง</option>
                            <option value="ห้องล่าง" <?php echo ($reserve_type == 'ห้องล่าง') ? 'selected' : ''; ?>>
                                ห้องล่าง
                            </option>
                            <option value="ห้องกลาง" <?php echo ($reserve_type == 'ห้องกลาง') ? 'selected' : ''; ?>>
                                ห้องกลาง
                            </option>
                            <option value="ห้องใหญ่" <?php echo ($reserve_type == 'ห้องใหญ่') ? 'selected' : ''; ?>>
                                ห้องใหญ่
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>
                <!-- ไม่มีปุ่มค้นหาอีกต่อไป เนื่องจากการ submit อัตโนมัติ -->


<!-- Room selection -->
<div class="form-row">
    <div class="col-md-12">
        <h4>เลือกห้องซ้อม :</h4>
        <div class="row">
            <?php
            // ค้นหาห้องทั้งหมด
            $sql = "SELECT * FROM room_tb";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($room = $result->fetch_assoc()) {
                    // ตรวจสอบสถานะห้องจากตารางการจอง
                    $room_type = $room['room_type'];
                    $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                   WHERE reserve_type = '$room_type' 
                                   AND reserve_date = '$reserve_date' 
                                   AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                    $status_result = $conn->query($status_sql);
                    $status_row = $status_result->fetch_assoc();
                    $is_reserved = $status_row['reserved_count'] > 0;

                    echo '<div class="col-md-4">';
                    echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="selectRoomType(\'' . $room['room_type'] . '\')">';
                    echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-type">' . $room['room_type'] . ' (' . $room['room_capacity'] . ' คน)</h5>';
                    echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                    echo '<p class="card-text">ราคา : ' . $room['room_price'] . ' /ชม.</p>';
                    
                    // ตรวจสอบสถานะห้อง
                    if ($is_reserved) {
                        echo '<p class="card-text text-unavailable">สถานะ : ไม่ว่าง</p>';
                    } else {
                        echo '<p class="card-text text-available">สถานะ : ว่าง</p>';
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


<!-- JavaScript function to update the form -->
<script>
function selectRoomType(roomType) {
    document.getElementById('reserve_type').value = roomType;
    // Submit the form to update the room selection
    document.querySelector('form').submit();
}
</script>

                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ชื่อลูกค้า :</label>
                        <input type="text" class="form-control" name="reserve_name"
                            value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">เบอร์โทร :</label>
                        <input type="text" class="form-control" name="reserve_telphone"
                            value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ที่อยู่ :</label>
                        <input type="text" class="form-control" name="reserve_address"
                            value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ราคาห้อง :</label>
                        <input type="text" class="form-control" name="reserve_price"
                            value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>

                <button class="btn btn-danger" type="submit">บันทึก</button>
                <button class="btn btn-secondary" type="button"
                    onclick="window.location.href='show.php';">ยกเลิก</button>
            </form>
        </div>
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

<!-- //หน้าtes2ล่าสุด26-08-67(ไม่รวมกับจอง เพราะอินเสิพไม่ได้ กะทำให้) -->


<!-- หน้าtes2ล่าสุด26-08-67(ไม่รวมกับจอง เพราะอินเสิพไม่ได้ เปลี่ยนสีสถานะ) -->
<?php
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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลลูกค้า</h1>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card1">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>

                <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                <input type="hidden" name="reserve_id"
                    value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">

                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">วันที่ :</label>
                        <input type="date" class="form-control" name="reserve_date" value="<?php echo $reserve_date; ?>"
                            required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                        <input type="time" class="form-control" name="reserve_time1"
                            value="<?php echo $reserve_time1; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                        <input type="time" class="form-control" name="reserve_time2"
                            value="<?php echo $reserve_time2; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom04">ประเภทห้อง :</label>
                        <select name="reserve_type" class="form-control" id="reserve_type" required
                            onchange="this.form.submit();">
                            <option value="">เลือกห้อง</option>
                            <option value="ห้องล่าง" <?php echo ($reserve_type == 'ห้องล่าง') ? 'selected' : ''; ?>>
                                ห้องล่าง
                            </option>
                            <option value="ห้องกลาง" <?php echo ($reserve_type == 'ห้องกลาง') ? 'selected' : ''; ?>>
                                ห้องกลาง
                            </option>
                            <option value="ห้องใหญ่" <?php echo ($reserve_type == 'ห้องใหญ่') ? 'selected' : ''; ?>>
                                ห้องใหญ่
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>
                <!-- ไม่มีปุ่มค้นหาอีกต่อไป เนื่องจากการ submit อัตโนมัติ -->


<!-- Room selection -->
<div class="form-row">
    <div class="col-md-12">
        <h4>เลือกห้องซ้อม :</h4>
        <div class="row">
            <?php
            // ค้นหาห้องทั้งหมด
            $sql = "SELECT * FROM room_tb";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($room = $result->fetch_assoc()) {
                    // ตรวจสอบสถานะห้องจากตารางการจอง
                    $room_type = $room['room_type'];
                    $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                   WHERE reserve_type = '$room_type' 
                                   AND reserve_date = '$reserve_date' 
                                   AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                    $status_result = $conn->query($status_sql);
                    $status_row = $status_result->fetch_assoc();
                    $is_reserved = $status_row['reserved_count'] > 0;

                    // แสดงข้อมูลห้องซ้อม
                    echo '<div class="col-md-4">';
                    echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="selectRoomType(\'' . $room['room_type'] . '\')">';
                    echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-type">' . $room['room_type'] . ' (' . $room['room_capacity'] . ' คน)</h5>';
                    echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                    echo '<p class="card-text">ราคา : ' . $room['room_price'] . ' /ชม.</p>';

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


<!-- JavaScript function to update the form -->
<script>
function selectRoomType(roomType) {
    document.getElementById('reserve_type').value = roomType;
    // Submit the form to update the room selection
    document.querySelector('form').submit();
}
</script>

                <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ชื่อลูกค้า :</label>
                        <input type="text" class="form-control" name="reserve_name"
                            value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">เบอร์โทร :</label>
                        <input type="text" class="form-control" name="reserve_telphone"
                            value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ที่อยู่ :</label>
                        <input type="text" class="form-control" name="reserve_address"
                            value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ราคาห้อง :</label>
                        <input type="text" class="form-control" name="reserve_price"
                            value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>

                <button class="btn btn-danger" type="submit">บันทึก</button>
                <button class="btn btn-secondary" type="button"
                    onclick="window.location.href='show.php';">ยกเลิก</button>
            </form>
        </div>
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

<!-- //หน้าtes2ล่าสุด26-08-67(ไม่รวมกับจอง เพราะอินเสิพไม่ได้ เปลี่ยนสีสถานะ) -->

<!-- หน้าtes2ล่าสุด27-08-67(ไม่รวมกับจอง เพราะอินเสิพไม่ได้ เปลี่ยนสีสถานะ) -->

<?php
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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลลูกค้า</h1>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card1">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>

                <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                <input type="hidden" name="reserve_id"
                    value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">

                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">วันที่ :</label>
                        <input type="date" class="form-control" name="reserve_date" value="<?php echo $reserve_date; ?>"
                            required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                        <input type="time" class="form-control" name="reserve_time1"
                            value="<?php echo $reserve_time1; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                        <input type="time" class="form-control" name="reserve_time2"
                            value="<?php echo $reserve_time2; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom04">ประเภทห้อง :</label>
                        <select name="reserve_type" class="form-control" id="reserve_type" required
                            onchange="this.form.submit();">
                            <option value="">เลือกห้อง</option>
                            <option value="ห้องล่าง" <?php echo ($reserve_type == 'ห้องล่าง') ? 'selected' : ''; ?>>
                                ห้องล่าง
                            </option>
                            <option value="ห้องกลาง" <?php echo ($reserve_type == 'ห้องกลาง') ? 'selected' : ''; ?>>
                                ห้องกลาง
                            </option>
                            <option value="ห้องใหญ่" <?php echo ($reserve_type == 'ห้องใหญ่') ? 'selected' : ''; ?>>
                                ห้องใหญ่
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>
                <!-- ไม่มีปุ่มค้นหาอีกต่อไป เนื่องจากการ submit อัตโนมัติ -->


<!-- Room selection -->
<div class="form-row">
    <div class="col-md-12">
        <h4>เลือกห้องซ้อม :</h4>
        <div class="row">
            <?php
            // ค้นหาห้องทั้งหมด
            $sql = "SELECT * FROM room_tb";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($room = $result->fetch_assoc()) {
                    // ตรวจสอบสถานะห้องจากตารางการจอง
                    $room_type = $room['room_type'];
                    $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                   WHERE reserve_type = '$room_type' 
                                   AND reserve_date = '$reserve_date' 
                                   AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                    $status_result = $conn->query($status_sql);
                    $status_row = $status_result->fetch_assoc();
                    $is_reserved = $status_row['reserved_count'] > 0;

                    // แสดงข้อมูลห้องซ้อม
                    echo '<div class="col-md-4">';
                    echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="selectRoomType(\'' . $room['room_type'] . '\')">';
                    echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-type">' . $room['room_type'] . ' (' . $room['room_capacity'] . ' คน)</h5>';
                    echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                    echo '<p class="card-text">ราคา : ' . $room['room_price'] . ' /ชม.</p>';

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


<!-- JavaScript function to update the form -->
<script>
function selectRoomType(roomType) {
    document.getElementById('reserve_type').value = roomType;
    // Submit the form to update the room selection
    document.querySelector('form').submit();
}
</script>

                <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ชื่อลูกค้า :</label>
                        <input type="text" class="form-control" name="reserve_name"
                            value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">เบอร์โทร :</label>
                        <input type="text" class="form-control" name="reserve_telphone"
                            value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ที่อยู่ :</label>
                        <input type="text" class="form-control" name="reserve_address"
                            value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">ราคาห้อง :</label>
                        <input type="text" class="form-control" name="reserve_price"
                            value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>

                <button class="btn btn-danger" type="submit">บันทึก</button>
                <button class="btn btn-secondary" type="button"
                    onclick="window.location.href='show.php';">ยกเลิก</button>
            </form>
        </div>
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

<!-- //หน้าtes2ล่าสุด27-08-67(ไม่รวมกับจอง เพราะอินเสิพไม่ได้ เปลี่ยนสีสถานะ) -->


<!-- หน้าtes2ล่าสุด28-08-67(ไม่รวมกับจอง เพราะอินเสิพได้ ) -->
<?php
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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลลูกค้า</h1>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card1">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>

                <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                <input type="hidden" name="reserve_id"
                    value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">

                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">วันที่ :</label>
                        <input type="date" class="form-control" name="reserve_date" value="<?php echo $reserve_date; ?>"
                            required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                        <input type="time" class="form-control" name="reserve_time1"
                            value="<?php echo $reserve_time1; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                        <input type="time" class="form-control" name="reserve_time2"
                            value="<?php echo $reserve_time2; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom04">ประเภทห้อง :</label>
                        <select name="reserve_type" class="form-control" id="reserve_type" required
                            onchange="this.form.submit();">
                            <option value="">เลือกห้อง</option>
                            <option value="ห้องล่าง" <?php echo ($reserve_type == 'ห้องล่าง') ? 'selected' : ''; ?>>
                                ห้องล่าง
                            </option>
                            <option value="ห้องกลาง" <?php echo ($reserve_type == 'ห้องกลาง') ? 'selected' : ''; ?>>
                                ห้องกลาง
                            </option>
                            <option value="ห้องใหญ่" <?php echo ($reserve_type == 'ห้องใหญ่') ? 'selected' : ''; ?>>
                                ห้องใหญ่
                            </option>
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
            // ค้นหาห้องทั้งหมด
            $sql = "SELECT * FROM room_tb";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($room = $result->fetch_assoc()) {
                    // ตรวจสอบสถานะห้องจากตารางการจอง
                    $room_type = $room['room_type'];
                    $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                   WHERE reserve_type = '$room_type' 
                                   AND reserve_date = '$reserve_date' 
                                   AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                    $status_result = $conn->query($status_sql);
                    $status_row = $status_result->fetch_assoc();
                    $is_reserved = $status_row['reserved_count'] > 0;

                    // แสดงข้อมูลห้องซ้อม
                    echo '<div class="col-md-4">';
                    echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="openReserveModal(\'' . $room['room_type'] . '\')">';
                    echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-type">' . $room['room_type'] . ' (ความจุ: ' . $room['room_capacity'] . ' คน)</h5>';
                    echo '<p class="card-text">' . $room['room_detail'] . '( ราคา: ' . $room['room_price'] . ' บาท/ชม.)</p>';
                    // echo '<p class="card-text">ราคา : ' . $room['room_price'] . ' บาท/ชม.</p>';

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
    <input type="hidden" name="reserve_type" value="<?php echo htmlspecialchars($reserve_type); ?>">
    

    <div class="modal-header">
    <h4 class="modal-title" id="reserveModalLabel"><i class="nav-icon fas fa-plus-square"></i> เพิ่มข้อมูลลูกค้า</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="history.back();" aria-label="Close"></button>
</div>



    <div class="modal-body">
        <div class="mb-3">
            <label for="reserve_name" class="form-label">ชื่อลูกค้า: </label>
            <input type="text" class="form-control" name="reserve_name" id="reserve_name" required>
        </div>
        <div class="mb-3">
            <label for="reserve_telphone" class="form-label">เบอร์โทร: </label>
            <input type="text" class="form-control" name="reserve_telphone" id="reserve_telphone" required>
        </div>
        <div class="mb-3">
            <label for="reserve_address" class="form-label">ที่อยู่: </label>
            <textarea type="text" class="form-control" name="reserve_address" id="reserve_address" rows="2" required></textarea>
        </div>
        <div class="mb-3">
            <label for="reserve_price" class="form-label">ราคาห้อง: </label>
            <input type="number" class="form-control" name="reserve_price" id="reserve_price" required>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" onclick="history.back();">ยกเลิก</button>
        <button type="submit" class="btn btn-danger">บันทึก</button>
    </div>
</form>

        </div>
    </div>
</div>

<script>
function openReserveModal(roomType) {
    document.getElementById('reserve_type').value = roomType;
    var modal = new bootstrap.Modal(document.getElementById('reserveModal'));
    modal.show();
}
</script>
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

<!-- //หน้าtes2ล่าสุด28-08-67(ไม่รวมกับจอง เพราะอินเสิพได้ ) -->

<!-- หน้าtes2ล่าสุด30-08-67(จองได้ แต่แก้ไขและคำนวนราคายังไม่ได้) -->

<?php
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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> จัดการข้อมูลจอง</h1>
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
                        <input type="date" class="form-control" name="reserve_date" value="<?php echo $reserve_date; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                        <input type="time" class="form-control" name="reserve_time1" value="<?php echo $reserve_time1; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                        <input type="time" class="form-control" name="reserve_time2" value="<?php echo $reserve_time2; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <!-- <div class="col-md-3 mb-3">
                        <label for="validationCustom04">ประเภทห้อง :</label>
                        <select name="reserve_type" class="form-control" id="reserve_type" required onchange="this.form.submit();">
                            <option value="">เลือกห้อง</option>
                            <option value="ห้องล่าง" <?php echo ($reserve_type == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                            <option value="ห้องกลาง" <?php echo ($reserve_type == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                            <option value="ห้องใหญ่" <?php echo ($reserve_type == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                        </select>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div> -->
                </div>
            </form>

            <!-- ไม่มีปุ่มค้นหาอีกต่อไป เนื่องจากการ submit อัตโนมัติ -->

            <!-- Room selection -->
            <div class="form-row">
                <div class="col-md-12">
                    <h4>เลือกห้องซ้อม :</h4>
                    <div class="row">
                        <?php
                        // ค้นหาห้องทั้งหมด
                        $sql = "SELECT * FROM room_tb";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($room = $result->fetch_assoc()) {
                                // ตรวจสอบสถานะห้องจากตารางการจอง
                                $room_type = $room['room_type'];
                                $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                               WHERE reserve_type = '$room_type' 
                                               AND reserve_date = '$reserve_date' 
                                               AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                                $status_result = $conn->query($status_sql);
                                $status_row = $status_result->fetch_assoc();
                                $is_reserved = $status_row['reserved_count'] > 0;

                                // แสดงข้อมูลห้องซ้อม
                                echo '<div class="col-md-4">';
                                echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="openReserveModal(\'' . $room['room_type'] . '\')">';
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
                                    <div class="mb-3">
                                        <label for="reserve_name" class="form-label">ชื่อลูกค้า: </label>
                                        <input type="text" class="form-control" name="reserve_name" id="reserve_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_telphone" class="form-label">เบอร์โทร: </label>
                                        <input type="text" class="form-control" name="reserve_telphone" id="reserve_telphone" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_address" class="form-label">ที่อยู่: </label>
                                        <textarea class="form-control" name="reserve_address" id="reserve_address" rows="2" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_type" class="form-label">ประเภท: </label>
                                        <input  class="form-control" type="text" id="modal_reserve_type" name="reserve_type" value="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_price" class="form-label">ราคาห้อง: </label>
                                        <input type="number" class="form-control" name="reserve_price" id="reserve_price" required>
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

                <script>
                    function openReserveModal(roomType) {
                        document.getElementById('modal_reserve_type').value = roomType;
                        var modal = new bootstrap.Modal(document.getElementById('reserveModal'));
                        modal.show();
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
<!-- //หน้าtes2ล่าสุด30-08-67(จองได้ แต่แก้ไขและคำนวนราคายังไม่ได้) -->



<!-- หน้าจองที่ไม่มีสถานะ(จองได้ แก้ไขได้ลบได้) -->
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

// การลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = $conn->prepare("DELETE FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $delete_id);
    
    if ($sql->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $sql->close();
}

// การแก้ไขข้อมูล
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = $conn->prepare("SELECT * FROM reserve_tb WHERE reserve_id = ?");
    $sql->bind_param("i", $edit_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $sql->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserve_id = intval($_POST['reserve_id']);
    $reserve_name = $conn->real_escape_string($_POST['reserve_name']);
    $reserve_time1 = $conn->real_escape_string($_POST['reserve_time1']);
    $reserve_time2 = $conn->real_escape_string($_POST['reserve_time2']);
    $reserve_type = $conn->real_escape_string($_POST['reserve_type']);
    $reserve_date = $conn->real_escape_string($_POST['reserve_date']);
    $reserve_telphone = $conn->real_escape_string($_POST['reserve_telphone']);
    $reserve_address = $conn->real_escape_string($_POST['reserve_address']);
    $reserve_price = $conn->real_escape_string($_POST['reserve_price']);

    if ($reserve_id > 0) {
        // อัพเดทข้อมูลที่มีอยู่
        $sql = $conn->prepare("UPDATE reserve_tb SET reserve_name=?, reserve_time1=?, reserve_time2=?, reserve_type=?, reserve_date=?, reserve_telphone=?, reserve_address=?, reserve_price=? WHERE reserve_id=?");
        $sql->bind_param("ssssssssi", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price, $reserve_id);
    } else {
        // แทรกบันทึกใหม่
        $sql = $conn->prepare("INSERT INTO reserve_tb (reserve_name, reserve_time1, reserve_time2, reserve_type, reserve_date, reserve_telphone, reserve_address, reserve_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssssss", $reserve_name, $reserve_time1, $reserve_time2, $reserve_type, $reserve_date, $reserve_telphone, $reserve_address, $reserve_price);
    }

    if ($sql->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href='show.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $sql->close();
}

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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>


<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid"> 
    <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลลูกค้า</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card1">
        <div class="card-header card-navy card-outline"><br>
            <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate> 

                    <!-- ใช้อินพุตเพื่อให้แก้ไขได้โดยไม่ต้องใส่ไอดี -->
                    <input type="hidden" name="reserve_id" value="<?php echo isset($row['reserve_id']) ? $row['reserve_id'] : ''; ?>">
                            
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">วันที่ :</label>
                            <input type="date" class="form-control" name="reserve_date" value="<?php echo isset($row['reserve_date']) ? $row['reserve_date'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>    
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                            <input type="time" class="form-control" name="reserve_time1" value="<?php echo isset($row['reserve_time1']) ? $row['reserve_time1'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                            <input type="time" class="form-control" name="reserve_time2" value="<?php echo isset($row['reserve_time2']) ? $row['reserve_time2'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom04">ประเภทห้อง :</label>
                            <select name="reserve_type" class="form-control" id="reserve_type" required>
                                <option value="">เลือกห้อง</option>
                                <option value="ห้องล่าง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องล่าง') ? 'selected' : ''; ?>>ห้องล่าง</option>
                                <option value="ห้องกลาง" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องกลาง') ? 'selected' : ''; ?>>ห้องกลาง</option>
                                <option value="ห้องใหญ่" <?php echo (isset($row['reserve_type']) && $row['reserve_type'] == 'ห้องใหญ่') ? 'selected' : ''; ?>>ห้องใหญ่</option>
                            </select>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room selection -->
                    <div class="form-row">
                        <div class="col-md-12">
                            <h4>เลือกห้องซ้อม :</h4>
                            <div class="row">
                                <?php
                                $sql = "SELECT * FROM room_tb"; // ตาราง room_tb ที่เก็บข้อมูลห้อง
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($room = $result->fetch_assoc()) {
                                        echo '<div class="col-md-4">';
                                        echo '<div class="card" onclick="updateRoomType(\'' . $room['room_type'] . '\')">';
                                        echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
                                        echo '<div class="card-body">';
                                        echo '<h5 class="card-type">' . $room['room_type'] . ' (' . $room['room_capacity'] . ' คน)</h5>';
                                        echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                                        echo '<p class="card-text">ราคา : ' . $room['room_price'] . ' /ชม.</p>';
                                        echo '<p class="card-text">สถานะ : ' . $room['room_status'] . '</p>';
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
                    
                    <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ชื่อลูกค้า :</label>
                            <input type="text" class="form-control" name="reserve_name" value="<?php echo isset($row['reserve_name']) ? $row['reserve_name'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">เบอร์โทร :</label>
                            <input type="text" class="form-control" name="reserve_telphone" value="<?php echo isset($row['reserve_telphone']) ? $row['reserve_telphone'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ที่อยู่ :</label>
                            <input type="text" class="form-control" name="reserve_address" value="<?php echo isset($row['reserve_address']) ? $row['reserve_address'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="validationCustom01">ราคาห้อง :</label>
                            <input type="text" class="form-control" name="reserve_price" value="<?php echo isset($row['reserve_price']) ? $row['reserve_price'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                **กรุณากรอกข้อมูล
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-danger" type="submit">บันทึก</button>
                    <button class="btn btn-secondary" type="button" onclick="window.location.href='show.php';">ยกเลิก</button>
                </form>                
                </div>
            </div>
        </div>
    </div>
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

function updateRoomType(roomType) {
  document.getElementById('reserve_type').value = roomType;
}
</script>
</body>
</html>

<?php
$conn->close();
?>

<!-- //หน้าจองที่ไม่มีสถานะ(จองได้ แก้ไขได้ลบได้) -->

<!-- 3-9-67หน้าจองที่มีสถานะ(ยังไม่กำหนดเวลา) -->
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
    max-height: 150px; /* ปรับความสูงของรูป */
    object-fit: cover; /* ปรับการแสดงผลของรูปให้เต็มในกรณีที่รูปไม่ตรงกับขนาดที่กำหนด */
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
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> จัดการข้อมูลจอง</h1>
        </div>
      <div class="col-md-6 text-right">
        <a href="show.php?action=add" class="btn btn-danger"> <!-- สีเขียวbtn-success สีแดงbtn-danger ฟ้าbtn-primary เทาbtn-secondary-->
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
                        <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                        <input type="time" class="form-control" name="reserve_time1" value="<?php echo $reserve_time1; ?>" required onchange="this.form.submit();">
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                        <input type="time" class="form-control" name="reserve_time2" value="<?php echo $reserve_time2; ?>" required onchange="this.form.submit();">
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
                        // ค้นหาห้องทั้งหมด
                        $sql = "SELECT * FROM room_tb";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($room = $result->fetch_assoc()) {
                                // ตรวจสอบสถานะห้องจากตารางการจอง
                                $room_type = $room['room_type'];
                                $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                               WHERE reserve_type = '$room_type' 
                                               AND reserve_date = '$reserve_date' 
                                               AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                                $status_result = $conn->query($status_sql);
                                $status_row = $status_result->fetch_assoc();
                                $is_reserved = $status_row['reserved_count'] > 0;

                                // แสดงข้อมูลห้องซ้อม
                                echo '<div class="col-md-4">';
                                echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="openReserveModal(\'' . $room['room_type'] . '\')">';
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
                                    <div class="mb-3">
                                        <label for="reserve_name" class="form-label">ชื่อลูกค้า: </label>
                                        <input type="text" class="form-control" name="reserve_name" id="reserve_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_telphone" class="form-label">เบอร์โทร: </label>
                                        <input type="text" class="form-control" name="reserve_telphone" id="reserve_telphone" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_address" class="form-label">ที่อยู่: </label>
                                        <textarea class="form-control" name="reserve_address" id="reserve_address" rows="2" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_type" class="form-label">ประเภท: </label>
                                        <input  class="form-control" type="text" id="modal_reserve_type" name="reserve_type" value="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_price" class="form-label">ราคาห้อง: </label>
                                        <input type="number" class="form-control" name="reserve_price" id="reserve_price" required>
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

                <!-- เปลี่ยนปีเป็นพ.ศ.ด้วยจาวาสคริป -->
                <script>
                    function openReserveModal(roomType) {
                        document.getElementById('modal_reserve_type').value = roomType;
                        var modal = new bootstrap.Modal(document.getElementById('reserveModal'));
                        modal.show();
                    }
                </script>
            </div>
        </div>
    </div>
</section>

<!-- เปลี่ยนปีเป็นพ.ศ.ด้วยจาวาสคริป -->
<script>
function convertToBuddhistYear() {
    var dateInput = document.getElementById("reserve_date");
    var dateValue = new Date(dateInput.value);
    
    if (!isNaN(dateValue.getTime())) {
        // เพิ่ม 543 ปีเพื่อเปลี่ยนเป็นปี พ.ศ.
        var buddhistYear = dateValue.getFullYear() + 543;
        // กำหนดปีที่แปลงแล้วกลับเข้าไปใน input
        var formattedDate = dateValue.toISOString().split('T')[0];
        var parts = formattedDate.split("-");
        parts[0] = buddhistYear; // แทนที่ปี ค.ศ. ด้วยปี พ.ศ.
        dateInput.value = parts.join("-");
    }
    dateInput.form.submit();
}
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
<!-- //3-9-67หน้าจองที่มีสถานะ(ยังไม่กำหนดเวลา) -->


<?php
session_start();
require 'db.php';  // รวมไฟล์การเชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo "กรุณากรอกข้อมูลให้ครบทุกช่อง.";
        exit;
    }

    // เตรียมคำสั่งเลือก
    $sql = "SELECT user_id, username, password, user_type FROM users WHERE username = ? AND password = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $username, $password, $user_type);
                if ($stmt->fetch()) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['user_type'] = $user_type;

                    // ตรวจสอบ user_type และเปลี่ยนเส้นทางตามประเภท
                    if ($user_type == 'admin') {
                        echo "<script>
                                alert('คุณได้เข้าสู่ระบบแล้ว');
                                window.location.href = 'admin/index.php';
                              </script>";
                    } elseif ($user_type == 'user') {
                        echo "<script>
                                alert('คุณได้เข้าสู่ระบบแล้ว');
                                window.location.href = 'index.php';
                              </script>";
                    } else {
                        echo "ประเภทผู้ใช้ไม่ถูกต้อง.";
                    }
                }
            } else {
                echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง.";
            }
        } else {
            echo "อ๊ะ! บางอย่างผิดพลาด. กรุณาลองใหม่อีกครั้งในภายหลัง.";
        }

        $stmt->close();
    }
}

$conn->close();
?>


<!-- 5-9-67หน้าจองที่มีสถานะ(กำหนดเวลาแล้ว) -->

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

            <!-- Room selection -->
            <div class="form-row">
                <div class="col-md-12">
                    <h4>เลือกห้องซ้อม :</h4>
                    <div class="row">
                        <?php
                        // ค้นหาห้องทั้งหมด
                        $sql = "SELECT * FROM room_tb";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($room = $result->fetch_assoc()) {
                                // ตรวจสอบสถานะห้องจากตารางการจอง
                                $room_type = $room['room_type'];
                                $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                               WHERE reserve_type = '$room_type' 
                                               AND reserve_date = '$reserve_date' 
                                               AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                                $status_result = $conn->query($status_sql);
                                $status_row = $status_result->fetch_assoc();
                                $is_reserved = $status_row['reserved_count'] > 0;

                                // แสดงข้อมูลห้องซ้อม
                                echo '<div class="col-md-4">';
                                $cardClass = $is_reserved ? 'card-disabled' : 'card';
                                echo '<div class="' . $cardClass . '" id="card-' . $room['room_type'] . '" onclick="openReserveModal(\'' . $room['room_type'] . '\', ' . ($is_reserved ? 'true' : 'false') . ')">';
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
                                    <div class="mb-3">
                                        <label for="reserve_name" class="form-label">ชื่อลูกค้า: </label>
                                        <input type="text" class="form-control" name="reserve_name" id="reserve_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_telphone" class="form-label">เบอร์โทร: </label>
                                        <input type="text" class="form-control" name="reserve_telphone" id="reserve_telphone" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_address" class="form-label">ที่อยู่: </label>
                                        <textarea class="form-control" name="reserve_address" id="reserve_address" rows="2" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_type" class="form-label">ประเภท: </label>
                                        <input class="form-control" type="text" id="modal_reserve_type" name="reserve_type" value="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="reserve_price" class="form-label">ราคาห้อง: </label>
                                        <input type="number" class="form-control" name="reserve_price" id="reserve_price" required>
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
                    function openReserveModal(roomType, isReserved) {
                        if (isReserved) {
                            alert('ห้องนี้ไม่สามารถจองได้');
                            return;
                        }
                        document.getElementById('modal_reserve_type').value = roomType;
                        var modal = new bootstrap.Modal(document.getElementById('reserveModal'));
                        modal.show();
                    }
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
<!-- //5-9-67หน้าจองที่มีสถานะ(กำหนดเวลาแล้ว) -->



<!-- 10-9-67หน้าte2ที่ยังไม่มีสถานะ(ข้อมูลอยู่ในmodalหมดและคำนวนเรียบร้อยตามอ.ชุต้องการ) -->
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
          <i class="nav-icon fas fa-address-card"></i> แสดงข้อมูลทั้งหมด
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
                                $status_sql = "SELECT COUNT(*) AS reserved_count FROM reserve_tb 
                                                WHERE reserve_type = '$room_type' 
                                                AND reserve_date = '$reserve_date' 
                                                AND (reserve_time1 <= '$reserve_time2' AND reserve_time2 >= '$reserve_time1')";
                                $status_result = $conn->query($status_sql);
                                $status_row = $status_result->fetch_assoc();
                                $is_reserved = $status_row['reserved_count'] > 0;

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
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="reserve_type">ประเภทห้อง :</label>
                                            <input type="text" class="form-control" id="modal_reserve_type" name="reserve_type" readonly required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="reserve_price">ราคาห้อง (บาท/ชม.):</label>
                                            <input type="number" class="form-control" id="reserve_price" name="reserve_price"  step="0.01" readonly required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="reserve_total">ยอดรวมทั้งหมด :</label>
                                            <input type="number" class="form-control" id="reserve_total" name="reserve_total" readonly>
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
                <!-- End of Modal -->

            </div>
        </div>
    </div>
</section>

<script>
// เปิด modal และเติมข้อมูลที่จำเป็น
function openReserveModal(reserveType, roomPrice, isReserved) {
    if (!isReserved) {
        document.getElementById('modal_reserve_type').value = reserveType;
        document.getElementById('reserve_price').value = roomPrice;
        $('#reserveModal').modal('show');
    }
}

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
            // คำนวณจำนวนชั่วโมงที่ต่างกัน
            var diffInMs = time2 - time1;
            var diffInMinutes = diffInMs / (1000 * 60); // แปลงจากมิลลิวินาทีเป็นนาที
            var diffInHours = Math.floor(diffInMinutes / 60); // จำนวนชั่วโมงเต็ม
            var diffInHalfHours = Math.ceil((diffInMinutes % 60) / 30); // จำนวนครึ่งชั่วโมงที่เหลือ

            // คำนวณราคาทั้งหมด
            var totalPrice = (diffInHours * roomPrice) + (diffInHalfHours * 50);
            document.getElementById('reserve_total').value = totalPrice.toFixed(2); // แสดงยอดรวมทั้งหมด
        } else {
            alert('เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด');
            document.getElementById('reserve_total').value = '0.00'; // รีเซ็ตค่าเมื่อเวลาผิด
        }
    } else {
        document.getElementById('reserve_total').value = '0.00'; // รีเซ็ตค่าเมื่อไม่มีเวลา
    }
}

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
<!-- //10-9-67หน้าte2ที่ยังไม่มีสถานะ(ข้อมูลอยู่ในmodalหมดและคำนวนเรียบร้อยตามอ.ชุต้องการ) -->



<!-- 13-9-67หน้าใบเสร็จ(แต่ยังไม่เรียงวันที่) -->
<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบและรับค่า `id` ที่ส่งมา
$reserve_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($reserve_id <= 0) {
    die("Invalid ID.");
}

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT * FROM reserve_tb WHERE reserve_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reserve_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$data) {
    echo "<h1>No data found for reserve ID: " . htmlspecialchars($reserve_id) . "</h1>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ใบเสร็จรับเงิน</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f9f9f9;
        }
        .receipt {
            width: 700px; /* 3 ส่วนของความกว้าง */
            height: 600px; /* 2 ส่วนของความสูง */
            padding: 60px; /* เพิ่ม padding ให้มากขึ้น */
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box; /* รวม padding ในการคำนวณขนาด */
        }
        .receipt h1 {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .receipt .company-info, .receipt .customer-info, .receipt .summary {
            padding: 0 20px; /* เพิ่ม padding ซ้ายและขวา */
            margin-bottom: 15px;
        }
        .receipt .company-info p, .receipt .customer-info p {
            margin: 0;
            line-height: 1.2;
        }
        .receipt .details-table {
            width: auto; /* ปรับขนาดตารางให้เป็นตามข้อมูลจริง */
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-left: 20px; /* ขยับตารางเข้ามาทางซ้าย */
            margin-right: 20px; /* ขยับตารางเข้ามาทางขวา */
        }
        .receipt .details-table th, .receipt .details-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .receipt .details-table th {
            background-color: #f2f2f2;
            text-align: center;
            padding: 10px; /* เพิ่ม padding ให้หัวตาราง */
            font-weight: bold;
        }
        .receipt .details-table td {
            text-align: left;
        }
        .receipt .summary {
            font-size: 15px;
        }
        .receipt .total-price {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            margin-right: 20px; /* ให้ตรงกับการจัดวางของข้อมูลด้านบน */
        }
        .print-button {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            border-radius: 10px;
            cursor: pointer;
        }
        .receipt .customer-info {
            flex-direction: column;
        }
        .receipt .customer-info div {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h1>ใบเสร็จรับเงิน</h1>
        <div class="company-info">
            <div>
                <p><strong>Chromosome 21</strong></p>
                <p>ห้องซ้อมดนตรีโครโมโซม 21 ยินดีต้อนรับ!!<br>
                301 ซอย - ถนนผังเมือง4 ตำบลสะเตง อำเภอเมืองยะลา จังหวัดยะลา<br>
                Tel: 02-354-2345</p>
            </div>
        </div>
        <div class="customer-info">
            <div>
                <p><strong>ลูกค้า</strong><br>
                ชื่อผู้จอง: <?php echo htmlspecialchars($data['reserve_name']); ?><br>
                ที่อยู่: <?php echo htmlspecialchars($data['reserve_address']); ?><br>
                เบอร์: <?php echo htmlspecialchars($data['reserve_telphone']); ?></p>
            </div>
            <div>
                <p><strong>วันที่: <?php echo htmlspecialchars($data['reserve_date']); ?></strong></p>
            </div>
        </div>
        <table class="details-table">
            <tr>
                <th>#</th>
                <th>เวลาจอง (เริ่ม)</th>
                <th>เวลาจอง (ถึง)</th>
                <th>ประเภทห้อง</th>
                <th>ราคาห้อง บาท/ชม.</th>
            </tr>
            <tr>
                <td>1</td>
                <td><?php echo htmlspecialchars($data['reserve_time1']); ?></td>
                <td><?php echo htmlspecialchars($data['reserve_time2']); ?></td>
                <td><?php echo htmlspecialchars($data['reserve_type']); ?></td>
                <td><?php echo htmlspecialchars($data['reserve_price']); ?></td>
            </tr>
        </table>
        <div class="summary">
            <p><strong>หมายเหตุ</strong><br>
            ผู้รับ อักรอม สียะ</p>
        </div>
        <div class="total-price">
            ราคารวม = <?php echo htmlspecialchars($data['reserve_total']); ?> บาท
        </div>
        <div class="print-button" onclick="window.print()">Print</div>
    </div>
</body>
</html>
<!-- //13-9-67หน้าใบเสร็จ(แต่ยังไม่เรียงวันที่) -->


<!-- 18-9-67หน้าจองที่ยังไม่มีสถานะ(ข้อมูลอยู่ในmodalหมดและคำนวนเรียบร้อยตามอ.ชุต้องการ) -->

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
                                echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
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
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="reserve_type">ประเภทห้อง :</label>
                                            <input type="text" class="form-control" id="modal_reserve_type" name="reserve_type" readonly required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="reserve_price">ราคาห้อง (บาท/ชม.):</label>
                                            <input type="number" class="form-control" id="reserve_price" name="reserve_price"  step="0.01" readonly required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="reserve_total">ยอดรวมทั้งหมด :</label>
                                            <input type="number" class="form-control" id="reserve_total" name="reserve_total" readonly>
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
            // คำนวณจำนวนชั่วโมงที่ต่างกัน
            var diffInMs = time2 - time1;
            var diffInMinutes = diffInMs / (1000 * 60); // แปลงจากมิลลิวินาทีเป็นนาที
            var diffInHours = Math.floor(diffInMinutes / 60); // จำนวนชั่วโมงเต็ม
            var diffInHalfHours = Math.ceil((diffInMinutes % 60) / 30); // จำนวนครึ่งชั่วโมงที่เหลือ

            // คำนวณราคาทั้งหมด
            var totalPrice = (diffInHours * roomPrice) + (diffInHalfHours * 50);
            document.getElementById('reserve_total').value = totalPrice.toFixed(2); // แสดงยอดรวมทั้งหมด
        } else {
            alert('เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด');
            document.getElementById('reserve_total').value = '0.00'; // รีเซ็ตค่าเมื่อเวลาผิด
        }
    } else {
        document.getElementById('reserve_total').value = '0.00'; // รีเซ็ตค่าเมื่อไม่มีเวลา
    }
}

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
<!-- //18-9-67หน้าจองที่ยังไม่มีสถานะ(ข้อมูลอยู่ในmodalหมดและคำนวนเรียบร้อยตามอ.ชุต้องการ) -->

<!-- //27-9-67(คำนวนเวลาปัดไปที่ครึ่งชมละ 50) -->
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

/




<!-- 30-9-67(หน้าแสดงต้นฉบับ) -->
<?php
$menu = "show";
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

?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon fas fa-address-card"></i> บันทึกข้อมูลลูกค้า</h1>
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
                                <a class="btn btn-warning btn-xs" href="jong.edit.php?reserve_id=' . $row["reserve_id"] . '">
                                  <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a class="btn btn-danger btn-xs" href="jong.delete.php?delete_id=' . $row["reserve_id"] . '">
                                  <i class="fas fa-trash-alt"></i>
                                </a>
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
<!-- //30-9-67(หน้าแสดงต้นฉบับ) -->



<!-- 10-10-67(หน้าแสดงที่ไม่มีรายละเอียด) -->
<?php
$menu = "show";
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

// อัปเดตสถานะเป็น "ดำเนินการแล้ว" เมื่อกดปุ่มอนุมัติ
if (isset($_GET['approve_id'])) {
    $approve_id = $_GET['approve_id'];
    $sql_update_status = "UPDATE reserve_tb SET status = 'ดำเนินการแล้ว' WHERE reserve_id = $approve_id";
    if ($conn->query($sql_update_status) === TRUE) {
        echo "<script>alert('อนุมัติสำเร็จ!'); window.location.href='show.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// ดึงข้อมูลจากฐานข้อมูลและเรียงลำดับจากการจองใหม่สุด
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_hour, reserve_total, reserve_name, reserve_telphone, status 
        FROM reserve_tb
        ORDER BY reserve_id DESC";  // เรียงจากใหม่ไปเก่า
$result = $conn->query($sql);

?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon fas fa-address-card"></i> บันทึกข้อมูลลูกค้า</h1>
      </div>
      <div class="col-md-6 text-right">
        <a href="jong.php?action=add" class="btn btn-danger">
          <i class="fas fa-laptop-medical"></i>  เพิ่มข้อมูล
        </a>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- เพิ่ม CSS สำหรับแสดงสีตามสถานะ -->
<style>
  .status-pending {
      color: red;
      font-weight: bold;
  }
  .status-approved {
      color: green;
      font-weight: bold;
  }
</style>

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
                <th tabindex="0" rowspan="1" colspan="1" style="width: 9%;">เวลาเริ่ม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 9%;">สิ้นสุด</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ประเภท</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ชั่วโมง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 9%;">ยอดรวม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">ชื่อลูกค้า</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เบอร์โทร</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">สถานะ</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">เครื่องมือ</th>
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
                        echo "<td>" . $row["reserve_hour"] .' ชม.'. "</td>";
                        echo "<td>" . $row["reserve_total"] .' บาท'. "</td>";           
                        echo "<td>" . $row["reserve_name"] . "</td>";
                        echo "<td>" . $row["reserve_telphone"] . "</td>";

                        // ตรวจสอบสถานะและเพิ่มคลาส CSS
                        if ($row["status"] == 'รอดำเนินการ') {
                            echo "<td class='status-pending'>" . $row["status"] . "</td>";
                        } else {
                            echo "<td class='status-approved'>" . $row["status"] . "</td>";
                        }

                        echo '<td>
                                <a class="btn btn-warning btn-xs" href="jong.edit.php?reserve_id=' . $row["reserve_id"] . '">
                                  <i class="fas fa-pencil-alt"></i> 
                                </a>
                                <a class="btn btn-danger btn-xs" href="jong.delete.php?delete_id=' . $row["reserve_id"] . '">
                                  <i class="fas fa-times"></i> 
                                </a>
                                <a class="btn btn-info btn-xs" href="receipt.php?id=' . $row["reserve_id"] . '" target="_blank">
                                  <i class="fas fa-print"></i> 
                                </a>';
                        // เพิ่มปุ่มอนุมัติ
                        if ($row["status"] == 'รอดำเนินการ') {
                            echo ' <a class="btn btn-success btn-xs" href="show.php?approve_id=' . $row["reserve_id"] . '">
                                    <i class="fas fa-check"></i> 
                                  </a>';
                        }
                        echo '</td>';
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
});
</script>
</body>
</html>
<!-- //10-10-67(หน้าแสดงที่ไม่มีรายละเอียด) -->

<!-- //14-10-67(calenda ไม่มีสถานะ) -->
<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
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

// ดึงข้อมูลการจองจากฐานข้อมูล
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_name, reserve_telphone, status 
        FROM reserve_tb";
$result = $conn->query($sql);


$events = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // แปลงปี พ.ศ. เป็น ค.ศ.
        $reserve_date = new DateTime($row['reserve_date']);
        $year_in_christian_era = $reserve_date->format('Y') - 543;
        $reserve_date->setDate($year_in_christian_era, $reserve_date->format('m'), $reserve_date->format('d'));

        // ตั้งค่าสีตามประเภทของห้อง
        $backgroundColor = '#9999FF'; // ค่าเริ่มต้น (สีน้ำเงิน สำหรับห้องล่าง)
        if ($row['reserve_type'] === 'ห้องใหญ่') {
            $backgroundColor = '#32CD32'; // สีเขียว สำหรับห้องใหญ่
        } elseif ($row['reserve_type'] === 'ห้องกลาง') {
            $backgroundColor = '#CC66FF'; // สีม่วง สำหรับห้องกลาง
        }

        // จัดการข้อมูลเป็นรูปแบบที่ FullCalendar ต้องการ
        $events[] = [
            'title' => $row['reserve_name'] . ' - ' . $row['reserve_type'] . ' - ' . $row['reserve_telphone'],
            'start' => $reserve_date->format('Y-m-d') . 'T' . $row['reserve_time1'],
            'end'   => $reserve_date->format('Y-m-d') . 'T' . $row['reserve_time2'],
            'backgroundColor' => $backgroundColor,  // สีตามประเภทของห้อง
            'borderColor' => $backgroundColor, // สีขอบตามประเภทของห้อง
            'extendedProps' => [
                'telphone' => $row['reserve_telphone']  // เพิ่มเบอร์โทรศัพท์เป็น property เสริม
            ]
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Calendar</title>

    <!-- นำเข้า FullCalendar CSS และ JS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <!-- นำเข้า FullCalendar ภาษาไทย -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/th.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',  // (timeGridDay) มุมมองรายวัน, (timeGridWeek) มุมมองรายสัปดาห์
            locale: 'th',  // ตั้งค่าภาษาไทย
            events: <?php echo json_encode($events); ?>,  // ส่งข้อมูล events จาก PHP
            allDaySlot: false,  // ซ่อนช่วงเวลาทั้งวัน
            eventClick: function(info) {
                // เมื่อคลิกที่เหตุการณ์ จะแสดงชื่อและเบอร์โทร
                alert('ชื่อลูกค้า : ' + info.event.title + '\nเบอร์โทร : ' + info.event.extendedProps.telphone);
            },
            headerToolbar: {
                left: 'prev,next today',  // ปุ่มสำหรับเลื่อนวันถัดไปและวันก่อนหน้า
                center: 'title',
                right: 'timeGridDay,timeGridWeek,dayGridMonth'  // มุมมองรายรายวัน รายสัปดาห์ และรายเดือน
            },
            slotMinTime: "10:00:00",  // กำหนดเวลาเริ่มต้นในแต่ละวัน
            slotMaxTime: "20:30:00",  // กำหนดเวลาสิ้นสุดในแต่ละวัน
            slotDuration: "00:30:00"  // กำหนดช่วงเวลาทุกครึ่งชั่วโมง
        });

        calendar.render();
    });
    </script>
</head>
<body>

<!-- แสดงปฏิทิน -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon far fa-calendar-alt"></i> ตารางปฏิทินการจอง</h1>
      </div>
      <div class="col-md-6 text-right">
        <a href="jong.php?action=add" class="btn btn-danger"> <!-- สีเขียวbtn-success สีแดงbtn-danger ฟ้าbtn-primary เทาbtn-secondary-->
          <i class="fas fa-laptop-medical"></i>  จองห้องซ้อมดนตรี
        </a>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<section class="content">
  <div class="container-fluid">
    <div id="calendar"></div> <!-- ตำแหน่งที่จะวางปฏิทิน -->
  </div>
</section>
<?php include('footer.php'); ?>
</body>
</html>
<!-- //14-10-67(calenda ไม่มีสถานะ) -->



<!-- 14-10-67(หน้ารายงานที่ไม่มีการ์ดสรุปรายได้) -->
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
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_price, reserve_hour, reserve_total, reserve_name, reserve_telphone 
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
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เวลาเริ่ม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">สิ้นสุดเวลา.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ประเภท</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคา/ชม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ชั่วโมง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ยอดรวม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ชื่อลูกค้า</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เบอร์โทร</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;"></th>
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
                        echo "<td>" . $row["reserve_total"] . " บาท</td>";           
                        echo "<td>" . $row["reserve_name"] . "</td>";
                        echo "<td>" . $row["reserve_telphone"] . "</td>";
                        echo '<td>
                                  <a class="btn btn-info btn-xs" href="receipt.php?id=' . $row["reserve_id"] . '" target="_blank">
                                    <i class="fas fa-print"></i> ปริ้น
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
<!-- //14-10-67(หน้ารายงานที่ไม่มีการ์ดสรุปรายได้) -->


<?php
$menu = "jong";
include("menu_session.php");  // ดึงข้อมูลผู้ใช้จาก session
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
                        // Display room data
                        $sql = "SELECT * FROM room_tb";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($room = $result->fetch_assoc()) {
                                $room_type = $room['room_type'];
                                $room_price = $room['room_price'];

                                echo '<div class="col-md-4">';
                                echo '<div class="card" id="card-' . $room['room_type'] . '" onclick="openReserveModal(\'' . $room['room_type'] . '\', ' . $room_price . ')">';
                                echo '<img src="uploads/' . $room['room_img'] . '" class="card-img-top" alt="Room Image">';
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
                                            <input type="text" class="form-control" id="reserve_telphone" name="reserve_telphone" required>
                                        </div>
                                    </div>
                            
                                </div>
                                
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">บันทึก</button>
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

// ฟังก์ชันคำนวณชั่วโมงและยอดรวม
function calculateTotal() {
    // ดึงค่าจาก dropdown
    var reserveTime1 = document.getElementById('reserve_time1').value;
    var reserveTime2 = document.getElementById('reserve_time2').value;
    var roomPrice = parseFloat(document.getElementById('reserve_price').value); // ราคาห้องที่อ่านได้จาก input

    if (reserveTime1 && reserveTime2 && !isNaN(roomPrice)) {
        // แปลงเวลาจากสตริงเป็น Date object เพื่อทำการคำนวณ
        var time1 = new Date('1970-01-01T' + reserveTime1 + ':00');
        var time2 = new Date('1970-01-01T' + reserveTime2 + ':00');

        // ตรวจสอบว่าเวลาถึงมากกว่าเวลาที่เริ่มต้น
        if (time2 > time1) {
            // คำนวณจำนวนมิลลิวินาทีที่ต่างกัน
            var diffInMs = time2 - time1;
            var diffInMinutes = diffInMs / (1000 * 60); // แปลงเป็นนาที

            // คำนวณจำนวนชั่วโมงและนาที
            var hours = Math.floor(diffInMinutes / 60);  // จำนวนชั่วโมงเต็ม
            var minutes = diffInMinutes % 60;  // จำนวนนาทีที่เหลือ

            // คำนวณราคาทั้งหมด
            var totalPrice = (hours * roomPrice) + ((minutes > 0) ? (roomPrice / 2) : 0); // ถ้านาทีเกิน 0 ให้คิดครึ่งชั่วโมง
            document.getElementById('reserve_total').value = totalPrice.toFixed(2); // แสดงยอดรวมทั้งหมด

            // แสดงผลเป็นชั่วโมง:นาที
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

// เมื่อเลือกเวลาเริ่มหรือเวลาถึงจะเรียกฟังก์ชัน calculateTotal
document.getElementById('reserve_time1').addEventListener('change', calculateTotal);
document.getElementById('reserve_time2').addEventListener('change', calculateTotal);


</script>
<script>
// เช็คเวลาที่จองแล้วในวันและประเภทห้อง โดยการใช้ JavaScript ร่วมกับ jQuery และ AJAX     
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
                $(this).prop('disabled', false).css('color', 'green'); // ตั้งค่าเวลาที่ว่างเป็นสีเขียว
            });

            // ปิดใช้งานหรือเปลี่ยนสีของ option ที่ตรงกับเวลาที่จองแล้ว
            $('#reserve_time1 option, #reserve_time2 option').each(function() {
                if (reservedTimes.includes(this.value)) {
                    $(this).prop('disabled', true).css('color', 'red'); // เปลี่ยนสีเป็นสีแดงทึบสำหรับเวลาที่ไม่ว่าง
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




<!-- หน้ารายงานสรุป -->
<?php
$conn->close();
?>

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

// Query สำหรับการ์ดสรุปรายงาน
$sql_income = "SELECT SUM(reserve_total) AS total_income FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND MONTH(reserve_date) = MONTH(CURDATE())";
$sql_total_customers = "SELECT COUNT(DISTINCT reserve_name) AS total_customers FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND MONTH(reserve_date) = MONTH(CURDATE())";
$sql_large_rooms = "SELECT COUNT(*) AS total_large_rooms FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND reserve_type = 'ห้องใหญ่' AND MONTH(reserve_date) = MONTH(CURDATE())";
$sql_medium_rooms = "SELECT COUNT(*) AS total_medium_rooms FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND reserve_type = 'ห้องกลาง' AND MONTH(reserve_date) = MONTH(CURDATE())";
$sql_bottom_rooms = "SELECT COUNT(*) AS total_bottom_rooms FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND reserve_type = 'ห้องล่าง' AND MONTH(reserve_date) = MONTH(CURDATE())";

// ดึงข้อมูลสำหรับการ์ดแต่ละใบ
$result_income = $conn->query($sql_income);
$result_customers = $conn->query($sql_total_customers);
$result_large_rooms = $conn->query($sql_large_rooms);
$result_medium_rooms = $conn->query($sql_medium_rooms);
$result_bottom_rooms = $conn->query($sql_bottom_rooms);

// กำหนดค่าเริ่มต้นของตัวแปร
$total_income = 0;
$total_customers = 0;
$total_large_rooms = 0;
$total_medium_rooms = 0;
$total_bottom_rooms = 0;

if ($result_income && $result_income->num_rows > 0) {
    $row = $result_income->fetch_assoc();
    $total_income = $row['total_income'];
}
if ($result_customers && $result_customers->num_rows > 0) {
    $row = $result_customers->fetch_assoc();
    $total_customers = $row['total_customers'];
}
if ($result_large_rooms && $result_large_rooms->num_rows > 0) {
    $row = $result_large_rooms->fetch_assoc();
    $total_large_rooms = $row['total_large_rooms'];
}
if ($result_medium_rooms && $result_medium_rooms->num_rows > 0) {
    $row = $result_medium_rooms->fetch_assoc();
    $total_medium_rooms = $row['total_medium_rooms'];
}
if ($result_bottom_rooms && $result_bottom_rooms->num_rows > 0) {
    $row = $result_bottom_rooms->fetch_assoc();
    $total_bottom_rooms = $row['total_bottom_rooms'];
}

// Query สำหรับตารางข้อมูลการจอง
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_price, reserve_hour, reserve_total, reserve_name, reserve_telphone 
        FROM reserve_tb 
        WHERE status = 'ดำเนินการแล้ว' 
        ORDER BY reserve_id DESC";  // เรียงข้อมูลตามลำดับ ID จากใหม่ไปเก่า
$result = $conn->query($sql);

?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <h1><i class="nav-icon fas fa-file-alt"></i> รายงานยอดประจำเดือน</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
<div class="row">
    <!-- การ์ดรายได้ทั้งหมด -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-info">
        <div class="inner">
          <h3><?php echo number_format($total_income); ?> บาท</h3>
          <p>รายได้ทั้งหมด</p>
        </div>
        <div class="icon">
          <i class="fas fa-money-bill-wave"></i>
        </div>
      </div>
    </div>

    <!-- การ์ดจำนวนลูกค้าทั้งหมด -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-success">
        <div class="inner">
          <h3><?php echo $total_customers; ?></h3>
          <p>จำนวนลูกค้าทั้งหมด</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
      </div>
    </div>

    <!-- การ์ดจำนวนประเภทห้องใหญ่ -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3><?php echo $total_large_rooms; ?></h3>
          <p>จำนวนประเภทห้องใหญ่</p>
        </div>
        <div class="icon">
          <i class="fas fa-door-open"></i>
        </div>
      </div>
    </div>

    <!-- การ์ดจำนวนประเภทห้องกลาง -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-primary">
        <div class="inner">
          <h3><?php echo $total_medium_rooms; ?></h3>
          <p>จำนวนประเภทห้องกลาง</p>
        </div>
        <div class="icon">
          <i class="fas fa-door-closed"></i>
        </div>
      </div>
    </div>

    <!-- การ์ดจำนวนประเภทห้องล่าง -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3><?php echo $total_bottom_rooms; ?></h3>
          <p>จำนวนประเภทห้องล่าง</p>
        </div>
        <div class="icon">
          <i class="fas fa-building"></i>
        </div>
      </div>
    </div>
</div>



  <!-- ตารางข้อมูลการจอง -->
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
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เวลาเริ่ม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">สิ้นสุดเวลา.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ประเภท</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคา/ชม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ชั่วโมง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ยอดรวม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ชื่อลูกค้า</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เบอร์โทร</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;"></th>
              </tr>
            </thead>
            <tbody>
            <?php
              if ($result && $result->num_rows > 0) {
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
                        echo "<td>" . $row["reserve_total"] . " บาท</td>";           
                        echo "<td>" . $row["reserve_name"] . "</td>";
                        echo "<td>" . $row["reserve_telphone"] . "</td>";
                        echo '<td>
                                  <a class="btn btn-info btn-xs" href="receipt.php?id=' . $row["reserve_id"] . '" target="_blank">
                                    <i class="fas fa-print"></i> ปริ้น
                                  </a>
                                </td>';
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='10'>ไม่มีข้อมูลที่สามารถใช้ได้</td></tr>";
                  }
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

<?php $conn->close(); ?>
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

// Query สำหรับการ์ดสรุปรายงาน
$sql_income = "SELECT SUM(reserve_total) AS total_income FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND MONTH(reserve_date) = MONTH(CURDATE())";
$sql_total_customers = "SELECT COUNT(DISTINCT reserve_name) AS total_customers FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND MONTH(reserve_date) = MONTH(CURDATE())";
$sql_large_rooms = "SELECT COUNT(*) AS total_large_rooms FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND reserve_type = 'ห้องใหญ่' AND MONTH(reserve_date) = MONTH(CURDATE())";
$sql_medium_rooms = "SELECT COUNT(*) AS total_medium_rooms FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND reserve_type = 'ห้องกลาง' AND MONTH(reserve_date) = MONTH(CURDATE())";
$sql_bottom_rooms = "SELECT COUNT(*) AS total_bottom_rooms FROM reserve_tb WHERE status = 'ดำเนินการแล้ว' AND reserve_type = 'ห้องล่าง' AND MONTH(reserve_date) = MONTH(CURDATE())";

// ดึงข้อมูลสำหรับการ์ดแต่ละใบ
$result_income = $conn->query($sql_income);
$result_customers = $conn->query($sql_total_customers);
$result_large_rooms = $conn->query($sql_large_rooms);
$result_medium_rooms = $conn->query($sql_medium_rooms);
$result_bottom_rooms = $conn->query($sql_bottom_rooms);

// กำหนดค่าเริ่มต้นของตัวแปร
$total_income = 0;
$total_customers = 0;
$total_large_rooms = 0;
$total_medium_rooms = 0;
$total_bottom_rooms = 0;

if ($result_income && $result_income->num_rows > 0) {
    $row = $result_income->fetch_assoc();
    $total_income = $row['total_income'];
}
if ($result_customers && $result_customers->num_rows > 0) {
    $row = $result_customers->fetch_assoc();
    $total_customers = $row['total_customers'];
}
if ($result_large_rooms && $result_large_rooms->num_rows > 0) {
    $row = $result_large_rooms->fetch_assoc();
    $total_large_rooms = $row['total_large_rooms'];
}
if ($result_medium_rooms && $result_medium_rooms->num_rows > 0) {
    $row = $result_medium_rooms->fetch_assoc();
    $total_medium_rooms = $row['total_medium_rooms'];
}
if ($result_bottom_rooms && $result_bottom_rooms->num_rows > 0) {
    $row = $result_bottom_rooms->fetch_assoc();
    $total_bottom_rooms = $row['total_bottom_rooms'];
}

// Query สำหรับตารางข้อมูลการจอง
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_price, reserve_hour, reserve_total, reserve_name, reserve_telphone 
        FROM reserve_tb 
        WHERE status = 'ดำเนินการแล้ว' 
        ORDER BY reserve_id DESC";  // เรียงข้อมูลตามลำดับ ID จากใหม่ไปเก่า
$result = $conn->query($sql);

?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <h1><i class="nav-icon fas fa-file-alt"></i> สรุปรายงาน</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
<div class="row">
    <!-- การ์ดจำนวนประเภทห้องใหญ่ -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-warning">
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
      <div class="small-box bg-primary">
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
      <div class="small-box bg-danger">
        <div class="inner">
          <h3><?php echo $total_bottom_rooms; ?></h3>
          <p>จำนวนห้องล่าง</p>
        </div>
        <div class="icon">
          <i class="fas fa-building"></i>
        </div>
      </div>
    </div>

    <!-- การ์ดจำนวนลูกค้าทั้งหมด -->
    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-success">
        <div class="inner">
          <h3><?php echo $total_customers; ?></h3>
          <p>จำนวนลูกค้าทั้งหมด</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
      </div>
    </div>
    <!-- การ์ดรายได้ทั้งหมด -->
    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-info">
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
  <div class="card">
    <div class="card-header card-navy card-outline"><br>
    <div class="card-body p-1">
      <div class="row">
        <div class="col-md-12">
          <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
            <thead>
              <tr role="row" class="info">
                <th tabindex="0" rowspan="1" colspan="1" style="width: 1%;">ลำดับ</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">วันที่</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 12%;">เวลาเริ่ม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">สิ้นสุด</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 4%;">ประเภท</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคา</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 2%;">ชั่วโมง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 16%;">ยอดรวม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ชื่อลูกค้า</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 9%;">เบอร์โทร</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;"></th>
              </tr>
            </thead>
            <tbody>
            <?php
              if ($result && $result->num_rows > 0) {
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
                        echo "<td>" . $row["reserve_total"] . " บาท</td>";           
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

<?php $conn->close(); ?>
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
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_price, reserve_hour, reserve_total, reserve_name, reserve_telphone 
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
    <h1><i class="nav-icon fas fa-file-alt"></i> สรุปรายงานการจอง</h1>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
<div class="row mb-4">
    <form method="GET" class="col-12 mb-3">
        <label for="start_date">เริ่มต้น:</label>
        <input type="date" name="start_date" value="<?php echo $start_date ? date('Y-m-d', strtotime($start_date)) : ''; ?>">
        <label for="end_date">สิ้นสุด:</label>
        <input type="date" name="end_date" value="<?php echo $end_date ? date('Y-m-d', strtotime($end_date)) : ''; ?>">
        <button type="submit" class="btn btn-primary">ค้นหา</button>
    </form>
</div>

<div class="row">
    <!-- การ์ดจำนวนประเภทห้องใหญ่ -->
    <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
      <div class="small-box bg-warning">
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
      <div class="small-box bg-primary">
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
      <div class="small-box bg-danger">
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
      <div class="small-box bg-dark">
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
      <div class="small-box bg-info">
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
<div class="card">
  <div class="card-header card-navy card-outline"><br>
  <div class="card-body p-1">
    <div class="row">
      <div class="col-md-12">
        <table id="example1" class="table table-bordered table-striped dataTable" role="grid">
          <thead>
          <tr role="row" class="info">
                <th tabindex="0" rowspan="1" colspan="1" style="width: 1%;">ลำดับ</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">วันที่จอง</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เวลาเริ่ม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">สิ้นสุดเวลา.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ประเภท</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคา/ชม.</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ชั่วโมง</th>
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
                    echo "<td>{$row['reserve_total']} บาท</td>";
                    echo "<td>{$row['reserve_name']}</td>";
                    echo "<td>{$row['reserve_telphone']}</td>";
                    
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>ไม่มีข้อมูลที่สามารถใช้ได้</td></tr>";
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


<!-- หน้าพนักงาน ข้อมูลแสดงกับเพิ่มอยู่รวมกัน-->

<?php
$menu = "employee";
include("menu_session.php");  // ดึงข้อมูลผู้ใช้จาก session
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


// ดึงข้อมูลจากฐานข้อมูล
$sql = "
    SELECT 
        user_tb.user_id, 
        user_tb.user_name, 
        user_tb.user_email, 
        user_tb.user_telphone, 
        user_tb.username, 
        user_tb.password, 
        user_type_tb.user_type_name 
    FROM 
        user_tb
    LEFT JOIN 
        user_type_tb 
    ON 
        user_tb.user_type = user_type_tb.user_type_id";
$result = $conn->query($sql);

?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="nav-icon fas fa-user-friends"></i> จัดการข้อมูลพนักงาน</h1>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="card">
        <div class="card-header card-navy card-outline">
            <form id="employeeForm" action="employee.edit.php" method="post" class="needs-validation" novalidate
                onsubmit="return validateForm()">
                <div class="form-row">
                    <input type="hidden" name="user_id">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">ชื่อ-สกุล :</label>
                        <input type="text" class="form-control" name="user_name" required>
                        <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">อีเมล์ :</label>
                        <input type="text" class="form-control" name="user_email" required>
                        <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_telphone">เบอร์โทร :</label>
                        <input type="text" class="form-control" id="user_telphone" name="user_telphone" maxlength="10"
                            required>
                        <div class="invalid-feedback">**กรุณากรอกเบอร์โทรให้ครบ 10 ตัวเลข</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">ชื่อผู้ใช้ :</label>
                        <input type="text" class="form-control" name="username" required>
                        <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">รหัสผ่าน :</label>
                        <input type="text" class="form-control" name="password" required>
                        <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">ประเภทผู้ใช้ :</label>
                        <div>
                        <?php
$querys = "SELECT `user_type_id`, `user_type_name` FROM `user_type_tb`";
$results = mysqli_query($conn, $querys);

if ($results) {
    while ($rows = mysqli_fetch_assoc($results)) {
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="user_type" id="userType' . $rows['user_type_id'] . '" value="' . $rows['user_type_id'] . '" required>';
        echo '<label class="form-check-label" for="userType' . $rows['user_type_id'] . '">' . $rows['user_type_name'] . '</label>';
        echo '</div>';
    }
}
?>

                            
                        </div>
                        <div class="invalid-feedback">**กรุณาเลือกประเภทผู้ใช้</div>
                    </div>


                </div>
                <button class="btn btn-danger" type="submit" onclick="return validatePhone()">บันทึก</button>
                <button class="btn btn-secondary" type="button" onclick="resetForm()">ยกเลิก</button>
            </form>
            <br>
            <div class="card-body p-1">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-12">
                        <table id="example1" class="table table-bordered table-striped dataTable" role="grid"
                            aria-describedby="example1_info">
                            <thead>
                                <tr role="row" class="info">
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ลำดับ</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ชื่อพนักงาน</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">อีเมล์พนักงาน</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">เบอร์พนักงาน</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ชื่อผู้ใช้</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ประเภทผู้ใช้</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">รหัสผ่าน</th>
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
                    echo "<td>" . $row["user_name"] . "</td>";
                    echo "<td>" . $row["user_email"] . "</td>";
                    echo "<td>" . $row["user_telphone"] . "</td>";
                    echo "<td>" . $row["username"] . "</td>";
                    echo "<td>" . $row["user_type_name"] . "</td>";
                    echo "<td>" . $row["password"] . "</td>";
                    echo '<td>
                            <a class="btn btn-warning btn-xs" href="employee.php?edit=' . $row["user_id"] . '">
                              <i class="fas fa-pencil-alt"></i> แก้ไข
                            </a>
                            <a class="btn btn-danger btn-xs" href="employee.delete.php?delete=' . $row["user_id"] . '" onclick="return confirm(\'คุณแน่ใจที่จะลบใช่ไหม?\')">
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
<!-- /.content -->

<?php include('footer.php'); ?>

<script>
$(function() {
    $(".datatable").DataTable();
});

function resetForm() {
    document.querySelector("form").reset();
    document.getElementsByName("submit")[0].name = "submit";
}

// ฟังก์ชันกรอกข้อมูลให้ครบทุกช่อง
function validateForm() {
    const userName = document.querySelector('[name="user_name"]').value.trim();
    const userEmail = document.querySelector('[name="user_email"]').value.trim();
    const userPhone = document.querySelector('[name="user_telphone"]').value.trim();
    const username = document.querySelector('[name="username"]').value.trim();
    const password = document.querySelector('[name="password"]').value.trim();
    const userType = document.querySelector('[name="user_type"]:checked');

    if (!userName || !userEmail || !userPhone || !username || !password || !userType) {
        alert('กรุณากรอกข้อมูลให้ครบทุกช่อง');
        return false;
    }
    return validatePhone(); // ตรวจสอบเบอร์โทรด้วย
}

// ฟังก์ชันกำหนดตัวเลขเบอร์โทรให้มีความยาวเท่ากับ 10 ตัวอักษร
function validatePhone() {
    const phoneInput = document.getElementById('user_telphone'); // ดึง input เบอร์โทร
    const phoneValue = phoneInput.value.replace(/\D/g, ''); // เอาเฉพาะตัวเลขออกมา

    // ตรวจสอบว่าเบอร์โทรมีความยาวครบ 10 ตัวอักษรหรือไม่
    if (phoneValue.length !== 10) {
        phoneInput.classList.add('is-invalid'); // แสดงข้อความแจ้งเตือนว่ากรอกไม่ครบ 10 ตัว
        alert('กรุณากรอกเบอร์โทรให้ครบ 10 ตัวเลข');
        return false; // หยุดการส่งข้อมูล
    }

    phoneInput.classList.remove('is-invalid'); // ลบการแจ้งเตือนถ้าครบ 10 ตัวแล้ว
    return true; // อนุญาตให้ส่งข้อมูล
}

// จำกัดให้สามารถกรอกเฉพาะตัวเลขและไม่เกิน 10 ตัว
document.getElementById('user_telphone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // ลบตัวอักษรที่ไม่ใช่ตัวเลขออก

    // จำกัดความยาวไม่เกิน 10 ตัว
    if (value.length > 10) {
        value = value.slice(0, 10); // ถ้าเกิน 10 ตัวให้ตัดออก
    }

    e.target.value = value; // ตั้งค่าใหม่ให้ input
});

// JavaScript สำหรับเติมข้อมูลการแก้ไข
if (window.location.search.indexOf('edit=') !== -1) {
    var urlParams = new URLSearchParams(window.location.search);
    var user_id = urlParams.get('edit');

    fetch("employee.edit.php?edit=" + user_id)
    .then(response => response.json())
    .then(data => {
        if (!data.error) {
            document.getElementsByName("user_id")[0].value = data.user_id;
            document.getElementsByName("user_name")[0].value = data.user_name;
            document.getElementsByName("user_email")[0].value = data.user_email;
            document.getElementsByName("user_telphone")[0].value = data.user_telphone;
            document.getElementsByName("username")[0].value = data.username;
            document.getElementsByName("password")[0].value = data.password;

            // ตั้งค่าให้ radio ถูกเลือกตาม user_type ที่ดึงมาได้
            const userTypeInput = document.querySelector(`input[name="user_type"][value="${data.user_type}"]`);
            if (userTypeInput) {
                userTypeInput.checked = true;
            }

            document.getElementsByName("submit")[0].name = "update";
            document.getElementById("employeeForm").action = "employee.edit.php";
        } else {
            alert(data.error);
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });

}
</script>

</body>

</html>

<?php
$conn->close();
?>
<!-- หน้าแก้ไขพนักงาน ข้อมูลแสดงกับเพิ่มอยู่รวมกัน-->
<?php
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
    // ดึงข้อมูลจากฟอร์ม
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_telphone = $_POST['user_telphone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';

    // ตรวจสอบว่า username ซ้ำหรือไม่
    $sql_check = "SELECT * FROM user_tb WHERE username = '$username' AND user_id != '$user_id'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // username ซ้ำ
        echo "<script>
            alert('ชื่อผู้ใช้นี้มีอยู่ในระบบแล้ว กรุณาใช้ชื่อผู้ใช้อื่น');
            window.history.back(); // ย้อนกลับไปหน้าเดิม
        </script>";
    } else {
        // ไม่มี username ซ้ำ ทำการเพิ่มหรือแก้ไขข้อมูล
        if (empty($user_id)) {
            // เพิ่มข้อมูลใหม่
            $sql = "INSERT INTO user_tb (user_name, user_email, user_telphone, username, password, user_type) 
                    VALUES ('$user_name', '$user_email', '$user_telphone', '$username', '$password', '$user_type')";
        } else {
            // แก้ไขข้อมูล
            $sql = "UPDATE user_tb 
                    SET user_name = '$user_name', user_email = '$user_email', user_telphone = '$user_telphone', 
                        username = '$username', password = '$password', user_type = '$user_type' 
                    WHERE user_id = '$user_id'";
        }

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                alert('บันทึกข้อมูลเรียบร้อยแล้ว');
                window.location.href = 'employee.php';
            </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// ดึงข้อมูลจากฐานข้อมูลสำหรับการแก้ไข
if (isset($_GET['edit'])) {
    $user_id = $_GET['edit'];
    $sql = "SELECT user_id, user_name, user_email, user_telphone, username, password, user_type FROM user_tb WHERE user_id='$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "ไม่พบข้อมูล"]);
    }
}


$conn->close();
?>
<!-- หน้าพนักงาน ข้อมูลแสดงกับเพิ่มอยู่รวมกัน-->

<!-- หน้าห้องซ้อม ข้อมูลแสดงกับเพิ่มอยู่รวมกัน-->
<?php
$menu = "room";
include("menu_session.php");  // ดึงข้อมูลผู้ใช้จาก session
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

    // อัปโหลดไฟล์รูปภาพ
    if ($room_img && move_uploaded_file($_FILES["room_img"]["tmp_name"], $target_file)) {
        if ($room_id) {
            // การอัพเดทข้อมูล
            $sql = "UPDATE room_tb SET room_type='$room_type', room_price='$room_price', room_capacity='$room_capacity', room_detail='$room_detail', room_img='$room_img' WHERE room_id='$room_id'";
        } else {
            // การเพิ่มข้อมูล
            $sql = "INSERT INTO room_tb (room_type, room_price, room_capacity, room_detail, room_img) VALUES ('$room_type', '$room_price','$room_capacity', '$room_detail', '$room_img')";
        }

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                alert('บันทึกข้อมูลสำเร็จ');
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

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('room_id').value = '$edit_id';
                document.getElementsByName('room_type')[0].value = '".htmlspecialchars($row['room_type'], ENT_QUOTES)."';
                document.getElementsByName('room_price')[0].value = '".htmlspecialchars($row['room_price'], ENT_QUOTES)."';
                document.getElementsByName('room_capacity')[0].value = '".htmlspecialchars($row['room_capacity'], ENT_QUOTES)."';
                document.getElementsByName('room_detail')[0].value = '".htmlspecialchars($row['room_detail'], ENT_QUOTES)."';
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
      <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate onsubmit="return validateForm()">  
        <div class="form-row">
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
            <input type="number" class="form-control" name="room_capacity" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="validationCustom01">รูปภาพห้อง :</label>
            <input type="file" class="form-control" name="room_img">
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
          <div class="col-md-12">
            <table id="example1" class="table table-bordered table-striped">
              <thead>
              <tr role="row" class="info">
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 8%;">ลำดับ</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 12%;">ประเภท</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 12%;">ราคา</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 12%;">ความจุ</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">รูปภาพห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 25%;">รายละเอียดห้อง</th>
                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">แก้ไข/ลบ</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    $number = 0;
                    while($row = $result->fetch_assoc()) {
                        $number++;
                        echo "<tr>";
                        echo "<td>$number</td>";
                        echo "<td>" . htmlspecialchars($row["room_type"], ENT_QUOTES) . "</td>";
                        echo "<td>" . htmlspecialchars($row["room_price"], ENT_QUOTES) . "</td>";
                        echo "<td>" . htmlspecialchars($row["room_capacity"], ENT_QUOTES) . "</td>";
                        echo "<td><img src='uploads/" . htmlspecialchars($row["room_img"], ENT_QUOTES) . "' width='100'></td>";
                        echo "<td>" . htmlspecialchars($row["room_detail"], ENT_QUOTES) . "</td>";
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
// ฟังก์ชันกรอกข้อมูลให้ครบทุกช่อง
function validateForm() {
    // ตรวจสอบว่าฟิลด์แต่ละช่องถูกกรอกครบถ้วนหรือไม่
    const roomType = document.getElementsByName('room_type')[0].value.trim();
    const roomPrice = document.getElementsByName('room_price')[0].value.trim();
    const roomCapacity = document.getElementsByName('room_capacity')[0].value.trim();
    const roomDetail = document.getElementsByName('room_detail')[0].value.trim();
    
    // ตรวจสอบว่าทุกฟิลด์มีข้อมูล
    if (!roomType || !roomPrice || !roomCapacity || !roomDetail) {
        alert('กรุณากรอกข้อมูลให้ครบทุกช่อง');
        return false; // หยุดการส่งข้อมูลถ้ายังกรอกไม่ครบ
    }

    return true; // ส่งข้อมูลเมื่อกรอกครบทุกฟิลด์
}


function resetForm() {
    document.getElementById('room_id').value = '';
    document.getElementsByName('room_type')[0].value = '';
    document.getElementsByName('room_price')[0].value = '';
    document.getElementsByName('room_capacity')[0].value = '';
    document.getElementsByName('room_detail')[0].value = '';
    document.getElementsByName('room_img')[0].value = '';
}
</script>

<?php include('footer.php'); ?>

<script>
$(document).ready(function() {
  $('#example1').DataTable();
});
</script>

</body>
</html>
<!-- หน้าห้องซ้อม ข้อมูลแสดงกับเพิ่มอยู่รวมกัน-->



<!-- หน้าแก้ไขการจอง แต่ราคาไม่ดึงมา-->
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
    $sql = "SELECT room_price FROM room_tb WHERE room_type = '$reserve_type'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['room_price']; // คืนค่าราคาห้อง
    }
    return 0; // กรณีไม่พบข้อมูล
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
                <option value="0" <?php echo ($row['reserve_more'] == '0') ? 'selected' : ''; ?>>0 ชม</option>
                <option value="0.5" <?php echo ($row['reserve_more'] == '0.5') ? 'selected' : ''; ?>>0:30 นาที</option>
                <option value="1" <?php echo ($row['reserve_more'] == '1') ? 'selected' : ''; ?>>1 ชม</option>
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
                    // อัปเดตฟิลด์เวลาหรือราคาตามข้อมูลที่ได้จากฐานข้อมูล
                    $('#reserve_price').val(response.room_price);  // เปลี่ยนราคาห้องตามข้อมูลที่ได้
                    $('#reserve_time1').html(response.time_options);  // เปลี่ยนตัวเลือกเวลาเริ่ม
                    $('#reserve_time2').html(response.time_options);  // เปลี่ยนตัวเลือกเวลาถึง
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

                <?php
// เชื่อมต่อฐานข้อมูลjong.edit.get.times
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "project_room";

$conn = new mysqli($servername, $username, $password, $dbname); 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); 
}

// ตรวจสอบว่ามีการส่งค่า reserve_type และ reserve_date หรือไม่
if (isset($_POST['reserve_type']) && isset($_POST['reserve_date'])) {
    $reserve_type = $_POST['reserve_type']; // รับค่าประเภทห้องที่ส่งมาจากฟอร์มหรือ AJAX
    $reserve_date = $_POST['reserve_date']; // รับค่าวันที่ที่ส่งมาจากฟอร์มหรือ AJAX

    // ดึงเวลาที่ถูกจองแล้วตามประเภทห้องและวันที่
    $sql = "SELECT reserve_time1, reserve_time2 FROM reserve_tb 
            WHERE reserve_type = '$reserve_type' 
            AND reserve_date = '$reserve_date'"; // สร้างคำสั่ง SQL เพื่อค้นหาช่วงเวลาที่จองแล้วตามประเภทห้องและวันที่
    $result = $conn->query($sql); // รันคำสั่ง SQL และเก็บผลลัพธ์ในตัวแปร $result

    $reserved_times = []; // สร้าง array ว่างสำหรับเก็บช่วงเวลาที่ถูกจองแล้ว

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reserved_times[] = [
                'start' => substr($row['reserve_time1'], 0, 5), // ดึงเฉพาะส่วนของเวลาในรูปแบบ HH:MM
                'end' => substr($row['reserve_time2'], 0, 5) // ดึงเฉพาะส่วนของเวลาในรูปแบบ HH:MM
            ]; // เพิ่มช่วงเวลาที่จองแล้วลงใน array
        }
    }

    // สร้างตัวเลือกเวลาสำหรับเวลาเริ่มและเวลาถึง
    $time_options_start = '<option value="">--:--</option>';  // ค่าเริ่มต้น สำหรับเวลาเริ่ม
    $time_options_end = '<option value="">--:--</option>';    // ค่าเริ่มต้น สำหรับเวลาถึง

    for ($hour = 10; $hour <= 20; $hour++) { // วนลูปตั้งแต่ 10:00 น. ถึง 20:00 น.
        for ($minute = 0; $minute < 60; $minute += 30) { // วนลูปทุก 30 นาที (00 และ 30 นาที)
            $time = sprintf('%02d:%02d', $hour, $minute); // จัดรูปแบบเวลาให้เป็น HH:MM เช่น 10:00, 10:30
            $is_reserved = false; // ตั้งค่าเริ่มต้นว่าช่วงเวลานี้ยังไม่ถูกจอง

            // ตรวจสอบว่าเวลานั้นถูกจองหรือยัง
            foreach ($reserved_times as $reserved) {
                // ถ้าเวลาเริ่มอยู่ในช่วงเวลาที่ถูกจองแล้ว
                if ($time >= $reserved['start'] && $time < $reserved['end']) {
                    $is_reserved = true; // ตั้งค่าว่าเวลานี้ถูกจองแล้ว
                    break; // หยุดการตรวจสอบเพิ่มเติม
                }
            }

            // กำหนดสีสำหรับเวลาว่างหรือเวลาที่จองแล้ว
            $color = $is_reserved ? 'red' : 'green'; // ใช้สีแดงถ้าถูกจองแล้ว และสีเขียวถ้าว่าง

            // ตัวเลือกสำหรับเวลาเริ่ม
            $time_options_start .= "<option value='$time' style='color: $color;'>$time</option>";

            // ตัวเลือกสำหรับเวลาถึง (เพื่อให้เลือกเวลาถึงที่ตามหลังเวลาที่เลือกเริ่ม)
            $time_options_end .= "<option value='$time' style='color: $color;'>$time</option>";
        }
    }

    // ส่งข้อมูลกลับไปในรูปแบบ JSON
    echo json_encode([
        'time_options_start' => $time_options_start, // ส่ง HTML ของตัวเลือกเวลาเริ่ม
        'time_options_end' => $time_options_end // ส่ง HTML ของตัวเลือกเวลาถึง
    ]);
}

$conn->close(); 
?>

<?php
// เชื่อมต่อฐานข้อมูลjong.edit.get.room
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "project_room"; 

$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); 
}

// ตรวจสอบว่ามีการส่งค่า reserve_type หรือไม่
if (isset($_POST['reserve_type'])) {
    $reserve_type = $_POST['reserve_type']; // รับค่าประเภทห้องที่ส่งมาจากฟอร์มหรือ AJAX

    // ดึงราคาห้องตามประเภทห้อง
    $sql = "SELECT room_price FROM room_tb WHERE room_type = '$reserve_type'"; // สร้างคำสั่ง SQL เพื่อดึงราคาห้องตามประเภทห้อง
    $result = $conn->query($sql); // รันคำสั่ง SQL และเก็บผลลัพธ์ในตัวแปร $result

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // ดึงแถวข้อมูลจากผลลัพธ์ของการค้นหา
        $room_price = $row['room_price']; // ดึงราคาห้องจากแถวข้อมูล

        // สร้างตัวเลือกเวลาสำหรับเวลาเริ่มและเวลาถึง
        $time_options = ''; // สร้างตัวแปรว่างเพื่อเก็บ HTML ของตัวเลือกเวลา
        for ($hour = 10; $hour <= 20; $hour++) { // วนลูปตั้งแต่ 10:00 น. ถึง 20:00 น.
            for ($minute = 0; $minute < 60; $minute += 30) { // วนลูปทุก 30 นาที (00 และ 30 นาที)
                $time = sprintf('%02d:%02d', $hour, $minute); // จัดรูปแบบเวลาให้เป็น HH:MM เช่น 10:00, 10:30
                $time_options .= "<option value='$time'>$time</option>"; // เพิ่มตัวเลือกเวลาในรูปแบบ HTML
            }
        }

        // ส่งข้อมูลเป็น JSON กลับไปยัง AJAX
        echo json_encode([
            'room_price' => $room_price, // ส่งราคาห้องกลับไป
            'time_options' => $time_options // ส่ง HTML ของตัวเลือกเวลา
        ]);
    }
}
$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
