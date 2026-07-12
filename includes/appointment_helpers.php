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
    $r = db_fetch_array(db_query("SELECT * FROM tblstylists WHERE ID='$id' LIMIT 1"));
    return $r ? ($r['StylistName'] ?? '—') : '—';
}

function msms_has_table($con, $tableName)
{
    // Supabase REST does not expose information_schema; hardcode known tables
    $knownTables = [
        'tbladmin', 'tblappointment', 'tblcustomers', 'tblinvoice',
        'tblpage', 'tblservices', 'tblstylists', 'tblstylist_services',
        'tblsubscribers'
    ];
    return in_array(strtolower($tableName), $knownTables);
}

function msms_has_column($con, $table, $column)
{
    // Supabase REST does not expose information_schema; hardcode known columns
    $schema = [
        'tblappointment' => ['id','aptnumber','name','email','phonenumber','aptdate','apttime','services','applydate','remark','status','remarkdate','stylistid','stylistremark','styliststatus'],
        'tblcustomers' => ['id','name','email','mobilenumber','gender','details','creationdate','updationdate','username','password'],
        'tblstylists' => ['id','stylistname','specialty','mobilenumber','email','username','password','creationdate'],
        'tblservices' => ['id','servicename','description','cost','creationdate','updationdate'],
    ];
    $tbl = strtolower($table);
    $col = strtolower($column);
    if (isset($schema[$tbl])) {
        return in_array($col, $schema[$tbl]);
    }
    return false;
}

function msms_stylists_for_service($con, $serviceName)
{
    $serviceName = db_real_escape_string($serviceName);
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
    $ret = db_query($sql);
    if ($ret) {
        while ($row = db_fetch_array($ret)) {
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
        $ret = db_query("SELECT srv.ServiceName, srv.Cost
            FROM tblstylist_services ss
            INNER JOIN tblservices srv ON srv.ID = ss.ServiceId
            WHERE ss.StylistId='$sid'
            ORDER BY srv.ServiceName");
        while ($row = db_fetch_array($ret)) {
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
