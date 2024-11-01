<?php
session_start(); // ต้องมีการเรียกใช้ session ก่อนการทำงานใดๆ เพื่อให้สามารถใช้ตัวแปร session ได้

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // ถ้าผู้ใช้ล็อกอินแล้ว
    $user_name = $_SESSION['user_name']; // ดึงชื่อล็อกอินของผู้ใช้จาก session และเก็บในตัวแปร $user_name
    $user_type = $_SESSION['user_type_name']; // ดึงประเภทของผู้ใช้จาก session และเก็บในตัวแปร $user_type
} else {
    // ถ้าผู้ใช้ยังไม่ได้ล็อกอิน
    $user_name = 'Guest'; // กำหนดชื่อผู้ใช้เป็น 'Guest'
    $user_type = '';  // ไม่กำหนดประเภทของผู้ใช้ (ให้เป็นค่าว่าง)
}
?>
