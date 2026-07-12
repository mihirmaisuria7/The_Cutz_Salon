<?php
require_once __DIR__ . '/supabase_db.php';

$con = true;

function db_query($sql) {
    return supabase_query($sql);
}

function db_fetch_array($result) {
    return supabase_fetch_array($result);
}

function db_num_rows($result) {
    return supabase_num_rows($result);
}

function db_real_escape_string($string) {
    return supabase_escape($string);
}

function db_insert_id() {
    return supabase_insert_id();
}

