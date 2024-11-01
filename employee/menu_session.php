<?php
session_start(); // ต้องมีการเรียกใช้ session ก่อนการทำงานใดๆ

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $user_name = $_SESSION['user_name'];
    $user_type = $_SESSION['user_type_name'];
} else {
    $user_name = 'Guest';
    $user_type = '';  // ถ้ายังไม่ได้ล็อกอิน ไม่แสดง user_type
}
?>
