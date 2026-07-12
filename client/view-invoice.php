<?php
include('includes/auth.php');
$pageTitle = 'Invoice';
$uid = intval($_SESSION['bpmsuid']);
$invid = intval($_GET['invoiceid']);

$chk = db_query("SELECT * FROM tblinvoice WHERE BillingId='$invid' AND Userid='$uid' LIMIT 1");
if (db_num_rows($chk) == 0) {
    echo "<script>alert('Invalid invoice.'); window.location='my-invoices.php';</script>";
    exit;
}

$ret = db_query("SELECT tblinvoice.PostingDate, tblcustomers.Name, tblcustomers.Email, tblcustomers.MobileNumber, tblcustomers.Gender
  FROM tblinvoice JOIN tblcustomers ON tblcustomers.ID=tblinvoice.Userid WHERE tblinvoice.BillingId='$invid' AND tblinvoice.Userid='$uid' LIMIT 1");
$row = db_fetch_array($ret);

include('includes/header.php');
?>
<div class="card-panel" id="printarea">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Invoice #<?php echo $invid; ?></h3>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">Print</button>
  </div>
  <table class="table table-bordered">
    <tr><th colspan="4">Customer Details</th></tr>
    <tr><th>Name</th><td><?php echo htmlspecialchars($row['Name']); ?></td><th>Contact</th><td><?php echo htmlspecialchars($row['MobileNumber']); ?></td></tr>
    <tr><th>Email</th><td><?php echo htmlspecialchars($row['Email']); ?></td><th>Gender</th><td><?php echo htmlspecialchars($row['Gender']); ?></td></tr>
    <tr><th>Invoice Date</th><td colspan="3"><?php echo date('d-m-Y', strtotime($row['PostingDate'])); ?></td></tr>
  </table>
  <h5 class="mt-4">Services</h5>
  <table class="table table-bordered table-salon">
    <thead><tr><th>#</th><th>Service</th><th>Cost (Rs.)</th></tr></thead>
    <tbody>
<?php
$total = 0;
$cnt = 1;
$items = db_query("SELECT tblservices.ServiceName, tblservices.Cost FROM tblinvoice JOIN tblservices ON tblservices.ID=tblinvoice.ServiceId WHERE tblinvoice.BillingId='$invid' AND tblinvoice.Userid='$uid'");
while ($it = db_fetch_array($items)) {
    $total += intval($it['Cost']);
?>
      <tr><td><?php echo $cnt++; ?></td><td><?php echo htmlspecialchars($it['ServiceName']); ?></td><td><?php echo intval($it['Cost']); ?></td></tr>
<?php } ?>
      <tr><th colspan="2" class="text-end">Grand Total</th><th>Rs. <?php echo $total; ?></th></tr>
    </tbody>
  </table>
</div>
<a href="my-invoices.php" class="btn btn-outline-secondary">Back</a>
<?php include('includes/footer.php'); ?>
