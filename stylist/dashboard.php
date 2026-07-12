<?php
include('includes/auth.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
$pageTitle = 'Dashboard';
$sid = intval($_SESSION['bpmsstid']);

$sty = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM tblstylists WHERE ID='$sid'"));
$today = date('Y-m-d');

$totalAssigned = mysqli_num_rows(mysqli_query($con, "SELECT ID FROM tblappointment WHERE StylistId='$sid'"));
$pendingResponse = mysqli_num_rows(mysqli_query($con, "SELECT ID FROM tblappointment WHERE StylistId='$sid' AND (StylistStatus='' OR StylistStatus IS NULL) AND Status!='2'"));
$todayCount = mysqli_num_rows(mysqli_query($con, "SELECT ID FROM tblappointment WHERE StylistId='$sid' AND AptDate='$today'"));
$confirmed = mysqli_num_rows(mysqli_query($con, "SELECT ID FROM tblappointment WHERE StylistId='$sid' AND Status='1' AND StylistStatus='1'"));

include('includes/header.php');
?>
<div class="row mb-4 align-items-center">
  <div class="col-auto"><div class="profile-avatar"><?php echo strtoupper(substr($sty['StylistName'], 0, 1)); ?></div></div>
  <div class="col">
    <h1 class="h3 mb-1">Welcome, <?php echo htmlspecialchars($sty['StylistName']); ?>!</h1>
    <p class="text-muted mb-0">Review client booking requests and confirm your availability.</p>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?php echo $pendingResponse; ?></div><div class="lbl">Awaiting your reply</div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?php echo $totalAssigned; ?></div><div class="lbl">Total requests</div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?php echo $todayCount; ?></div><div class="lbl">Today</div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?php echo $confirmed; ?></div><div class="lbl">Fully confirmed</div></div></div>
</div>

<div class="card-panel">
  <h3>Recent client requests</h3>
  <div class="table-responsive">
    <table class="table table-hover table-stylist">
      <thead><tr><th>#</th><th>Client</th><th>Service</th><th>Date</th><th>Time</th><th>Overall</th><th></th></tr></thead>
      <tbody>
<?php
$ret = mysqli_query($con, "SELECT * FROM tblappointment WHERE StylistId='$sid' ORDER BY ID DESC LIMIT 8");
$cnt = 1;
while ($row = mysqli_fetch_array($ret)) {
?>
        <tr>
          <td><?php echo $cnt++; ?></td>
          <td><?php echo htmlspecialchars($row['Name']); ?></td>
          <td><?php echo htmlspecialchars($row['Services']); ?></td>
          <td><?php echo htmlspecialchars($row['AptDate']); ?></td>
          <td><?php echo htmlspecialchars($row['AptTime']); ?></td>
          <td><?php echo msms_apt_overall_badge_html($row); ?></td>
          <td><a href="view-appointment.php?id=<?php echo intval($row['ID']); ?>" class="btn btn-sm btn-stylist"><?php echo msms_stylist_can_respond($row, $sid) ? 'Respond' : 'View'; ?></a></td>
        </tr>
<?php }
if ($cnt === 1) {
    echo '<tr><td colspan="7" class="text-center text-muted">No requests yet. Clients choose you when booking.</td></tr>';
}
?>
      </tbody>
    </table>
  </div>
  <a href="my-appointments.php" class="btn btn-outline-secondary btn-sm">View all</a>
</div>
<?php include('includes/footer.php'); ?>
