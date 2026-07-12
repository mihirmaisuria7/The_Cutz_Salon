<?php
include('includes/auth.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
$pageTitle = 'My Appointments';
$uid = intval($_SESSION['bpmsuid']);
$cust = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM tblcustomers WHERE ID='$uid'"));
$email = mysqli_real_escape_string($con, $cust['Email']);
$phone = $cust['MobileNumber'];

include('includes/header.php');
?>
<div class="card-panel">
  <h3>My Appointments</h3>
  <p class="text-muted small">Your booking is confirmed when both admin and your requested stylist accept (unless rejected by either).</p>
  <div class="table-responsive">
    <table class="table table-bordered table-salon">
      <thead>
        <tr>
          <th>#</th>
          <th>Apt Number</th>
          <th>Service</th>
          <th>Stylist</th>
          <th>Date</th>
          <th>Time</th>
          <th>Admin</th>
          <th>Stylist</th>
          <th>Overall</th>
        </tr>
      </thead>
      <tbody>
<?php
$ret = mysqli_query($con, "SELECT * FROM tblappointment WHERE Email='$email' OR PhoneNumber='$phone' ORDER BY ID DESC");
$cnt = 1;
while ($row = mysqli_fetch_array($ret)) {
    $styName = msms_stylist_name($con, $row['StylistId'] ?? 0);
    $hasStylist = !empty($row['StylistId']);
    $ss = $row['StylistStatus'] ?? '';
?>
        <tr>
          <td><?php echo $cnt++; ?></td>
          <td><?php echo htmlspecialchars($row['AptNumber']); ?></td>
          <td><?php echo htmlspecialchars($row['Services']); ?></td>
          <td><?php echo htmlspecialchars($styName); ?></td>
          <td><?php echo htmlspecialchars($row['AptDate']); ?></td>
          <td><?php echo htmlspecialchars($row['AptTime']); ?></td>
          <td><?php echo msms_apt_admin_badge_html($row['Status'] ?? ''); ?></td>
          <td><?php echo $hasStylist ? msms_apt_stylist_badge_html($ss) : '<span class="text-muted">—</span>'; ?></td>
          <td><?php echo msms_apt_overall_badge_html($row); ?></td>
        </tr>
<?php }
if ($cnt === 1) {
    echo '<tr><td colspan="9" class="text-center">No appointments found.</td></tr>';
}
?>
      </tbody>
    </table>
  </div>
  <a href="book-appointment.php" class="btn btn-salon">Book New</a>
</div>
<?php include('includes/footer.php'); ?>
