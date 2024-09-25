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
            width: 800px; /* 3 ส่วนของความกว้าง */
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
            padding: 0 10px; /* เพิ่ม padding ซ้ายและขวา */
            margin-bottom: 15px;
        }
        .receipt .company-info p, .receipt .customer-info p {
            margin: 0;
            line-height: 1.2;
        }
        .receipt .details-table {
            width: 100%; /* ทำให้ตารางเต็มความกว้างของกรอบ */
            max-width: 850px; /* จำกัดขนาดตารางสูงสุด */
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-left: 10px; /* ขยับตารางเข้ามาทางซ้าย */
            margin-right: 10px; /* ขยับตารางเข้ามาจากขอบขวามากขึ้น */
        }

        .receipt .details-table th, .receipt .details-table td {
            padding: 10px; /* เพิ่มพื้นที่ในแต่ละเซลล์ของตาราง */
            border: 1px solid #ddd;
            text-align: left;
        }
        .receipt .details-table th {
            background-color: #f2f2f2;
            text-align: center;
            padding: 10px; /* เพิ่ม padding ให้หัวตาราง */
            font-weight: bold;
            margin-right: 20px; /* ให้ตรงกับการจัดวางของข้อมูลด้านบน */
        }
        .receipt .details-table td {
            text-align: left;
        }
        .receipt .summary {
            font-size: 15px;
            margin-bottom: 10px; /* ลดระยะห่างระหว่างตารางและหมายเหตุ */
        }

        .receipt .total-price {
            text-align: right; /* ทำให้ข้อความชิดขวา */
            font-size: 20px; /* ปรับขนาดตัวอักษร */
            font-weight: bold; /* ทำให้ตัวอักษรหนา */
            margin-right: 0px; /* ขยับข้อความเข้ามาทางซ้าย เพื่อไม่ให้ชิดขอบเกินไป */
            margin-top: 20px; /* เพิ่มระยะห่างจากตาราง */
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
                เบอร์: <?php echo htmlspecialchars($data['reserve_telphone']); ?></p>
            </div>
            <div>
                <p><strong>วันที่: <?php echo htmlspecialchars($data['reserve_date']); ?></strong></p>
            </div>
        </div>
        <table class="details-table">
            <tr>
                <th>#</th>
                <th>เวลา (เริ่ม)</th>
                <th>เวลา (ถึง)</th>
                <th>ประเภทห้อง</th>
                <th>ราคาห้อง /ชม.</th>
                <th>ชั่วโมง</th>
            </tr>
            <tr>
                <td>1</td>
                <td><?php echo htmlspecialchars($data['reserve_time1']); ?></td>
                <td><?php echo htmlspecialchars($data['reserve_time2']); ?></td>
                <td><?php echo htmlspecialchars($data['reserve_type']); ?></td>
                <td><?php echo htmlspecialchars($data['reserve_price']); ?></td>
                <td><?php echo htmlspecialchars($data['reserve_hour']); ?></td>
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
