<?php
session_start();
error_reporting(0);
include(__DIR__ . '/../includes/dbconnection.php');

if (strlen($_SESSION['bpmsuid'] ?? '') > 0) {
    header('location:dashboard.php');
    exit;
}

$msg = '';
if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mobilenum = mysqli_real_escape_string($con, $_POST['mobilenum']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = md5($_POST['password']);
    $details = mysqli_real_escape_string($con, $_POST['details'] ?? '');

    $chk = mysqli_query($con, "SELECT ID FROM tblcustomers WHERE UserName='$username' OR Email='$email' LIMIT 1");
    if (mysqli_num_rows($chk) > 0) {
        $msg = 'danger|Username or email already exists.';
    } else {
        $q = mysqli_query($con, "INSERT INTO tblcustomers(Name,Email,MobileNumber,Gender,Details,UserName,Password) VALUES('$name','$email','$mobilenum','$gender','$details','$username','$password')");
        if ($q) {
            $newid = mysqli_insert_id($con);
            $_SESSION['bpmsuid'] = $newid;
            $_SESSION['bpmsuname'] = $username;
            header('location:dashboard.php');
            exit;
        }
        $msg = 'danger|Registration failed. Run SQL File/client_auth_update.sql first if columns are missing.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register | MSMS Salon</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center" style="min-height:100vh;background:var(--bg)">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card-panel">
        <h2 class="mb-4">Create Client Account</h2>
        <?php if ($msg) { list($t,$m)=explode('|',$msg); ?>
        <div class="alert alert-<?php echo $t; ?>"><?php echo htmlspecialchars($m); ?></div>
        <?php } ?>
        <form method="post">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Mobile</label><input type="text" name="mobilenum" class="form-control" maxlength="10" pattern="[0-9]+" required></div>
            <div class="col-md-6"><label class="form-label">Gender</label>
              <select name="gender" class="form-select" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Transgender">Transgender</option>
              </select>
            </div>
            <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" minlength="6" required></div>
            <div class="col-12"><label class="form-label">Notes (optional)</label><textarea name="details" class="form-control" rows="2"></textarea></div>
          </div>
          <button type="submit" name="register" class="btn btn-salon mt-4">Register</button>
          <a href="../index.php" class="btn btn-outline-secondary mt-4 ms-2">Back to Login</a>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
