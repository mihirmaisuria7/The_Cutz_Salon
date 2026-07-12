<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
include_once('includes/auth_check.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
if (isset($_POST['submit'])) {
    $name = db_real_escape_string( $_POST['name']);
    $username = db_real_escape_string( $_POST['username']);
    $email = db_real_escape_string( $_POST['email']);
    $mobilenum = db_real_escape_string( $_POST['mobilenum']);
    $specialty = db_real_escape_string( $_POST['specialty']);
    $password = md5($_POST['password']);
    $query = db_query( "INSERT INTO tblstylists(StylistName,UserName,Email,MobileNumber,Specialty,Password) VALUES('$name','$username','$email','$mobilenum','$specialty','$password')");
    if ($query) {
        $newId = db_insert_id();
        if ($newId && msms_has_table($con, 'tblstylist_services') && !empty($_POST['service_ids'])) {
            foreach ($_POST['service_ids'] as $svcId) {
                $svcId = intval($svcId);
                if ($svcId > 0) {
                    db_query( "INSERT IGNORE INTO tblstylist_services(StylistId, ServiceId) VALUES('$newId','$svcId')");
                }
            }
        }
        echo "<script>alert('Stylist has been added.');</script>";
        echo "<script>window.location.href = 'manage-stylists.php'</script>";
    } else {
        echo "<script>alert('Something Went Wrong. Please try again.');</script>";
    }
}
$allServices = db_query( "SELECT ID, ServiceName, Cost FROM tblservices ORDER BY ServiceName");
?>
<!DOCTYPE HTML>
<html>
<head>
<title>MSMS | Add Stylist</title>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href="css/font-awesome.css" rel="stylesheet">
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/modernizr.custom.js"></script>
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
<script> new WOW().init(); </script>
<script src="js/metisMenu.min.js"></script>
<script src="js/custom.js"></script>
<link href="css/custom.css" rel="stylesheet">
</head>
<body class="cbp-spmenu-push">
<div class="main-content">
<?php include_once('includes/sidebar.php');?>
<?php include_once('includes/header.php');?>
<div id="page-wrapper">
<div class="main-page">
<div class="forms">
<h3 class="title1">Add Stylist</h3>
<div class="form-grids row widget-shadow" data-example-id="basic-forms">
<div class="form-title"><h4>Salon Stylist Account:</h4></div>
<div class="form-body">
<form method="post">
<div class="form-group"><label>Name</label><input type="text" class="form-control" name="name" required></div>
<div class="form-group"><label>Username (for login)</label><input type="text" class="form-control" name="username" required></div>
<div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" required></div>
<div class="form-group"><label>Mobile Number</label><input type="text" class="form-control" name="mobilenum" maxlength="15" required></div>
<div class="form-group"><label>Specialty</label><input type="text" class="form-control" name="specialty" placeholder="e.g. Hair styling"></div>
<div class="form-group"><label>Password</label><input type="password" class="form-control" name="password" minlength="6" required></div>
<div class="form-group"><label>Expert services (shown to clients)</label>
<?php while ($srv = db_fetch_array($allServices)) {
    echo '<div class="checkbox"><label><input type="checkbox" name="service_ids[]" value="'.intval($srv['ID']).'"> '
        . htmlspecialchars($srv['ServiceName']) . ' - Rs. ' . intval($srv['Cost']) . '</label></div>';
} ?>
</div>
<button type="submit" name="submit" class="btn btn-default">Add Stylist</button>
<a href="manage-stylists.php" class="btn btn-default">Cancel</a>
</form>
</div>
</div>
</div>
</div>
</div>
<?php include_once('includes/footer.php');?>
</div>
<script src="js/classie.js"></script>
<script>
var menuLeft = document.getElementById('cbp-spmenu-s1'),
showLeftPush = document.getElementById('showLeftPush'),
body = document.body;
showLeftPush.onclick = function() {
classie.toggle(this, 'active');
classie.toggle(body, 'cbp-spmenu-push-toright');
classie.toggle(menuLeft, 'cbp-spmenu-open');
disableOther('showLeftPush');
};
function disableOther(button) {
if (button !== 'showLeftPush') { classie.toggle(showLeftPush, 'disabled'); }
}
</script>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<script src="js/bootstrap.js"></script>
</body>
</html>


