<?php
/**
 * Shared appointment / stylist helpers (admin, client, stylist panels).
 */

function msms_stylist_name($con, $stylistId)
{
    if (empty($stylistId)) {
        return '—';
    }
    $id = intval($stylistId);
    $r = mysqli_fetch_array(mysqli_query($con, "SELECT StylistName FROM tblstylists WHERE ID='$id'"));
    return $r ? $r['StylistName'] : '—';
}

function msms_has_table($con, $tableName)
{
    $t = mysqli_real_escape_string($con, $tableName);
    $q = mysqli_query($con, "SHOW TABLES LIKE '$t'");
    return ($q && mysqli_num_rows($q) > 0);
}

function msms_has_column($con, $table, $column)
{
    $table = mysqli_real_escape_string($con, $table);
    $column = mysqli_real_escape_string($con, $column);
    $q = mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return ($q && mysqli_num_rows($q) > 0);
}

function msms_stylists_for_service($con, $serviceName)
{
    $serviceName = mysqli_real_escape_string($con, $serviceName);
    if (msms_has_table($con, 'tblstylist_services')) {
        $sql = "SELECT DISTINCT s.ID, s.StylistName, s.Specialty, s.MobileNumber, s.Email
            FROM tblstylists s
            INNER JOIN tblstylist_services ss ON ss.StylistId = s.ID
            INNER JOIN tblservices srv ON srv.ID = ss.ServiceId
            WHERE srv.ServiceName = '$serviceName'
            ORDER BY s.StylistName";
    } else {
        $sql = "SELECT ID, StylistName, Specialty, MobileNumber, Email FROM tblstylists ORDER BY StylistName";
    }
    $rows = [];
    $ret = mysqli_query($con, $sql);
    if ($ret) {
        while ($row = mysqli_fetch_array($ret)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function msms_services_for_stylist($con, $stylistId)
{
    $sid = intval($stylistId);
    $names = [];
    if (msms_has_table($con, 'tblstylist_services')) {
        $ret = mysqli_query($con, "SELECT srv.ServiceName, srv.Cost
            FROM tblstylist_services ss
            INNER JOIN tblservices srv ON srv.ID = ss.ServiceId
            WHERE ss.StylistId='$sid'
            ORDER BY srv.ServiceName");
        while ($row = mysqli_fetch_array($ret)) {
            $names[] = $row;
        }
    }
    return $names;
}

function msms_apt_is_rejected($row)
{
    if (($row['Status'] ?? '') === '2') {
        return true;
    }
    if (!empty($row['StylistId']) && ($row['StylistStatus'] ?? '') === '2') {
        return true;
    }
    return false;
}

function msms_apt_is_confirmed($row)
{
    if (msms_apt_is_rejected($row)) {
        return false;
    }
    if (($row['Status'] ?? '') !== '1') {
        return false;
    }
    if (empty($row['StylistId'])) {
        return true;
    }
    return ($row['StylistStatus'] ?? '') === '1';
}

function msms_apt_admin_status_text($status)
{
    if ($status === '1') {
        return 'Accepted';
    }
    if ($status === '2') {
        return 'Rejected';
    }
    return 'Pending';
}

function msms_apt_stylist_status_text($stylistStatus)
{
    if ($stylistStatus === '1') {
        return 'Accepted';
    }
    if ($stylistStatus === '2') {
        return 'Rejected';
    }
    return 'Pending';
}

function msms_apt_overall_status_text($row)
{
    if (msms_apt_is_rejected($row)) {
        return 'Rejected';
    }
    if (msms_apt_is_confirmed($row)) {
        return 'Confirmed';
    }
    return 'Pending approval';
}

function msms_apt_overall_badge_html($row)
{
    if (msms_apt_is_rejected($row)) {
        return '<span class="badge badge-rejected">Rejected</span>';
    }
    if (msms_apt_is_confirmed($row)) {
        return '<span class="badge badge-accepted">Confirmed</span>';
    }
    return '<span class="badge badge-pending">Pending</span>';
}

function msms_apt_admin_badge_html($status)
{
    if ($status === '1') {
        return '<span class="badge badge-accepted">Accepted</span>';
    }
    if ($status === '2') {
        return '<span class="badge badge-rejected">Rejected</span>';
    }
    return '<span class="badge badge-pending">Pending</span>';
}

function msms_apt_stylist_badge_html($stylistStatus)
{
    if ($stylistStatus === '1') {
        return '<span class="badge badge-accepted">Accepted</span>';
    }
    if ($stylistStatus === '2') {
        return '<span class="badge badge-rejected">Rejected</span>';
    }
    return '<span class="badge badge-pending">Pending</span>';
}

function msms_stylist_can_respond($row, $stylistId)
{
    if (intval($row['StylistId'] ?? 0) !== intval($stylistId)) {
        return false;
    }
    if (($row['Status'] ?? '') === '2') {
        return false;
    }
    $ss = $row['StylistStatus'] ?? '';
    return ($ss === '' || $ss === null);
}

function msms_admin_can_respond($row)
{
    $st = $row['Status'] ?? '';
    return ($st === '' || $st === null);
}
