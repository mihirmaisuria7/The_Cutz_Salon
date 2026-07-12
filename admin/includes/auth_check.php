<?php
if (empty($_SESSION['bpmsaid'])) {
    header('Location: logout.php');
    exit;
}
