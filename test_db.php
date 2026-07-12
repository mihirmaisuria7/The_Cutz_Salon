<?php
mysqli_report(MYSQLI_REPORT_OFF);
$c = mysqli_init();
mysqli_options($c, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
$ok = @mysqli_real_connect($c, '127.0.0.1', 'root', '', 'msmsdb', 3306);
echo $ok ? "MySQL OK\n" : ('FAIL: ' . mysqli_connect_error() . "\n");
