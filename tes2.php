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


</script>

</body>

</html>

<?php
$conn->close();
?>