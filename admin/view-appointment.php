<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
include_once('includes/auth_check.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
if(isset($_POST['submit']))
  {
    
    $cid = intval($_GET['viewid'] ?? 0);
      $remark=db_real_escape_string( $_POST['remark']);
      $status=db_real_escape_string( $_POST['status']);
     
    
     
   $query=db_query( "update  tblappointment set Remark='$remark',Status='$status' where ID='$cid'");
    if ($query) {
    
    echo '<script>alert("All remark has been updated")</script>';
  }
  else
    {
      echo '<script>alert("Something Went Wrong. Please try again.")</script>';
    }

  
}

if (isset($_POST['assign_stylist'])) {
    $cid = intval($_GET['viewid'] ?? 0);
    $stylistId = intval($_POST['stylist_id']);
    if ($stylistId > 0) {
        db_query( "UPDATE tblappointment SET StylistId='$stylistId' WHERE ID='$cid'");
        echo '<script>alert("Stylist assigned successfully.")</script>';
    } else {
        db_query( "UPDATE tblappointment SET StylistId=NULL WHERE ID='$cid'");
        echo '<script>alert("Stylist assignment cleared.")</script>';
    }
}
  

  ?>
<!DOCTYPE HTML>
<html>
<head>
<title>MSMS || View Appointment</title>

<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!-- font CSS -->
<!-- font-awesome icons -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- //font-awesome icons -->
 <!-- js-->
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/modernizr.custom.js"></script>
<!--webfonts-->
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
<!--//webfonts--> 
<!--animate-->
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
	<script>
		 new WOW().init();
	</script>
<!--//end-animate-->
<!-- Metis Menu -->
<script src="js/metisMenu.min.js"></script>
<script src="js/custom.js"></script>
<link href="css/custom.css" rel="stylesheet">
<!--//Metis Menu -->
</head> 
<body class="cbp-spmenu-push">
	<div class="main-content">
		<!--left-fixed -navigation-->
		 <?php include_once('includes/sidebar.php');?>
		<!--left-fixed -navigation-->
		<!-- header-starts -->
		 <?php include_once('includes/header.php');?>
		<!-- //header-ends -->
		<!-- main content start-->
		<div id="page-wrapper">
			<div class="main-page">
				<div class="tables">
					<h3 class="title1">View Appointment</h3>
					
					
				
					<div class="table-responsive bs-example widget-shadow">
						<p style="font-size:16px; color:red" align="center"> <?php if (!empty($msg)) {
    echo $msg;
  }  ?> </p>
						<h4>View Appointment:</h4>
						<?php
$cid = intval($_GET['viewid'] ?? 0);
$ret=db_query("select * from tblappointment where ID='$cid'");
$cnt=1;
while ($row=db_fetch_array($ret)) {

?>
						<table class="table table-bordered">
							<tr>
    <th>Appointment Number</th>
    <td><?php  echo $row['AptNumber'];?></td>
  </tr>
  <tr>
<th>Name</th>
    <td><?php  echo $row['Name'];?></td>
  </tr>

<tr>
    <th>Email</th>
    <td><?php  echo $row['Email'];?></td>
  </tr>
   <tr>
    <th>Mobile Number</th>
    <td><?php  echo $row['PhoneNumber'];?></td>
  </tr>
   <tr>
    <th>Appointment Date</th>
    <td><?php  echo $row['AptDate'];?></td>
  </tr>
 
<tr>
    <th>Appointment Time</th>
    <td><?php  echo $row['AptTime'];?></td>
  </tr>
  
  <tr>
    <th>Services</th>
    <td><?php  echo $row['Services'];?></td>
  </tr>
  <tr>
    <th>Apply Date</th>
    <td><?php  echo $row['ApplyDate'];?></td>
  </tr>
  

  <tr>
    <th>Status</th>
    <td> <?php  
if($row['Status']=="1")
{
  echo "Selected";
}

if($row['Status']=="2")
{
  echo "Rejected";
}

     ;?></td>
  </tr>
<?php
$assignedName = '—';
if (!empty($row['StylistId'])) {
    $sid = intval($row['StylistId']);
    $stRow = db_fetch_array(db_query( "SELECT StylistName FROM tblstylists WHERE ID='$sid'"));
    if ($stRow) { $assignedName = $stRow['StylistName']; }
}
?>
  <tr>
    <th>Client requested stylist</th>
    <td><?php echo htmlspecialchars($assignedName); ?></td>
  </tr>
  <tr>
    <th>Stylist response</th>
    <td><?php echo !empty($row['StylistId']) ? msms_apt_stylist_status_text($row['StylistStatus'] ?? '') : '—'; ?></td>
  </tr>
  <tr>
    <th>Overall booking</th>
    <td><?php echo msms_apt_overall_status_text($row); ?></td>
  </tr>
<?php if (!empty($row['StylistRemark'])) { ?>
  <tr>
    <th>Stylist message / notes</th>
    <td><?php echo nl2br(htmlspecialchars($row['StylistRemark'])); ?></td>
  </tr>
<?php } ?>
						</table>
						<div class="table-responsive bs-example widget-shadow" style="margin-top:1rem;">
						<h5>Change stylist (optional)</h5>
						<p class="text-muted small">Client already chose a stylist when booking. Override only if needed.</p>
						<form method="post">
						<select name="stylist_id" class="form-control wd-450" style="max-width:320px;display:inline-block;">
						<option value="0">— None —</option>
<?php
$stylists = db_query( "SELECT ID, StylistName FROM tblstylists ORDER BY StylistName");
while ($st = db_fetch_array($stylists)) {
    $sel = (intval($row['StylistId']) === intval($st['ID'])) ? 'selected' : '';
    echo '<option value="'.intval($st['ID']).'" '.$sel.'>'.htmlspecialchars($st['StylistName']).'</option>';
}
?>
						</select>
						<button type="submit" name="assign_stylist" class="btn btn-default">Assign</button>
						</form>
						</div>
						<table class="table table-bordered">
							<?php if (msms_admin_can_respond($row)) { ?>


<form name="submit" method="post" enctype="multipart/form-data"> 

<tr>
    <th>Admin remark :</th>
    <td>
    <textarea name="remark" placeholder="Message to client" rows="6" cols="14" class="form-control wd-450" required="true"></textarea></td>
   </tr>

  <tr>
    <th>Accept or reject :</th>
    <td>
   <select name="status" class="form-control wd-450" required="true" >
     <option value="1">Accept request</option>
     <option value="2">Reject request</option>
   </select></td>
  </tr>

  <tr align="center">
    <td colspan="2"><button type="submit" name="submit" class="btn btn-az-primary pd-x-20">Submit decision</button></td>
  </tr>
  </form>
<?php } else { ?>
						</table>
						<table class="table table-bordered">
							<tr>
    <th>Remark</th>
    <td><?php echo $row['Remark']; ?></td>
  </tr>


<tr>
<th>Remark date</th>
<td><?php echo $row['RemarkDate']; ?>  </td></tr>

						</table>
						<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<!--footer-->
		
        <!--//footer-->
	</div>
	<!-- Classie -->
		<script src="js/classie.js"></script>
		<script>
			var menuLeft = document.getElementById( 'cbp-spmenu-s1' ),
				showLeftPush = document.getElementById( 'showLeftPush' ),
				body = document.body;
				
			showLeftPush.onclick = function() {
				classie.toggle( this, 'active' );
				classie.toggle( body, 'cbp-spmenu-push-toright' );
				classie.toggle( menuLeft, 'cbp-spmenu-open' );
				disableOther( 'showLeftPush' );
			};
			
			function disableOther( button ) {
				if( button !== 'showLeftPush' ) {
					classie.toggle( showLeftPush, 'disabled' );
				}
			}
		</script>
	<!--scrolling js-->
	<script src="js/jquery.nicescroll.js"></script>
	<script src="js/scripts.js"></script>
	<!--//scrolling js-->
	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.js"> </script>
</body>
</html>

