<?php
require_once __DIR__ . '/supabase_config.php';

if (SUPABASE_DB_PASSWORD !== '') {
    // Supabase PostgreSQL (requires pdo_pgsql enabled; mysqli extension should be off)
    require_once __DIR__ . '/mysqli_compat.php';
    $con = mysqli_connect(
        SUPABASE_DB_HOST,
        SUPABASE_DB_USER,
        SUPABASE_DB_PASSWORD,
        SUPABASE_DB_NAME,
        SUPABASE_DB_PORT
    );
} else {
    // Local WAMP MySQL fallback until Supabase DB password is configured
    mysqli_report(MYSQLI_REPORT_OFF);
    $con = mysqli_init();
    mysqli_options($con, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    @mysqli_real_connect($con, '127.0.0.1', 'root', '', 'msmsdb', 3306);
}

if (mysqli_connect_errno()) {
    echo 'Connection Fail: ' . mysqli_connect_error();
}
