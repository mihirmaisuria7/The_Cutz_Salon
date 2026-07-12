<?php
session_start();
unset($_SESSION['bpmsuid']);
unset($_SESSION['bpmsuname']);
session_destroy();
header('location:../index.php');
exit;
