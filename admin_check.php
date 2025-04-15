<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}
?>