<?php
include('includes/auth.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
$pageTitle = 'Dashboard';
$uid = intval($_SESSION['bpmsuid']);

$cust = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM tblcustomers WHERE ID='$uid'"));
$email = mysqli_real_escape_string($con, $cust['Email']);
$phone = $cust['MobileNumber'];

$aptTotal = mysqli_num_rows(mysqli_query($con, "SELECT ID FROM tblappointment WHERE Email='$email' OR PhoneNumber='$phone'"));
$aptPending = mysqli_num_rows(mysqli_query($con, "SELECT ID FROM tblappointment WHERE (Email='$email' OR PhoneNumber='$phone') AND (Status='' OR Status IS NULL)"));
$aptAccepted = mysqli_num_rows(mysqli_query($con, "SELECT ID FROM tblappointment WHERE (Email='$email' OR PhoneNumber='$phone') AND Status='1'"));
$invCount = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT BillingId FROM tblinvoice WHERE Userid='$uid'"));

include('includes/header.php');
?>
<div class="row mb-4 align-items-center">
  <div class="col-auto"><div class="profile-avatar"><?php echo strtoupper(substr($cust['Name'],0,1)); ?></div></div>
  <div class="col">
    <h1 class="h3 mb-1">Welcome, <?php echo htmlspecialchars($cust['Name']); ?>!</h1>
    <p class="text-muted mb-0">Manage your salon visits from your client dashboard.</p>
  </div>
  <div class="col-auto"><a href="book-appointment.php" class="btn btn-salon"><i class="bi bi-calendar-plus"></i> Book Appointment</a></div>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?php echo $aptTotal; ?></div><div class="lbl">Total Appointments</div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?php echo $aptPending; ?></div><div class="lbl">Pending</div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?php echo $aptAccepted; ?></div><div class="lbl">Confirmed</div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?php echo $invCount; ?></div><div class="lbl">Invoices</div></div></div>
</div>

<div class="card-panel">
  <h3>Recent Appointments</h3>
  <div class="table-responsive">
    <table class="table table-hover table-salon">
      <thead><tr><th>#</th><th>Apt No.</th><th>Service</th><th>Stylist</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
      <tbody>
<?php
$ret = mysqli_query($con, "SELECT * FROM tblappointment WHERE Email='$email' OR PhoneNumber='$phone' ORDER BY ID DESC LIMIT 5");
$cnt = 1;
while ($row = mysqli_fetch_array($ret)) {
?>
        <tr>
          <td><?php echo $cnt++; ?></td>
          <td><?php echo htmlspecialchars($row['AptNumber']); ?></td>
          <td><?php echo htmlspecialchars($row['Services']); ?></td>
          <td><?php echo htmlspecialchars(msms_stylist_name($con, $row['StylistId'] ?? 0)); ?></td>
          <td><?php echo htmlspecialchars($row['AptDate']); ?></td>
          <td><?php echo htmlspecialchars($row['AptTime']); ?></td>
          <td><?php echo msms_apt_overall_badge_html($row); ?></td>
        </tr>
<?php } if ($cnt === 1) { echo '<tr><td colspan="7" class="text-center text-muted">No appointments yet. <a href="book-appointment.php">Book one now</a>.</td></tr>'; } ?>
      </tbody>
    </table>
  </div>
  <a href="my-appointments.php" class="btn btn-outline-secondary btn-sm">View all</a>
</div>
<?php include('includes/footer.php'); ?>
