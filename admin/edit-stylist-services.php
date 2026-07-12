<?php
session_start();
error_reporting(0);
include('includes/supabase_db.php');
include_once('includes/auth_check.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';

$stylistId = intval($_GET['id'] ?? 0);
$sty = db_fetch_array(db_query( "SELECT * FROM tblstylists WHERE ID='$stylistId'"));
if (!$sty) {
    echo "<script>alert('Stylist not found.'); window.location='manage-stylists.php';</script>";
    exit;
}

if (isset($_POST['save_services'])) {
    if (msms_has_table($con, 'tblstylist_services')) {
        db_query( "DELETE FROM tblstylist_services WHERE StylistId='$stylistId'");
        if (!empty($_POST['service_ids']) && is_array($_POST['service_ids'])) {
            foreach ($_POST['service_ids'] as $svcId) {
                $svcId = intval($svcId);
                if ($svcId > 0) {
                    db_query( "INSERT IGNORE INTO tblstylist_services(StylistId, ServiceId) VALUES('$stylistId','$svcId')");
                }
            }
        }
        echo "<script>alert('Service expertise updated.'); window.location='edit-stylist-services.php?id=$stylistId';</script>";
    } else {
        echo "<script>alert('Run SQL File/stylist_booking_update.sql first.');</script>";
    }
}

$selected = [];
if (msms_has_table($con, 'tblstylist_services')) {
    $selQ = db_query( "SELECT ServiceId FROM tblstylist_services WHERE StylistId='$stylistId'");
    while ($s = db_fetch_array($selQ)) {
        $selected[] = intval($s['ServiceId']);
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>MSMS | Stylist Services</title>
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href="css/font-awesome.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">
</head>
<body class="cbp-spmenu-push">
<div class="main-content">
<?php include_once('includes/sidebar.php');?>
<?php include_once('includes/header.php');?>
<div id="page-wrapper">
<div class="main-page">
<div class="forms">
<h3 class="title1">Services for <?php echo htmlspecialchars($sty['StylistName']); ?></h3>
<p>Select which services this stylist is expert in (shown to clients when booking).</p>
<div class="form-grids row widget-shadow">
<div class="form-body">
<form method="post">
<?php
$services = db_query( "SELECT ID, ServiceName, Cost FROM tblservices ORDER BY ServiceName");
while ($srv = db_fetch_array($services)) {
    $chk = in_array(intval($srv['ID']), $selected, true) ? 'checked' : '';
    echo '<div class="checkbox"><label><input type="checkbox" name="service_ids[]" value="'.intval($srv['ID']).'" '.$chk.'> '
        . htmlspecialchars($srv['ServiceName']) . ' - Rs. ' . intval($srv['Cost']) . '</label></div>';
}
?>
<br>
<button type="submit" name="save_services" class="btn btn-default">Save</button>
<a href="manage-stylists.php" class="btn btn-default">Back</a>
</form>
</div>
</div>
</div>
</div>
</div>
<?php include_once('includes/footer.php');?>
</div>
<script src="js/bootstrap.js"></script>
</body>
</html>


