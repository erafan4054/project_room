<?php
$menu = "employee";
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
$sql = "SELECT employee_id, employee_name, employee_email, employee_telphone, employee_username, employee_password FROM employee_tb";
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
      <form id="employeeForm" action="employee.edit.php" method="post" class="needs-validation" novalidate>
        <div class="form-row">
          <input type="hidden" name="employee_id">
          <div class="col-md-4 mb-3">
            <label for="validationCustom01">ชื่อ-สกุล :</label>
            <input type="text" class="form-control" name="employee_name" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-4 mb-3">
            <label for="validationCustom01">อีเมล์ :</label>
            <input type="text" class="form-control" name="employee_email" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-4 mb-3">
            <label for="validationCustom01">เบอร์โทร :</label>
            <input type="text" class="form-control" name="employee_telphone" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
        </div>
        <div class="form-row">  
          <div class="col-md-6 mb-3">  
            <label for="validationCustom01">ชื่อผู้ใช้ :</label>
            <input type="text" class="form-control" name="employee_username" required>
            <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="validationCustom01">รหัสผ่าน :</label>
            <input type="text" class="form-control" name="employee_password" required>
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
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ลำดับ</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ชื่อพนักงาน</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">อีเมล์พนักงาน</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">เบอร์พนักงาน</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ชื่อผู้ใช้</th>
                  <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">รหัสผ่าน</th>
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
                    echo "<td>" . $row["employee_name"] . "</td>";
                    echo "<td>" . $row["employee_email"] . "</td>";
                    echo "<td>" . $row["employee_telphone"] . "</td>";
                    echo "<td>" . $row["employee_username"] . "</td>";
                    echo "<td>" . $row["employee_password"] . "</td>";
                    echo '<td>
                            <a class="btn btn-warning btn-xs" href="employee.php?edit=' . $row["employee_id"] . '">
                              <i class="fas fa-pencil-alt"></i> แก้ไข
                            </a>
                            <a class="btn btn-danger btn-xs" href="employee.delete.php?delete=' . $row["employee_id"] . '" onclick="return confirm(\'คุณแน่ใจที่จะลบใช่ไหม?\')">
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
$(function () {
  $(".datatable").DataTable();
});

function resetForm() {
    document.querySelector("form").reset();
    document.getElementsByName("submit")[0].name = "submit";
}

// JavaScript สำหรับเติมข้อมูลการแก้ไข
if (window.location.search.indexOf('edit=') !== -1) {
    var urlParams = new URLSearchParams(window.location.search);
    var employee_id = urlParams.get('edit');
    
    fetch("employee.edit.php?edit=" + employee_id)
        .then(response => response.json())
        .then(data => {
            document.getElementsByName("employee_id")[0].value = employee_id;
            document.getElementsByName("employee_name")[0].value = data.employee_name;
            document.getElementsByName("employee_email")[0].value = data.employee_email;
            document.getElementsByName("employee_telphone")[0].value = data.employee_telphone;
            document.getElementsByName("employee_username")[0].value = data.employee_username;
            document.getElementsByName("employee_password")[0].value = data.employee_password;
            document.getElementsByName("submit")[0].name = "update";
            document.getElementById("employeeForm").action = "employee.edit.php";
        });
}
</script>

</body>
</html>

<?php
$conn->close();
?>
