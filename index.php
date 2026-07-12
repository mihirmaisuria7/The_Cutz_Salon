<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Already logged in — redirect by role
if (!empty($_SESSION['bpmsaid'])) {
    header('location:admin/dashboard.php');
    exit;
}
if (!empty($_SESSION['bpmsuid'])) {
    header('location:client/dashboard.php');
    exit;
}
if (!empty($_SESSION['bpmsstid'])) {
    header('location:stylist/dashboard.php');
    exit;
}

$loginError = '';
if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($con, $_POST['username']);
    $pass = md5($_POST['password']);

    // Admin login (same as admin panel)
    $adminQ = mysqli_query($con, "SELECT ID FROM tbladmin WHERE UserName='$user' AND Password='$pass'");
    $admin = mysqli_fetch_array($adminQ);
    if ($admin) {
        $_SESSION['bpmsaid'] = $admin['ID'];
        header('location:admin/dashboard.php');
        exit;
    }

    // Client login
    $clientQ = mysqli_query($con, "SELECT ID, UserName FROM tblcustomers WHERE UserName='$user' AND Password='$pass'");
    $client = mysqli_fetch_array($clientQ);
    if ($client) {
        $_SESSION['bpmsuid'] = $client['ID'];
        $_SESSION['bpmsuname'] = $client['UserName'];
        header('location:client/dashboard.php');
        exit;
    }

    // Stylist login
    $stylistQ = mysqli_query($con, "SELECT ID, UserName FROM tblstylists WHERE UserName='$user' AND Password='$pass'");
    if ($stylistQ) {
        $stylist = mysqli_fetch_array($stylistQ);
        if ($stylist) {
            $_SESSION['bpmsstid'] = $stylist['ID'];
            $_SESSION['bpmsstname'] = $stylist['UserName'];
            header('location:stylist/dashboard.php');
            exit;
        }
    }

    $loginError = 'Invalid username or password.';
}

$about = mysqli_fetch_array(mysqli_query($con, "SELECT PageDescription FROM tblpage WHERE PageType='aboutus'"));
$contact = mysqli_fetch_array(mysqli_query($con, "SELECT PageDescription, Email, MobileNumber, Timing FROM tblpage WHERE PageType='contactus'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MSMS Salon | Home &amp; Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root { --primary:#8b5a2b; --primary-dark:#6d4522; --accent:#d4a574; }
body { font-family:'Segoe UI',system-ui,sans-serif; }
.hero {
  background: linear-gradient(135deg, rgba(109,69,34,.92), rgba(139,90,43,.85)),
    url('https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1200') center/cover;
  color:#fff; padding:5rem 0; min-height:70vh; display:flex; align-items:center;
}
.hero h1 { font-weight:800; font-size:2.75rem; }
.btn-gold { background:var(--accent); border:none; color:#3d2914; font-weight:600; }
.btn-gold:hover { background:#c49563; color:#3d2914; }
.section { padding:4rem 0; }
.service-card { border:1px solid #e8dfd4; border-radius:12px; padding:1.25rem; height:100%; transition:.2s; }
.service-card:hover { box-shadow:0 8px 24px rgba(0,0,0,.08); transform:translateY(-2px); }
.login-box { background:#fff; border-radius:16px; padding:2rem; box-shadow:0 12px 40px rgba(0,0,0,.15); }
.navbar-brand { font-weight:700; color:var(--primary-dark)!important; }
footer { background:#2c2416; color:#ccc; padding:2rem 0; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="bi bi-scissors"></i>  The Cutz Unisex Salon</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="#login">Login</a></li>
        <li class="nav-item"><a class="nav-link btn btn-sm btn-gold ms-2" href="client/register.php">Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <h1>Beauty &amp; Style,<br>Booked Your Way</h1>
        <p class="lead mb-4">Salon Management System — clients book online, stylists manage assigned appointments, admins run the salon.</p>
        <a href="#login" class="btn btn-gold btn-lg me-2">Sign In</a>
        <a href="client/register.php" class="btn btn-outline-light btn-lg">New Client? Register</a>
      </div>
      <div class="col-lg-5 mt-4 mt-lg-0" id="login">
        <div class="login-box">
          <h4 class="mb-1 text-dark">Unified Login</h4>
          <p class="text-muted small mb-3">Admin, client, or stylist — same page, automatic redirect to your dashboard.</p>
          <?php if ($loginError) { ?><div class="alert alert-danger py-2"><?php echo htmlspecialchars($loginError); ?></div><?php } ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" placeholder="Admin, client, or stylist username" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn w-100 text-white" style="background:var(--primary)">Sign In</button>
          </form>
          <hr>
          <p class="small text-muted mb-0 text-center">
            Admin → Admin Panel &nbsp;|&nbsp; Client → Client Dashboard &nbsp;|&nbsp; Stylist → Stylist Panel<br>
            <a href="client/register.php">Create client account</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section bg-light" id="services">
  <div class="container">
    <h2 class="text-center mb-5">Our Services</h2>
    <div class="row g-4">
<?php
$sv = mysqli_query($con, "SELECT ServiceName, Description, Cost FROM tblservices ORDER BY ID LIMIT 6");
while ($s = mysqli_fetch_array($sv)) {
?>
      <div class="col-md-6 col-lg-4">
        <div class="service-card bg-white">
          <h5 style="color:var(--primary-dark)"><?php echo htmlspecialchars($s['ServiceName']); ?></h5>
          <p class="small text-muted"><?php echo htmlspecialchars(substr($s['Description'], 0, 100)); ?>...</p>
          <strong class="text-success">₹<?php echo intval($s['Cost']); ?></strong>
        </div>
      </div>
<?php } ?>
    </div>
    <p class="text-center mt-4"><a href="client/register.php" class="btn btn-gold">Register to book online</a></p>
  </div>
</section>

<section class="section" id="about">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 mx-auto text-center">
        <h2>About Us</h2>
        <p class="text-muted"><?php echo nl2br(htmlspecialchars(trim($about['PageDescription'] ?? 'Welcome to our salon.'))); ?></p>
      </div>
    </div>
  </div>
</section>

<section class="section bg-light" id="contact">
  <div class="container">
    <h2 class="text-center mb-4">Contact Us</h2>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4">
          <p><i class="bi bi-geo-alt text-primary"></i> <?php echo htmlspecialchars(trim($contact['PageDescription'] ?? '')); ?></p>
          <p><i class="bi bi-envelope text-primary"></i> <?php echo htmlspecialchars($contact['Email'] ?? ''); ?></p>
          <p><i class="bi bi-telephone text-primary"></i> <?php echo htmlspecialchars($contact['MobileNumber'] ?? ''); ?></p>
          <p><i class="bi bi-clock text-primary"></i> <?php echo htmlspecialchars($contact['Timing'] ?? '10:30 am to 8:30 pm'); ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="text-center">
  <div class="container">
    <p class="mb-0">&copy; <?php echo date('Y'); ?> The Cutz Unisex Salon Management System</p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
