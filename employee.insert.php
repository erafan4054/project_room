<?php
$menu = "employee.insert";
include("menu_session.php");
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

// การเพิ่มข้อมูลลงฐานข้อมูลเมื่อกดบันทึก
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_telphone = $_POST['user_telphone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // ตรวจสอบว่ามี username อยู่แล้วหรือไม่
    $check_sql = "SELECT * FROM user_tb WHERE username = '$username'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "<script>alert('ชื่อผู้ใช้นี้มีอยู่ในระบบแล้ว กรุณาใช้ชื่อผู้ใช้อื่น');window.history.back();</script>";
    } else {
        // ถ้าไม่มีข้อมูลซ้ำ ให้ทำการ INSERT
        $sql = "INSERT INTO user_tb (user_name, user_email, user_telphone, username, password, user_type) 
                VALUES ('$user_name', '$user_email', '$user_telphone', '$username', '$password', '$user_type')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('เพิ่มข้อมูลเรียบร้อยแล้ว');window.location='employee.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// ดึงข้อมูลประเภทผู้ใช้ (ส่วนนี้ต้องแน่ใจว่าอยู่หลังการเชื่อมต่อฐานข้อมูลและการเพิ่มข้อมูล)
$querys = "SELECT `user_type_id`, `user_type_name` FROM `user_type_tb`";
$results = $conn->query($querys);

?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h1><i class="nav-icon fas fa-laptop-medical"></i> เพิ่มข้อมูลพนักงาน</h1>
      </div>
      <div class="col-md-6 text-right">
        <a href="employee.php?action=add" class="btn btn-danger">
          <i class="nav-icon fas fa-user-friends"></i> รายการบันทึกพนักงาน
        </a>
      </div>
    </div>
  </div>
</section>


<!-- ฟอร์มเพิ่มข้อมูลพนักงาน -->
<section class="content">
    <div class="card">
        <div class="card-header card-navy card-outline">
            <form id="employeeForm" action="" method="post" class="needs-validation" novalidate onsubmit="return validateForm()">
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
                        <input type="text" class="form-control" id="user_telphone" name="user_telphone" maxlength="10" required>
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
                        // ดึงข้อมูลประเภทผู้ใช้จากฐานข้อมูล
                        $querys = "SELECT `user_type_id`, `user_type_name` FROM `user_type_tb`";
                        $results = $conn->query($querys);

                        if ($results->num_rows > 0) {
                            while ($rows = $results->fetch_assoc()) {
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
                <div class="col-md-6 mb-3">
                </div>
                <button class="btn btn-danger" type="submit" onclick="return validatePhone()">บันทึก</button>
                <button class="btn btn-secondary" type="button" onclick="resetForm()">ยกเลิก</button>
            </form>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>

<script>
function resetForm() {
    document.querySelector("form").reset();
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
    const phoneInput = document.getElementById('user_telphone'); 
    const phoneValue = phoneInput.value.replace(/\D/g, ''); 

    if (phoneValue.length !== 10) {
        phoneInput.classList.add('is-invalid'); 
        alert('กรุณากรอกเบอร์โทรให้ครบ 10 ตัวเลข');
        return false; 
    }

    phoneInput.classList.remove('is-invalid'); 
    return true; 
}

document.getElementById('user_telphone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); 

    if (value.length > 10) {
        value = value.slice(0, 10); 
    }

    e.target.value = value; 
});
</script>

<?php
$conn->close();
?>
