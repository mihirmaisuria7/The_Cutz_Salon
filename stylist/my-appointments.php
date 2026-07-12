<?php
include('includes/auth.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
$pageTitle = 'My Schedule';
$sid = intval($_SESSION['bpmsstid']);

include('includes/header.php');
?>
<div class="card-panel">
  <h3>Client requests for you</h3>
  <p class="text-muted small">Clients book you for a service, date, and time. Accept or reject each request; admin also reviews the same booking.</p>
  <div class="table-responsive">
    <table class="table table-bordered table-stylist">
      <thead>
        <tr>
          <th>#</th>
          <th>Apt Number</th>
          <th>Client</th>
          <th>Service</th>
          <th>Date</th>
          <th>Time</th>
          <th>Admin</th>
          <th>Your response</th>
          <th>Overall</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
<?php
$ret = db_query("SELECT * FROM tblappointment WHERE StylistId='$sid' ORDER BY ID DESC");
$cnt = 1;
while ($row = db_fetch_array($ret)) {
    $ss = $row['StylistStatus'] ?? '';
?>
        <tr>
          <td><?php echo $cnt++; ?></td>
          <td><?php echo htmlspecialchars($row['AptNumber']); ?></td>
          <td><?php echo htmlspecialchars($row['Name']); ?></td>
          <td><?php echo htmlspecialchars($row['Services']); ?></td>
          <td><?php echo htmlspecialchars($row['AptDate']); ?></td>
          <td><?php echo htmlspecialchars($row['AptTime']); ?></td>
          <td><?php echo msms_apt_admin_badge_html($row['Status'] ?? ''); ?></td>
          <td><?php echo msms_apt_stylist_badge_html($ss); ?></td>
          <td><?php echo msms_apt_overall_badge_html($row); ?></td>
          <td><a href="view-appointment.php?id=<?php echo intval($row['ID']); ?>" class="btn btn-sm btn-stylist"><?php echo msms_stylist_can_respond($row, $sid) ? 'Respond' : 'View'; ?></a></td>
        </tr>
<?php }
if ($cnt === 1) {
    echo '<tr><td colspan="10" class="text-center">No client requests yet.</td></tr>';
}
?>
      </tbody>
    </table>
  </div>
</div>
<?php include('includes/footer.php'); ?>
