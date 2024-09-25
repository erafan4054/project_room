<!-- Main Sidebar Container -->
<!-- http://fordev22.com/ -->
<aside class="main-sidebar sidebar-light-navy elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link bg-red">
      <img src="../assets/dist/img/logo.png"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8; width: 30px; height: 30px;">
      <span class="brand-text font-weight-light"> Chromosome21</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../assets/dist/img/avatar2.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <?php
          // ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                $user_name = $_SESSION['user_name'];
                $user_type = $_SESSION['user_type'];  // ดึง user_type จาก session
            } else {
                $user_name = 'Guest';
                $user_type = '';  // ถ้ายังไม่ได้ล็อกอิน ไม่แสดง user_type
            }
          ?>

        <a href="" target="_blank" class="d-block">
            <?php echo htmlspecialchars($user_name) . ' (' . htmlspecialchars($user_type) . ')'; ?> <!-- แสดงชื่อผู้ใช้พร้อมประเภทผู้ใช้ -->
        </a>     
      </div>
    </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <!-- nav-compact -->
        <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-header"></li>

          <li class="nav-item">
            <a href="index.php" class="nav-link <?php if($menu=="index"){echo "active";} ?>">
              <i class="nav-icon fas fa-home"></i>
              <p>หน้าหลัก</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="doc.php" class="nav-link <?php if($menu=="doc"){echo "active";} ?>">
              <i class="nav-icon fas fa-laptop-medical"></i>
              <p>จัดการข้อมูลจอง
                <i class="right fa fa-chevron-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="jong.php" class="nav-link <?php if($submenu=="booking"){echo "active";} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>รายการจอง</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="show.php" class="nav-link <?php if($submenu=="display"){echo "active";} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>รายการบันทึก</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="report.php" class="nav-link <?php if($menu=="report"){echo "active";} ?>">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>รายงาน</p>
            </a>
          </li>

          <div class="user-panel mt-2 pb-3 mb-2 d-flex"></div>
          <li class="nav-item">
            <a href="logout.php" class="nav-link text-danger">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>ออกจากระบบ</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
      <!-- http://fordev22.com/ -->
    </div>
    <!-- /.sidebar -->
  </aside>
