<?php
include('includes/auth.php');
$pageTitle = 'My Profile';
$sid = intval($_SESSION['bpmsstid']);

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mobilenum = mysqli_real_escape_string($con, $_POST['mobilenum']);
    $specialty = mysqli_real_escape_string($con, $_POST['specialty']);
    mysqli_query($con, "UPDATE tblstylists SET StylistName='$name',Email='$email',MobileNumber='$mobilenum',Specialty='$specialty' WHERE ID='$sid'");
    echo "<script>alert('Profile updated successfully.');</script>";
}

if (isset($_POST['changepass'])) {
    $old = md5($_POST['oldpass']);
    $new = md5($_POST['newpass']);
    $chk = mysqli_query($con, "SELECT ID FROM tblstylists WHERE ID='$sid' AND Password='$old'");
    if (mysqli_num_rows($chk) > 0) {
        mysqli_query($con, "UPDATE tblstylists SET Password='$new' WHERE ID='$sid'");
        echo "<script>alert('Password changed.');</script>";
    } else {
        echo "<script>alert('Current password is incorrect.');</script>";
    }
}

$row = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM tblstylists WHERE ID='$sid'"));
include('includes/header.php');
?>
<div class="row g-4">
  <div class="col-lg-7">
    <div class="card-panel">
      <h3>Edit Profile</h3>
      <form method="post">
        <div class="mb-3"><label class="form-label">Username</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($row['UserName']); ?>" disabled></div>
        <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['StylistName']); ?>" required></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['Email']); ?>" required></div>
        <div class="mb-3"><label class="form-label">Mobile</label><input type="text" name="mobilenum" class="form-control" value="<?php echo htmlspecialchars($row['MobileNumber']); ?>" maxlength="15" required></div>
        <div class="mb-3"><label class="form-label">Specialty</label><input type="text" name="specialty" class="form-control" value="<?php echo htmlspecialchars($row['Specialty']); ?>" placeholder="e.g. Hair color, bridal makeup"></div>
        <button type="submit" name="submit" class="btn btn-stylist">Save Profile</button>
      </form>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card-panel">
      <h3>Change Password</h3>
      <form method="post">
        <div class="mb-3"><label class="form-label">Current Password</label><input type="password" name="oldpass" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="newpass" class="form-control" minlength="6" required></div>
        <button type="submit" name="changepass" class="btn btn-outline-secondary">Update Password</button>
      </form>
    </div>
    <div class="card-panel">
      <p class="text-muted small mb-0"><strong>Joined:</strong> <?php echo date('d M Y', strtotime($row['CreationDate'])); ?></p>
    </div>
  </div>
</div>
<?php include('includes/footer.php'); ?>
