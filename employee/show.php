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
$sql = "SELECT reserve_id, reserve_date, reserve_time1, reserve_time2, reserve_type, reserve_hour, reserve_more, reserve_total, reserve_name, reserve_telphone, reserve_price, status 
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
          <i class="fas fa-laptop-medical"></i>  เพิ่มข้อมูลจอง
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

  /* Custom CSS Modal รายละเอียดการจอง */
  .modal-header {
      background-color: #ffffff; /* เปลี่ยนเป็นสีขาว */
      color: black;
      border-bottom: 1px solid #000000; /* เพิ่มเส้นขอบด้านล่างสีดำ */
      font-weight: bold; /* ทำให้ตัวอักษรหนา */
  }
  
  .modal-title {
    font-size: 1.5rem;
    font-weight: bold;
  }

  .modal-content {
      border-radius: 8px;
      padding: 20px;
  }

  .modal-body p {
      font-size: 16px;
      margin: 10px 0;
      font-weight: bold; /* ทำให้ตัวอักษรใน modal-body หนา */
  }

  .modal-footer {
      display: flex;
      justify-content: center; /* จัดปุ่มให้อยู่ตรงกลาง */
  }

  .modal-lg {
      max-width: 600px;
  }

  .btn-xs {
      font-size: 14px;
      padding: 8px 12px;
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
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">เวลาจอง</th>               
                <th tabindex="0" rowspan="1" colspan="1" style="width: 4%;">ประเภท</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ชั่วโมงเพิ่ม</th> <!-- เพิ่มคอลัมน์ใหม่ -->
                <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ยอดรวม</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">ชื่อลูกค้า</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 11%;">สถานะ</th>
                <th tabindex="0" rowspan="1" colspan="1" style="width: 20%;">เครื่องมือ</th>
              </tr>
            </thead> 
            <tbody>
            <?php
              if ($result) {
                if ($result->num_rows > 0) {
                    $number = 0;
                    while ($row = $result->fetch_assoc()) {
                        $number++;
                        $date = new DateTime($row["reserve_date"]);
                        $formattedDate = $date->format('d-m-Y');
                        
                        echo "<tr>";
                        echo "<td>" . $number . "</td>";
                        echo "<td>" . $formattedDate . "</td>";
                        echo "<td>" . $row["reserve_time1"] . "</td>";                       
                        echo "<td>" . $row["reserve_type"] . "</td>";                       
                        echo "<td>";
                              if (!empty($row['reserve_more']) && $row['reserve_more'] != '0') {
                                  // ตรวจสอบค่าของ reserve_more และแสดงผลตามที่กำหนด
                                  switch ($row['reserve_more']) {
                                      case '0.5':
                                          echo '30 นาที';
                                          break;
                                      case '1':
                                          echo '1 ชม.';
                                          break;
                                      case '1.5':
                                          echo '1:30 นาที';
                                          break;
                                      default:
                                          echo $row['reserve_more'] . ' ชม.';
                                          break;
                                  }
                              } else {
                                  echo 'ไม่มี';
                              }
                        echo "</td>";
                        echo "<td>" . $row["reserve_total"] .' บาท'. "</td>";          
                        echo "<td>" . $row["reserve_name"] . "</td>";

                        if ($row["status"] == 'รอดำเนินการ') {
                            echo "<td class='status-pending'>" . $row["status"] . "</td>";
                        } else {
                            echo "<td class='status-approved'>" . $row["status"] . "</td>";
                        }

                        echo '<td>
                                <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#detailModal' . $row["reserve_id"] . '">
                                  <i class="fas fa-eye"></i> รายละเอียด
                                </button>
                                <a class="btn btn-info btn-xs" href="receipt.php?id=' . $row["reserve_id"] . '" target="_blank">
                                  <i class="fas fa-print"></i> ปริ้น
                                </a>
                              </td>';
                              
                        // <!-- Modal รายละเอียดการจอง -->
                        echo '
                        <div class="modal fade" id="detailModal' . $row["reserve_id"] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> 
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="detailModalLabel"><i class="fas fa-info-circle"></i> รายละเอียดการจอง</h5> 
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <i class="nav-icon fas fa-times"></i></button>
                              </div>
                              <div class="modal-body">
                                <div class="row">
                                  <div class="col-md-6">
                                    <p><strong>วันที่จอง :</strong> ' . $formattedDate . '</p>
                                  </div>
                                  <div class="col-md-6">
                                    <p><strong>เวลาเริ่ม :</strong> ' . $row["reserve_time1"] . ' นาที</p>
                                  </div>
                                </div>  
                                  <div class="row">                                 
                                  <div class="col-md-6">
                                    <p><strong>เวลาสิ้นสุด :</strong> ' . $row["reserve_time2"] . ' นาที</p>
                                  </div>
                                  <div class="col-md-6">
                                    <p><strong>จำนวนชั่วโมง :</strong> ' . $row["reserve_hour"] . ' ชม.</p>
                                  </div>
                                </div>
                                  <div class="row">
                                  <div class="col-md-6">
                                    <p><strong>ประเภทห้อง :</strong> ' . $row["reserve_type"] . '</p>
                                  </div>
                                    <div class="col-md-6">
                                    <p><strong>ราคาห้อง/ชม. :</strong> ' . $row["reserve_price"] . ' บาท</p>
                                  </div>
                                </div>
                                  <div class="row">
                                  <div class="col-md-6">
                                    <p><strong>ชื่อลูกค้า :</strong> ' . $row["reserve_name"] . '</p>
                                  </div>
                                  <div class="col-md-6">
                                    <p><strong>เบอร์โทร :</strong> ' . $row["reserve_telphone"] . '</p>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-6">
                                    <p><strong>สถานะ :</strong> 
                                      <span class="' . ($row["status"] == 'รอดำเนินการ' ? 'status-pending' : 'status-approved') . '">' . $row["status"] . '</span>
                                    </p>
                                  </div>
                                  <div class="col-md-6">
                                    <p><strong>ยอดรวมทั้งหมด :</strong> ' . $row["reserve_total"] . ' บาท</p>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                  <a class="btn btn-warning btn-xs" href="jong.edit.php?reserve_id=' . $row["reserve_id"] . '">
                                    <i class="fas fa-pencil-alt"></i> แก้ไขข้อมูลลูกค้า
                                  </a>
                                  <a class="btn btn-danger btn-xs" href="jong.delete.php?delete_id=' . $row["reserve_id"] . '">
                                    <i class="fas fa-times"></i> ยกเลิกการจอง
                                  </a>';
                                  
                              echo ' <a class="btn btn-success btn-xs" href="show.php?approve_id=' . $row["reserve_id"] . '">
                                      <i class="fas fa-check"></i> อนุมัติการจอง
                                    </a>';
                              echo '</div>';
                              echo '</div>';
                              echo '</div>';
                              echo '</div>';
                        echo '</tr>';
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

<?php include('footer.php'); ?>

<script>
$(function () {
  $(".datatable").DataTable();
});
</script>

</body>
</html>
