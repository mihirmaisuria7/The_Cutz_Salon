<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
include_once('includes/auth_check.php');

if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    mysqli_query($con, "UPDATE tblappointment SET StylistId=NULL WHERE StylistId='$id'");
    mysqli_query($con, "DELETE FROM tblstylists WHERE ID='$id'");
    echo "<script>alert('Stylist deleted.'); window.location='manage-stylists.php';</script>";
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>MSMS | Manage Stylists</title>
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href="css/font-awesome.css" rel="stylesheet">
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/modernizr.custom.js"></script>
<link href="css/custom.css" rel="stylesheet">
</head>
<body class="cbp-spmenu-push">
<div class="main-content">
<?php include_once('includes/sidebar.php');?>
<?php include_once('includes/header.php');?>
<div id="page-wrapper">
<div class="main-page">
<div class="tables">
<h3 class="title1">Manage Stylists</h3>
<p><a href="add-stylist.php" class="btn btn-primary btn-sm">Add New Stylist</a></p>
<div class="table-responsive bs-example widget-shadow">
<table class="table table-bordered">
<thead><tr><th>#</th><th>Name</th><th>Username</th><th>Email</th><th>Mobile</th><th>Specialty</th><th>Joined</th><th>Action</th></tr></thead>
<tbody>
<?php
$ret = mysqli_query($con, "SELECT * FROM tblstylists ORDER BY ID DESC");
$cnt = 1;
while ($row = mysqli_fetch_array($ret)) {
?>
<tr>
<td><?php echo $cnt++; ?></td>
<td><?php echo htmlspecialchars($row['StylistName']); ?></td>
<td><?php echo htmlspecialchars($row['UserName']); ?></td>
<td><?php echo htmlspecialchars($row['Email']); ?></td>
<td><?php echo htmlspecialchars($row['MobileNumber']); ?></td>
<td><?php echo htmlspecialchars($row['Specialty']); ?></td>
<td><?php echo date('d-m-Y', strtotime($row['CreationDate'])); ?></td>
<td>
<a href="edit-stylist-services.php?id=<?php echo intval($row['ID']); ?>">Services</a> |
<a href="manage-stylists.php?del=<?php echo intval($row['ID']); ?>" onclick="return confirm('Delete this stylist?');">Delete</a>
</td>
</tr>
<?php }
if ($cnt === 1) {
    echo '<tr><td colspan="8" align="center">No stylists yet. <a href="add-stylist.php">Add one</a>.</td></tr>';
}
?>
</tbody>
</table>
</div>
</div>
</div>
</div>
<?php include_once('includes/footer.php');?>
</div>
<script src="js/bootstrap.js"></script>
</body>
</html>
