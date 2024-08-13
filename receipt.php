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
    <title>ใบเสร็จ</title>
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
            width: 600px;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .receipt h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .receipt .company-info {
            text-align: left; /* ตั้งค่าชิดซ้าย */
            margin-bottom: 20px;
        }
        .receipt .company-info p {
            margin: 0;
            line-height: 1.6;
        }
        .receipt .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .receipt .details-table th, .receipt .details-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .receipt .details-table th {
            background-color: #f2f2f2;
        }
        .receipt .total-price {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
        }
        .print-button {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h1>ใบเสร็จ</h1>
        <div class="company-info">
            <p><strong>Chromosome 21 Yala</strong></p>
            <p>301 ซอย - ถนนผังเมือง 4 ตำบลสะเตง อำเภอเมืองยะลา จังหวัดยะลา</p>
            <p>Tel: 02-354-2345</p>
        </div>
        <p><strong>วันที่ทำรายการจอง : <?php echo htmlspecialchars($data['reserve_date']); ?></strong></p>
        <table class="details-table">
            <tr>
                <th>ชื่อลูกค้า :</th>
                <td><?php echo htmlspecialchars($data['reserve_name']); ?></td>
                <th>เบอร์โทรศัพท์ :</th>
                <td><?php echo htmlspecialchars($data['reserve_telphone']); ?></td>
            </tr>
            <tr>
                <th>ที่อยู่ลูกค้า :</th>
                <td><?php echo htmlspecialchars($data['reserve_address']); ?></td>
                <th>ประเภทห้อง :</th>
                <td><?php echo htmlspecialchars($data['reserve_type']); ?></td>
            </tr>
            <tr>
                <th>เวลาจอง/เริ่ม :</th>
                <td><?php echo htmlspecialchars($data['reserve_time1']); ?></td>
                <th>เวลาจอง/ถึง :</th>
                <td><?php echo htmlspecialchars($data['reserve_time2']); ?></td>
            </tr>
        </table>
        <div class="total-price">
            ราคา = <?php echo htmlspecialchars($data['reserve_price']); ?> บาท
        </div>
        <div class="print-button" onclick="window.print()">Print</div>
    </div>
</body>
</html>
