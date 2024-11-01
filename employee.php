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
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon fas fa-user-friends"></i> จัดการข้อมูลพนักงาน</h1>
      </div>
      <div class="col-md-6 text-right">
        <a href="employee.insert.php" class="btn btn-danger">
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
                        <table id="example1" class="table table-bordered table-striped dataTable" role="grid"
                            aria-describedby="example1_info">
                            <thead>
                            <tr role="row" class="info">
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ลำดับ</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ชื่อพนักงาน</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 18%;">อีเมล์พนักงาน</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">เบอร์พนักงาน</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ชื่อผู้ใช้</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ประเภทผู้ใช้</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">รหัสผ่าน</th>
                                    <th tabindex="0" rowspan="1" colspan="1" style="width: 13%;">แก้ไข/ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if ($result->num_rows > 0) {
                                      $number = 0;
                                      while($row = $result->fetch_assoc()) {
                                        $number++;
                                        echo "<tr>";
                                        echo "<td>" . $number . "</td>";
                                        echo "<td>" . $row["user_name"] . "</td>";
                                        echo "<td>" . $row["user_email"] . "</td>";
                                        echo "<td>" . $row["user_telphone"] . "</td>";
                                        echo "<td>" . $row["username"] . "</td>";
                                        echo "<td>" . $row["user_type_name"] . "</td>";
                                        echo "<td>" . $row["password"] . "</td>";
                                        echo '<td>
                                                <a class="btn btn-warning btn-xs" href="employee.edit.php?edit=' . $row["user_id"] . '">
                                                  <i class="fas fa-pencil-alt"></i> แก้ไข
                                                </a>
                                                <a class="btn btn-danger btn-xs" href="employee.delete.php?delete=' . $row["user_id"] . '" onclick="return confirm(\'คุณแน่ใจที่จะลบใช่ไหม?\')">
                                                  <i class="fas fa-trash-alt"></i> ลบ
                                                </a>
                                              </td>';
                                        echo "</tr>";
                                      }
                                    } else {
                                      echo "<tr><td colspan='8'>ยังไม่มีข้อมูลที่สามารถใช้ได้</td></tr>";
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