<?php
include('includes/auth.php');
$pageTitle = 'My Profile';
$uid = intval($_SESSION['bpmsuid']);

if (isset($_POST['submit'])) {
    $name = db_real_escape_string($_POST['name'] ?? '');
    $email = db_real_escape_string($_POST['email'] ?? '');
    $mobilenum = db_real_escape_string($_POST['mobilenum'] ?? '');
    $gender = db_real_escape_string($_POST['gender'] ?? '');
    $details = db_real_escape_string($_POST['details'] ?? '');
    db_query("UPDATE tblcustomers SET Name='$name',Email='$email',MobileNumber='$mobilenum',Gender='$gender',Details='$details' WHERE ID='$uid'");
    echo "<script>alert('Profile updated successfully.');</script>";
}

if (isset($_POST['changepass'])) {
    $old = md5($_POST['oldpass'] ?? '');
    $new = md5($_POST['newpass'] ?? '');
    $chk = db_query("SELECT * FROM tblcustomers WHERE ID='$uid' AND Password='$old' LIMIT 1");
    if (db_num_rows($chk) > 0) {
        db_query("UPDATE tblcustomers SET Password='$new' WHERE ID='$uid'");
        echo "<script>alert('Password changed.');</script>";
    } else {
        echo "<script>alert('Current password is incorrect.');</script>";
    }
}

$row = db_fetch_array(db_query("SELECT * FROM tblcustomers WHERE ID='$uid' LIMIT 1"));
include('includes/header.php');
?>
<div class="row g-4">
  <div class="col-lg-7">
    <div class="card-panel">
      <h3>Edit Profile</h3>
      <form method="post">
        <div class="mb-3"><label class="form-label">Username</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($row['UserName']); ?>" disabled></div>
        <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['Name']); ?>" required></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['Email']); ?>" required></div>
        <div class="mb-3"><label class="form-label">Mobile</label><input type="text" name="mobilenum" class="form-control" value="<?php echo htmlspecialchars($row['MobileNumber']); ?>" maxlength="10" required></div>
        <div class="mb-3"><label class="form-label">Gender</label>
          <select name="gender" class="form-select" required>
            <?php foreach (['Male','Female','Transgender'] as $g) {
              $sel = ($row['Gender']==$g)?'selected':'';
              echo "<option value=\"$g\" $sel>$g</option>";
            } ?>
          </select>
        </div>
        <div class="mb-3"><label class="form-label">Details</label><textarea name="details" class="form-control" rows="3"><?php echo htmlspecialchars($row['Details']); ?></textarea></div>
        <button type="submit" name="submit" class="btn btn-salon">Save Profile</button>
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
      <p class="text-muted small mb-0"><strong>Member since:</strong> <?php echo date('d M Y', strtotime($row['CreationDate'])); ?></p>
    </div>
  </div>
</div>
<?php include('includes/footer.php'); ?>
