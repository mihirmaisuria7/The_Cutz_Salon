<?php
session_start();
error_reporting(0);
include(__DIR__ . '/../../includes/supabase_db.php');
if (strlen($_SESSION['bpmsstid'] ?? '') == 0) {
    header('location:../index.php');
    exit;
}

