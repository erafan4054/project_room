<?php
$menu = "employee.edit";
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
            window.history.back();
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
                alert('บันทึกข้อมูลใหม่เรียบร้อยแล้ว');
                window.location.href = 'employee.php';
            </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// ดึงข้อมูลจากฐานข้อมูลสำหรับการแก้ไข
$edit_data = [];
if (isset($_GET['edit'])) {
    $user_id = $_GET['edit'];
    $sql = "SELECT user_id, user_name, user_email, user_telphone, username, password, user_type FROM user_tb WHERE user_id='$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
    }
}
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <h1><i class="nav-icon fas fa-edit"></i> แก้ไขข้อมูลพนักงาน</h1>
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
                    <input type="hidden" name="user_id" value="<?php echo isset($edit_data['user_id']) ? $edit_data['user_id'] : ''; ?>">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">ชื่อ-สกุล :</label>
                        <input type="text" class="form-control" name="user_name" value="<?php echo isset($edit_data['user_name']) ? $edit_data['user_name'] : ''; ?>" required>
                        <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">อีเมล์ :</label>
                        <input type="text" class="form-control" name="user_email" value="<?php echo isset($edit_data['user_email']) ? $edit_data['user_email'] : ''; ?>" required>
                        <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_telphone">เบอร์โทร :</label>
                        <input type="text" class="form-control" id="user_telphone" name="user_telphone" value="<?php echo isset($edit_data['user_telphone']) ? $edit_data['user_telphone'] : ''; ?>" maxlength="10" required>
                        <div class="invalid-feedback">**กรุณากรอกเบอร์โทรให้ครบ 10 ตัวเลข</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">ชื่อผู้ใช้ :</label>
                        <input type="text" class="form-control" name="username" value="<?php echo isset($edit_data['username']) ? $edit_data['username'] : ''; ?>" required>
                        <div class="invalid-feedback">**กรุณากรอกข้อมูล</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">รหัสผ่าน :</label>
                        <input type="text" class="form-control" name="password" value="<?php echo isset($edit_data['password']) ? $edit_data['password'] : ''; ?>" required>
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
                                $checked = (isset($edit_data['user_type']) && $edit_data['user_type'] == $rows['user_type_id']) ? 'checked' : '';
                                echo '<div class="form-check form-check-inline">';
                                echo '<input class="form-check-input" type="radio" name="user_type" id="userType' . $rows['user_type_id'] . '" value="' . $rows['user_type_id'] . '" ' . $checked . ' required>';
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
                <button class="btn btn-danger" type="submit" onclick="return validatePhone()">บันทึกการแก้ไข</button>
                <a href="employee.php" class="btn btn-secondary">กลับไปหน้ารายการ</a>
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
