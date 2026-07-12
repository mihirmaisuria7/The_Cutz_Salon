<?php
session_start();
error_reporting(0);
include(__DIR__ . '/../../includes/dbconnection.php');
if (strlen($_SESSION['bpmsuid'] ?? '') == 0) {
    header('location:../index.php');
    exit;
}
