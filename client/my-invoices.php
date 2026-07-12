<?php
include('includes/auth.php');
$pageTitle = 'My Invoices';
$uid = intval($_SESSION['bpmsuid']);
include('includes/header.php');
?>
<div class="card-panel">
  <h3>My Invoices</h3>
  <div class="table-responsive">
    <table class="table table-bordered table-salon">
      <thead><tr><th>#</th><th>Billing ID</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
<?php
$ret = mysqli_query($con, "SELECT DISTINCT BillingId, PostingDate FROM tblinvoice WHERE Userid='$uid' ORDER BY PostingDate DESC");
$cnt = 1;
while ($row = mysqli_fetch_array($ret)) {
?>
        <tr>
          <td><?php echo $cnt++; ?></td>
          <td><?php echo htmlspecialchars($row['BillingId']); ?></td>
          <td><?php echo date('d-m-Y', strtotime($row['PostingDate'])); ?></td>
          <td><a href="view-invoice.php?invoiceid=<?php echo intval($row['BillingId']); ?>" class="btn btn-sm btn-salon">View</a></td>
        </tr>
<?php } if ($cnt === 1) { echo '<tr><td colspan="4" class="text-center text-muted">No invoices yet.</td></tr>'; } ?>
      </tbody>
    </table>
  </div>
</div>
<?php include('includes/footer.php'); ?>
