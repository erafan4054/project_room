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