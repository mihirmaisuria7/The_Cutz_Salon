<?php
include('includes/auth.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
$pageTitle = 'Services';
include('includes/header.php');
?>
<div class="card-panel">
  <h3>Our Services &amp; Prices</h3>
  <p class="text-muted">See which stylists specialize in each service, then book your preferred expert.</p>
  <div class="row g-3">
<?php
$ret = db_query("SELECT * FROM tblservices ORDER BY ServiceName");
while ($row = db_fetch_array($ret)) {
    $experts = msms_stylists_for_service($con, $row['ServiceName']);
?>
    <div class="col-md-6 col-lg-4">
      <div class="border rounded p-3 h-100" style="border-color:var(--border)!important">
        <h5 class="text-primary"><?php echo htmlspecialchars($row['ServiceName']); ?></h5>
        <p class="small text-muted mb-2"><?php echo htmlspecialchars(substr($row['Description'], 0, 120)); ?>...</p>
        <strong class="text-success d-block mb-2">Rs. <?php echo intval($row['Cost']); ?></strong>
        <?php if (count($experts) > 0) { ?>
        <p class="small fw-semibold mb-1"><i class="bi bi-person-badge"></i> Expert stylists:</p>
        <ul class="small mb-2 ps-3">
          <?php foreach ($experts as $ex) { ?>
          <li><?php echo htmlspecialchars($ex['StylistName']); ?></li>
          <?php } ?>
        </ul>
        <?php } else { ?>
        <p class="small text-muted mb-2">Stylist list coming soon</p>
        <?php } ?>
        <a href="book-appointment.php" class="btn btn-salon btn-sm">Book this service</a>
      </div>
    </div>
<?php } ?>
  </div>
  <a href="stylists.php" class="btn btn-outline-secondary mt-4 me-2">All stylists</a>
  <a href="book-appointment.php" class="btn btn-salon mt-4">Book appointment</a>
</div>
<?php include('includes/footer.php'); ?>
