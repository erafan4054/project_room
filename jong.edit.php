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

// ตรวจสอบว่ามีการส่ง ID มาเพื่อต้องการแก้ไขข้อมูล
if (isset($_GET['reserve_id'])) {
    $reserve_id = $_GET['reserve_id'];

    // ดึงข้อมูลจากฐานข้อมูลตาม ID ที่ระบุ
    $sql = "SELECT * FROM reserve_tb WHERE reserve_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reserve_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "ไม่พบข้อมูลที่ต้องการแก้ไข";
        exit();
    }
} else {
    echo "ไม่พบ ID สำหรับการแก้ไข";
    exit();
}

// รับค่าจากฟอร์มหลังจากส่งข้อมูลเพื่อแก้ไข
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserve_date = isset($_POST['reserve_date']) ? $_POST['reserve_date'] : '';
    $reserve_time1 = isset($_POST['reserve_time1']) ? $_POST['reserve_time1'] : '';
    $reserve_time2 = isset($_POST['reserve_time2']) ? $_POST['reserve_time2'] : '';
    $reserve_name = isset($_POST['reserve_name']) ? $_POST['reserve_name'] : '';
    $reserve_telphone = isset($_POST['reserve_telphone']) ? $_POST['reserve_telphone'] : '';
    $reserve_address = isset($_POST['reserve_address']) ? $_POST['reserve_address'] : '';
    $reserve_type = isset($_POST['reserve_type']) ? $_POST['reserve_type'] : '';
    $reserve_price = isset($_POST['reserve_price']) ? $_POST['reserve_price'] : '';

    // สร้างคำสั่ง SQL สำหรับการอัปเดตข้อมูล
    $sql = "UPDATE reserve_tb SET reserve_date = ?, reserve_time1 = ?, reserve_time2 = ?, reserve_name = ?, reserve_telphone = ?, reserve_address = ?, reserve_type = ?, reserve_price = ? WHERE reserve_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $reserve_date, $reserve_time1, $reserve_time2, $reserve_name, $reserve_telphone, $reserve_address, $reserve_type, $reserve_price, $reserve_id);

    if ($stmt->execute()) {
        echo "<script>
                        alert('แก้ไขข้อมูลสำเร็จแล้ว');
                        window.location.href = 'show.php';
                      </script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $stmt->close();
    $conn->close();
    exit();
}
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="nav-icon fas fa-edit"></i> แก้ไขข้อมูลจอง</h1>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
        <div class="card-header card-navy card-outline"><br>
            <form action="jong.edit.php?reserve_id=<?php echo $reserve_id; ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="reserve_id" value="<?php echo $reserve_id; ?>">

                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom03">วันที่ :</label>
                        <input type="date" class="form-control" name="reserve_date" value="<?php echo htmlspecialchars($row['reserve_date']); ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">เวลาจอง (เริ่ม) :</label>
                        <input type="time" class="form-control" name="reserve_time1" value="<?php echo htmlspecialchars($row['reserve_time1']); ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom03">เวลาจอง (ถึง) :</label>
                        <input type="time" class="form-control" name="reserve_time2" value="<?php echo htmlspecialchars($row['reserve_time2']); ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="reserve_name">ชื่อลูกค้า:</label>
                        <input type="text" class="form-control" name="reserve_name" value="<?php echo htmlspecialchars($row['reserve_name']); ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="reserve_telphone">เบอร์โทร:</label>
                        <input type="text" class="form-control" name="reserve_telphone" value="<?php echo htmlspecialchars($row['reserve_telphone']); ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="reserve_address">ที่อยู่:</label>
                        <textarea class="form-control" name="reserve_address" rows="3" required><?php echo htmlspecialchars($row['reserve_address']); ?></textarea>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="reserve_type">ประเภท:</label>
                        <input type="text" class="form-control" name="reserve_type" value="<?php echo htmlspecialchars($row['reserve_type']); ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="reserve_price">ราคา:</label>
                        <input type="number" class="form-control" name="reserve_price" value="<?php echo htmlspecialchars($row['reserve_price']); ?>" required>
                        <div class="invalid-feedback">
                            **กรุณากรอกข้อมูล
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-secondary" type="button" onclick="window.history.back()">ยกเลิก</button>
                        <button class="btn btn-primary" type="submit">บันทึก</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- /.content -->
<?php include('footer.php'); ?>

</body>

</html>

<?php
$conn->close();
?>
