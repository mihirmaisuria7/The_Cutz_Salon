<?php
include('includes/auth.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
$pageTitle = 'Appointment Detail';
$sid = intval($_SESSION['bpmsstid']);
$aid = intval($_GET['id'] ?? 0);

if ($aid <= 0) {
    header('location:my-appointments.php');
    exit;
}

$row = db_fetch_array(db_query("SELECT * FROM tblappointment WHERE ID='$aid' AND StylistId='$sid' LIMIT 1"));
if (!$row) {
    echo "<script>alert('Appointment not found or not requested for you.'); window.location='my-appointments.php';</script>";
    exit;
}

if (isset($_POST['accept_request']) || isset($_POST['reject_request'])) {
    $response = isset($_POST['accept_request']) ? '1' : '2';
    $note = db_real_escape_string($_POST['response_note'] ?? '');
    if (msms_has_column($con, 'tblappointment', 'StylistStatus')) {
        db_query("UPDATE tblappointment SET StylistStatus='$response', StylistRemark='$note' WHERE ID='$aid' AND StylistId='$sid'");
    } else {
        db_query("UPDATE tblappointment SET StylistRemark='$note' WHERE ID='$aid' AND StylistId='$sid'");
    }
    $msg = $response === '1' ? 'Request accepted.' : 'Request rejected.';
    echo "<script>alert('$msg'); window.location='view-appointment.php?id=$aid';</script>";
    exit;
}

if (isset($_POST['save_note'])) {
    $note = db_real_escape_string($_POST['stylist_remark'] ?? '');
    db_query("UPDATE tblappointment SET StylistRemark='$note' WHERE ID='$aid' AND StylistId='$sid'");
    echo "<script>alert('Service note saved.'); window.location='view-appointment.php?id=$aid';</script>";
    exit;
}

$row = db_fetch_array(db_query("SELECT * FROM tblappointment WHERE ID='$aid' LIMIT 1"));
$canRespond = msms_stylist_can_respond($row, $sid);
$ss = $row['StylistStatus'] ?? '';

include('includes/header.php');
?>
<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="card-panel">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="mb-0">Appointment #<?php echo htmlspecialchars($row['AptNumber']); ?></h3>
        <div><?php echo msms_apt_overall_badge_html($row); ?></div>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-6"><strong>Client</strong><br><?php echo htmlspecialchars($row['Name']); ?></div>
        <div class="col-md-6"><strong>Phone</strong><br><?php echo htmlspecialchars($row['PhoneNumber']); ?></div>
        <div class="col-md-6"><strong>Service requested</strong><br><?php echo htmlspecialchars($row['Services']); ?></div>
        <div class="col-md-6"><strong>Client date &amp; time</strong><br><?php echo htmlspecialchars($row['AptDate']); ?> at <?php echo htmlspecialchars($row['AptTime']); ?></div>
        <div class="col-md-6"><strong>Admin decision</strong><br><?php echo msms_apt_admin_badge_html($row['Status'] ?? ''); ?></div>
        <div class="col-md-6"><strong>Your decision</strong><br><?php echo msms_apt_stylist_badge_html($ss); ?></div>
        <div class="col-12"><strong>Admin remark</strong><br><?php echo htmlspecialchars($row['Remark'] ?: '—'); ?></div>
        <?php if (!empty($row['StylistRemark']) && !$canRespond) { ?>
        <div class="col-12"><strong>Your message / notes</strong><br><?php echo nl2br(htmlspecialchars($row['StylistRemark'])); ?></div>
        <?php } ?>
      </div>

<?php if ($canRespond) { ?>
      <hr>
      <h5>Accept or reject this client request?</h5>
      <p class="text-muted small">The client chose you for this service at their preferred date and time.</p>
      <form method="post" class="mb-3">
        <div class="mb-3">
          <label class="form-label">Message (optional)</label>
          <textarea name="response_note" class="form-control" rows="3" placeholder="Confirm slot or suggest another time"></textarea>
        </div>
        <button type="submit" name="accept_request" class="btn btn-stylist">Accept request</button>
        <button type="submit" name="reject_request" class="btn btn-outline-danger ms-2" onclick="return confirm('Reject this booking request?');">Reject request</button>
      </form>
<?php } elseif ($ss === '1' && ($row['Status'] ?? '') !== '2') { ?>
      <hr>
      <h5>Update service notes</h5>
      <form method="post">
        <textarea name="stylist_remark" class="form-control mb-3" rows="4"><?php echo htmlspecialchars($row['StylistRemark'] ?? ''); ?></textarea>
        <button type="submit" name="save_note" class="btn btn-stylist">Save Notes</button>
      </form>
<?php } elseif ($ss === '2') { ?>
      <div class="alert alert-warning">You rejected this request.</div>
<?php } ?>

      <a href="my-appointments.php" class="btn btn-outline-secondary mt-3">Back</a>
    </div>
  </div>
</div>
<?php include('includes/footer.php'); ?>
