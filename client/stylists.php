<?php
include('includes/auth.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
$pageTitle = 'Our Stylists';

include('includes/header.php');
?>
<div class="card-panel mb-4">
  <h3>Meet Our Stylists</h3>
  <p class="text-muted">Each stylist specializes in specific services. When booking, choose your preferred expert for that service.</p>
</div>
<div class="row g-4">
<?php
$stylists = mysqli_query($con, "SELECT * FROM tblstylists ORDER BY StylistName");
if ($stylists && mysqli_num_rows($stylists) > 0) {
    while ($sty = mysqli_fetch_array($stylists)) {
        $services = msms_services_for_stylist($con, $sty['ID']);
?>
  <div class="col-md-6 col-lg-4">
    <div class="border rounded p-4 h-100 bg-white" style="border-color:var(--border)!important">
      <div class="d-flex align-items-center mb-3">
        <div class="profile-avatar me-3" style="width:56px;height:56px;font-size:1.5rem"><?php echo strtoupper(substr($sty['StylistName'], 0, 1)); ?></div>
        <div>
          <h5 class="mb-0"><?php echo htmlspecialchars($sty['StylistName']); ?></h5>
          <small class="text-muted"><?php echo htmlspecialchars($sty['Specialty'] ?: 'Salon professional'); ?></small>
        </div>
      </div>
      <?php if (!empty($services)) { ?>
      <p class="small fw-semibold mb-1">Expert in:</p>
      <ul class="small mb-3 ps-3">
        <?php foreach ($services as $sv) { ?>
        <li><?php echo htmlspecialchars($sv['ServiceName']); ?> <span class="text-success">₹<?php echo intval($sv['Cost']); ?></span></li>
        <?php } ?>
      </ul>
      <?php } else { ?>
      <p class="small text-muted">All salon services</p>
      <?php } ?>
      <a href="book-appointment.php?stylist=<?php echo intval($sty['ID']); ?>" class="btn btn-salon btn-sm">Book with this stylist</a>
    </div>
  </div>
<?php
    }
} else {
    echo '<div class="col-12"><p class="text-muted">Stylist profiles will appear here once added by admin.</p></div>';
}
?>
</div>
<a href="services.php" class="btn btn-outline-secondary mt-4">View all services</a>
<?php include('includes/footer.php'); ?>
