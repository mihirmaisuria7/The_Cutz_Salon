<?php
session_start();
unset($_SESSION['bpmsstid']);
unset($_SESSION['bpmsstname']);
session_destroy();
header('location:../index.php');
exit;
